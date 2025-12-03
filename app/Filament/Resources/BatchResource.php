<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BatchResource\Pages;
use App\Filament\Resources\BatchResource\RelationManagers;
use App\Models\Batch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->maxLength(1000)
                        ->rows(3),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->placeholder('Select batch start date')
                        ->helperText('Choose when this batch will start')
                        ->displayFormat('d/m/Y')
                        ->native(false),
                    Forms\Components\Select::make('teacher_id')
                        ->label('Assigned Teacher')
                        ->options(fn () => \App\Models\User::where('role', 'teacher')->pluck('name', 'id')->toArray())
                        ->placeholder('Select a teacher')
                        ->helperText('Choose a teacher to assign to this batch')
                        ->visible(fn () => auth()->user()->isAdmin())
                        ->columnSpanFull(),
                    Forms\Components\Placeholder::make('courses_help')
                        ->label('')
                        ->content('📚 Select which courses students in this batch can access:')
                        ->columnSpanFull(),
                    Forms\Components\Section::make('Course Assignments')
                        ->description('Toggle on/off the courses that students in this batch should have access to')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema(function () {
                                    // Get all courses from database
                                    $courses = \App\Models\Course::all();
                                    
                                    return $courses->map(function ($course) {
                                        return Forms\Components\Toggle::make("course_{$course->id}")
                                            ->label($course->name)
                                            ->default(fn ($record) => $record ? $record->courses->contains($course->id) : false)
                                            ->extraAttributes(['class' => 'custom-green-toggle'])
                                            ->live();
                                    })->toArray();
                                })
                        ])
                        ->columnSpanFull(),
                    Forms\Components\Section::make('Subject Assignments')
                        ->description('Toggle on/off the subjects that students in this batch should have access to.')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema(function ($record, $get) {
                                    // Get selected course IDs from toggles
                                    $selectedCourseIds = [];
                                    $courses = \App\Models\Course::all();
                                    foreach ($courses as $course) {
                                        if ($get("course_{$course->id}")) {
                                            $selectedCourseIds[] = $course->id;
                                        }
                                    }
                                    
                                    // If no courses are selected, show no subjects
                                    if (empty($selectedCourseIds)) {
                                        return [
                                            Forms\Components\Placeholder::make('no_courses_selected')
                                                ->label('')
                                                ->content('Please select at least one course to see available subjects.')
                                                ->columnSpanFull()
                                        ];
                                    }
                                    
                                    // Get subjects based on selected courses and user role
                                    if (auth()->user()->isTeacher()) {
                                        // For teachers: only show their assigned subjects that belong to selected courses
                                        $subjects = auth()->user()->subjects()->whereIn('course_id', $selectedCourseIds)->get();
                                    } else {
                                        // For admins: show subjects that belong to selected courses
                                        $subjects = \App\Models\Subject::whereIn('course_id', $selectedCourseIds)->get();
                                    }
                                    
                                    if ($subjects->isEmpty()) {
                                        return [
                                            Forms\Components\Placeholder::make('no_subjects_available')
                                                ->label('')
                                                ->content('No subjects available for the selected courses.')
                                                ->columnSpanFull()
                                        ];
                                    }
                                    
                                    return $subjects->map(function ($subject) {
                                        return Forms\Components\Toggle::make("subject_{$subject->id}")
                                            ->label($subject->name)
                                            ->default(function ($record) use ($subject) {
                                                if (!$record) return false;
                                                // Toggle is ON if subject is assigned to the batch
                                                return $record->subjects->contains($subject->id);
                                            })
                                            ->extraAttributes(['class' => 'custom-green-toggle']);
                                    })->toArray();
                                })
                        ])
                        ->columnSpanFull()
                        ->live(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['courses', 'days', 'students', 'teacher']);
                
                // If user is a teacher, only show batches assigned to them
                if (auth()->user()->isTeacher()) {
                    $query->where('teacher_id', auth()->id());
                }
                
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Not set'),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Assigned Teacher')
                    ->sortable()
                    ->searchable()
                    ->placeholder('No teacher assigned')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('courses.name')
                    ->label('Assigned Courses')
                    ->badge()
                    ->separator(',')
                    ->color('success')
                    ->wrap(),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(function ($record) {
                        if (auth()->user()->isTeacher()) {
                            return $record->getTeacherProgressPercentage(auth()->id()) . '%';
                        } else {
                            return $record->getOverallProgressPercentage() . '%';
                        }
                    })
                    ->color('info')
                    ->sortable(false),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Students')
                    ->counts('students')
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('status')
                    ->label('View Status')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.batches.status', $record))
                    ->color('info')
                    ->openUrlInNewTab(false)
                    ->visible(fn () => auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher() || auth()->user()->isAccounts() || auth()->user()->isManager())),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => static::canEdit($record) && !auth()->user()->isManager()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => static::canDelete($record) && !auth()->user()->isManager())
                    ->requiresConfirmation()
                    ->modalHeading('Delete Batch')
                    ->modalDescription(fn ($record) => 
                        'Are you sure you want to delete this batch? ' . 
                        'This will remove ' . $record->students()->count() . 
                        ' student(s) from this batch (they will not be deleted, just unassigned).'
                    )
                    ->modalSubmitActionLabel('Yes, Delete Batch'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Batches')
                        ->modalDescription('Are you sure you want to delete the selected batches? Students in these batches will be unassigned but not deleted.')
                        ->modalSubmitActionLabel('Yes, Delete Batches')
                        ->visible(fn () => (auth()->user()->isAdmin() || auth()->user()->isAccounts()) && !auth()->user()->isManager()),
                ])
                ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isAccounts()),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatches::route('/'),
            'create' => Pages\CreateBatch::route('/create'),
            'edit' => Pages\EditBatch::route('/{record}/edit'),
            'status' => Pages\BatchStatus::route('/{record}/status'),
        ];
    }

    public static function canCreate(): bool
    {
        return (auth()->user()->isAdmin() || auth()->user()->isAccounts()) && !auth()->user()->isManager();
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        // Managers cannot edit
        if (auth()->user()->isManager()) {
            return false;
        }
        
        // Admins and accounts users can edit any batch
        if (auth()->user()->isAdmin() || auth()->user()->isAccounts()) {
            return true;
        }
        
        // Teachers can only edit their own batches
        if (auth()->user()->isTeacher()) {
            return $record->teacher_id === auth()->id();
        }
        
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        // Managers cannot delete
        if (auth()->user()->isManager()) {
            return false;
        }
        
        // Only admins and accounts users can delete batches
        return auth()->user()->isAdmin() || auth()->user()->isAccounts();
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hide for dataentry users, only show for admin, accounts, and teachers
        return !(auth()->check() && auth()->user()->isDataEntry());
    }
}
