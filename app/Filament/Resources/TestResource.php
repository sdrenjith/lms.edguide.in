<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestResource\Pages;
use App\Models\Test;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class TestResource extends Resource
{
    protected static ?string $model = Test::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Tests';
    
    protected static ?string $navigationGroup = 'Test Management';
    
    protected static ?string $modelLabel = 'Test';
    
    protected static ?string $pluralModelLabel = 'Tests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Grid::make()
                    ->schema([
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Test Name')
                                    ->columnSpan(1),
                                \Filament\Forms\Components\Textarea::make('description')
                                    ->maxLength(1000)
                                    ->label('Description')
                                    ->placeholder('Optional description for the test')
                                    ->columnSpan(1),
                            ]),
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('total_score')
                                    ->required()
                                    ->numeric()
                                    ->default(100)
                                    ->minValue(1)
                                    ->maxValue(1000)
                                    ->label('Total Score'),
                                \Filament\Forms\Components\TextInput::make('passmark')
                                    ->required()
                                    ->numeric()
                                    ->default(50)
                                    ->minValue(1)
                                    ->maxValue(1000)
                                    ->label('Pass Mark'),
                                \Filament\Forms\Components\Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Course'),
                                \Filament\Forms\Components\Select::make('subject_id')
                                    ->relationship('subject', 'name', function (\Illuminate\Database\Eloquent\Builder $query) {
                                        $user = auth()->user();
                                        if ($user->isTeacher()) {
                                            return $query->where('teacher_id', $user->id);
                                        }
                                        return $query;
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Subject'),
                            ]),
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Active')
                            ->helperText('Inactive tests will not be visible to students')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->extraAttributes(['style' => 'padding-left: 2rem;']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Test Name'),
                
                TextColumn::make('total_score')
                    ->sortable()
                    ->label('Total Score'),
                
                TextColumn::make('passmark')
                    ->sortable()
                    ->label('Pass Mark'),
                
                TextColumn::make('course.name')
                    ->sortable()
                    ->searchable()
                    ->label('Course'),
                
                TextColumn::make('subject.name')
                    ->sortable()
                    ->searchable()
                    ->label('Subject'),
                
                TextColumn::make('is_active')
                    ->label('Active')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive')
                    ->color(fn($state) => $state ? 'success' : 'danger'),
                
                // Removed created_at column
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'name')
                    ->label('Course')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Subject')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('gray'),
                Tables\Actions\Action::make('submissions')
                    ->label('View Submissions')
                    ->icon('heroicon-o-users')
                    ->url(fn (Test $record): string => route('filament.admin.resources.tests.submissions', $record))
                    ->color('success'),
                Tables\Actions\EditAction::make()->color('primary'),
                Tables\Actions\DeleteAction::make()->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTests::route('/'),
            'create' => Pages\CreateTest::route('/create'),
            'edit' => Pages\EditTest::route('/{record}/edit'),
            'view' => Pages\ViewTest::route('/{record}'),
            'submissions' => Pages\TestSubmissions::route('/{record}/submissions'),
            'submissions.answers' => Pages\StudentAnswers::route('/{record}/submissions/{student}/answers'),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Temporarily hidden - Show for admin, teacher, and manager users
        return false; // $user && ($user->isAdmin() || $user->isTeacher() || $user->isManager());
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        
        if ($user->isTeacher()) {
            return parent::getEloquentQuery()
                ->whereHas('subject', function (Builder $query) use ($user) {
                    $query->where('teacher_id', $user->id);
                });
        }
        
        return parent::getEloquentQuery();
    }
} 