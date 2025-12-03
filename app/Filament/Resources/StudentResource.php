<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Batch;
use App\Models\User;
use App\Exports\StudentsExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'students';

    protected static ?string $modelLabel = 'Student';

    protected static ?string $pluralModelLabel = 'Students';

    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'student');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile Details')
                    ->schema([
                        Grid::make(1)->schema([
                            FileUpload::make('profile_picture')
                                ->label('Profile Picture')
                                ->image()
                                ->directory('profile-pictures')
                                ->imageEditor(),
                        ]),
                    ]),
                Section::make('Student Information')
                    ->schema([
                        Grid::make(2)->schema([
                            // 1. Student Full Name
                            TextInput::make('name')
                                ->label('Student Full Name')
                                ->required(),
                            
                            // 2. Guardian Name
                            TextInput::make('guardian_name')
                                ->label('Guardian Name')
                                ->required(),
                            
                            // 3. Date of Birth
                            DatePicker::make('dob')
                                ->label('Date of Birth')
                                ->id('dob-datepicker')
                                ->native(false)
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    if ($state) {
                                        $set('age', Carbon::parse($state)->age);
                                    }
                                }),
                            
                            // 4. Phone Number
                            TextInput::make('phone')
                                ->label('Phone Number')
                                ->tel()
                                ->required(),
                            
                            // 5. Qualification
                            Select::make('qualification')
                                ->label('Qualification')
                                ->options([
                                    'High School' => 'High School',
                                    'Diploma' => 'Diploma',
                                    'Bachelor\'s Degree' => 'Bachelor\'s Degree',
                                    'Master\'s Degree' => 'Master\'s Degree',
                                    'PhD' => 'PhD',
                                    'Other' => 'Other',
                                ])
                                ->required(),
                            
                            // 6. Batch (Optional)
                            Select::make('batch_id')
                                ->label('Batch (Optional)')
                                ->relationship('batch', 'name')
                                ->searchable()
                                ->preload(),
                            
                            // 7. Email ID
                            TextInput::make('email')
                                ->label('Email ID')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true),
                            
                            // 8. Address Information
                            \Filament\Forms\Components\Textarea::make('address')
                                ->label('Address Information')
                                ->required()
                                ->rows(3)
                                ->columnSpanFull(),
                            
                            // 9. Financial Information (Optional)
                            TextInput::make('course_fee')
                                ->label('Course Fee (Optional)')
                                ->numeric()
                                ->prefix('₹')
                                ->minValue(0)
                                ->maxValue(999999999999.99)
                                ->step(0.01)
                                ->helperText('Maximum value: ₹999,999,999,999.99'),
                            
                            // 10. Gender
                            Select::make('gender')
                                ->label('Gender')
                                ->options([
                                    'male' => 'Male',
                                    'female' => 'Female',
                                    'other' => 'Other',
                                ])
                                ->required(),
                            
                            // 11. Nationality
                            TextInput::make('nationality')
                                ->label('Nationality')
                                ->required()
                                ->default('Indian'),
                        ])
                    ]),
                Section::make('Account Security')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->dehydrated(fn ($state): bool => filled($state))
                                ->required(fn (string $context): bool => $context === 'create')
                                ->minLength(8),
                            TextInput::make('password_confirmation')
                                ->label('Confirm Password')
                                ->password()
                                ->dehydrated(false)
                                ->required(fn (string $context): bool => $context === 'create')
                                ->same('password'),
                        ])
                    ])
                    ->visible(fn (string $context): bool => $context === 'create'),
                
                Section::make('Verification Code Assignment')
                    ->schema([
                        Select::make('verification_code_id')
                            ->label('Verification Code')
                            ->options(function () {
                                return \App\Models\VerificationCode::where('is_used', false)
                                    ->where('is_active', true)
                                    ->where(function ($query) {
                                        $query->whereNull('expires_at')
                                              ->orWhere('expires_at', '>', now());
                                    })
                                    ->with(['course', 'subject'])
                                    ->get()
                                    ->mapWithKeys(function ($code) {
                                        return [$code->id => $code->code . ' - ' . $code->course->name . ' (' . $code->subject->name . ')'];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->helperText('Select an unused verification code to assign this student to a course and subject'),
                        
                        \Filament\Forms\Components\Toggle::make('is_verified')
                            ->label('Mark as Verified')
                            ->default(true)
                            ->helperText('Check this to automatically verify the student account'),
                    ])
                    ->visible(fn (string $context): bool => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('batch.name')
                    ->label('Batch')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qualification')
                    ->sortable(),
                Tables\Columns\TextColumn::make('course_fee')
                    ->label('Course Fee')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_fees_paid')
                    ->label('Fees Paid')
                    ->money('INR')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->total_fees_paid),
                Tables\Columns\TextColumn::make('balance_fees_due')
                    ->label('Balance Due')
                    ->money('INR')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->balance_fees_due),


                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        if ($record->balance_fees_due > 0) {
                            return 'Pending Fees';
                        }
                        return 'Fees Paid';
                    })
                    ->colors([
                        'danger' => 'Pending Fees',
                        'success' => 'Fees Paid',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('batch_id')
                    ->label('Batch')
                    ->options(Batch::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('All Batches'),
                Tables\Filters\TernaryFilter::make('has_batch')
                    ->label('Has Batch')
                    ->placeholder('All Students')
                    ->trueLabel('With Batch')
                    ->falseLabel('Without Batch')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('batch_id'),
                        false: fn (Builder $query) => $query->whereNull('batch_id'),
                    ),
            ])
            ->actionsColumnLabel('Actions')
            ->actions([
                Tables\Actions\Action::make('view_progress')
                    ->label('Progress')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->action(function ($record) {
                        return redirect()->to(route('filament.admin.resources.students.progress', $record));
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => !auth()->user()->isManager()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => !auth()->user()->isManager()),
                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected Students')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            $studentIds = $records->pluck('id')->toArray();
                            $students = User::whereIn('id', $studentIds)->where('role', 'student')->with('batch')->get();
                            
                            return Excel::download(
                                new StudentsExport(null, $students), 
                                'selected_students_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                            );
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        if (auth()->check() && auth()->user()->isManager()) {
            // For manager users, only show list page (read-only)
            return [
                'index' => Pages\ListStudents::route('/'),
                'progress' => Pages\StudentProgress::route('/{record}/progress'),
            ];
        }
        
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'progress' => Pages\StudentProgress::route('/{record}/progress'),
        ];
    }

    public static function canCreate(): bool
    {
        // Managers cannot create students
        return !auth()->user()->isManager();
    }
} 