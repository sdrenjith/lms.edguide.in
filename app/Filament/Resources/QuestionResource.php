<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use App\Models\Test;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Single comprehensive section
                Forms\Components\Section::make('Create Question')
                    ->description('Fill in all the details to create a new question')
                    ->icon('heroicon-m-plus-circle')
                    ->schema([
                        // Basic question info
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('day_id')
                                    ->label('Day')
                                    ->relationship('day', 'title')
                                    ->required()
                                    ->placeholder('Select day')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('course_id')
                                    ->label('Course')
                                    ->relationship('course', 'name')
                                    ->required()
                                    ->placeholder('Select course')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('subject_id')
                                    ->label('Subject')
                                    ->relationship('subject', 'name')
                                    ->required()
                                    ->placeholder('Select subject')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('test_id')
                                    ->label('Test (Optional)')
                                    ->options(fn () => Test::pluck('name', 'id'))
                                    ->searchable()
                                    ->placeholder('Select test (optional)')
                                    ->preload()
                                    ->required(false),

                                Forms\Components\TextInput::make('topic')
                                    ->label('Topic')
                                    ->placeholder('Enter topic (e.g., Grammar, Vocabulary)')
                                    ->maxLength(255),

                                Forms\Components\Select::make('question_type_id')
                                    ->label('Question Type')
                                    ->relationship('questionType', 'name')
                                    ->required()
                                    ->placeholder('Select type')
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('points')
                                    ->label('Points')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->placeholder('Points (default: 1)'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ]),

                        // Question content
                        Forms\Components\Textarea::make('instruction')
                            ->label('Question Instruction')
                            ->required()
                            ->placeholder('Enter the question instruction...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('explanation')
                            ->label('Explanation (Optional)')
                            ->placeholder('Enter explanation if needed...')
                            ->rows(2)
                            ->columnSpanFull(),

                        // Options Section
                        Forms\Components\Fieldset::make('Question Options')
                            ->schema([
                                Forms\Components\Repeater::make('question_options')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('option')
                                            ->label('Option Text')
                                            ->required()
                                            ->placeholder('Enter option text...')
                                            ->maxLength(500)
                                            ->extraAttributes(['class' => 'option-input']),
                                    ])
                                    ->default(function ($record) {
                                        if ($record && $record->question_data) {
                                            $data = json_decode($record->question_data, true);
                                            if (isset($data['options'])) {
                                                return collect($data['options'])->map(fn($opt) => ['option' => $opt])->toArray();
                                            }
                                        }
                                        // Start with only one option field
                                        return [['option' => '']];
                                    })
                                    ->addActionLabel('+ Add Option')
                                    ->deleteAction(
                                        fn ($action) => $action
                                            ->label('Remove')
                                            ->size('sm')
                                            ->color('danger')
                                            ->icon('heroicon-m-trash')
                                            ->extraAttributes(['class' => 'remove-btn-red'])
                                    )
                                    ->addAction(
                                        fn ($action) => $action
                                            ->label('+ Add Option')
                                            ->size('sm')
                                            ->color('success')
                                            ->icon('heroicon-m-plus')
                                            ->extraAttributes(['class' => 'add-btn-green'])
                                    )
                                    ->minItems(1) // Allow minimum 1 option
                                    ->maxItems(10)
                                    ->columnSpanFull()
                                    ->collapsed(false)
                                    ->reorderableWithButtons()
                                    ->itemLabel(fn (array $state): ?string => 'Option: ' . ($state['option'] ?? 'New Option')),
                            ])
                            ->columnSpanFull(),

                        // Answer Indices Section
                        Forms\Components\Fieldset::make('Correct Answer Indices')
                            ->schema([
                                Forms\Components\Placeholder::make('indices_help')
                                    ->label('')
                                    ->content('**Note:** Use 0 for first option, 1 for second option, 2 for third option, etc.')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('correct_indices')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('index')
                                            ->label('Answer Index')
                                            ->numeric()
                                            ->required()
                                            ->placeholder('0')
                                            ->minValue(0)
                                            ->extraAttributes(['class' => 'index-input'])
                                            ->helperText('Enter the index of the correct option'),
                                    ])
                                    ->default(function ($record) {
                                        if ($record && $record->answer_data) {
                                            $data = json_decode($record->answer_data, true);
                                            if (isset($data['correct_indices'])) {
                                                return collect($data['correct_indices'])->map(fn($idx) => ['index' => $idx])->toArray();
                                            }
                                        }
                                        return [['index' => 0]];
                                    })
                                    ->addActionLabel('+ Add Answer Index')
                                    ->deleteAction(
                                        fn ($action) => $action
                                            ->label('Remove')
                                            ->size('sm')
                                            ->color('danger')
                                            ->icon('heroicon-m-trash')
                                            ->extraAttributes(['class' => 'remove-btn-red'])
                                    )
                                    ->addAction(
                                        fn ($action) => $action
                                            ->label('+ Add Answer Index')
                                            ->size('sm')
                                            ->color('primary')
                                            ->icon('heroicon-m-plus')
                                    )
                                    ->minItems(1)
                                    ->columnSpanFull()
                                    ->collapsed(false)
                                    ->reorderableWithButtons()
                                    ->itemLabel(fn (array $state): ?string => 'Answer Index: ' . ($state['index'] ?? 'New')),
                            ])
                            ->columnSpanFull(),

                        // Hidden fields for JSON data
                        Forms\Components\Hidden::make('question_data')
                            ->dehydrateStateUsing(function ($state, $get) {
                                $options = collect($get('question_options'))->pluck('option')->filter()->toArray();
                                return json_encode([
                                    'question' => $get('instruction') ?: 'Auto-generated question from form',
                                    'options' => $options,
                                ]);
                            }),

                        Forms\Components\Hidden::make('answer_data')
                            ->dehydrateStateUsing(function ($state, $get) {
                                $indices = collect($get('correct_indices'))->pluck('index')->map(fn($i) => (int)$i)->filter()->toArray();
                                return json_encode([
                                    'correct_indices' => $indices,
                                ]);
                            }),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->width('60px'),
                    
                Tables\Columns\TextColumn::make('instruction')
                    ->label('Question')
                    ->searchable()
                    ->limit(80)
                    ->tooltip(function ($record) {
                        return $record->instruction;
                    })
                    ->extraAttributes(['class' => 'font-medium']),

                Tables\Columns\TextColumn::make('course.name')
                    ->label('Course')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'A1' => 'success',
                        'A2' => 'info',
                        'B1' => 'warning',
                        'B2' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Use direct course relationship for accurate display
                        return $record->course?->name ?? 'No Course';
                    }),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->badge()
                    ->color('secondary')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('test.name')
                    ->label('Test')
                    ->default('None')
                    ->sortable(),

                Tables\Columns\TextColumn::make('topic')
                    ->label('Topic')
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->placeholder('No topic'),

                Tables\Columns\TextColumn::make('questionType.display_name')
                    ->label('Type')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('day.title')
                    ->label('Day')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name', function ($query) {
                        // If user is a teacher, only show their assigned subjects
                        if (auth()->user()->isTeacher()) {
                            $teacherSubjectIds = auth()->user()->subjects()->pluck('subjects.id');
                            return $query->whereIn('id', $teacherSubjectIds);
                        }
                        return $query;
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('question_type_id')
                    ->label('Question Type')
                    ->relationship('questionType', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('topic')
                    ->label('Topic')
                    ->options(function () {
                        return \App\Models\Question::whereNotNull('topic')
                            ->distinct()
                            ->pluck('topic', 'topic')
                            ->filter()
                            ->toArray();
                    })
                    ->searchable()
                    ->multiple(),
            ])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->color('info'),
                \Filament\Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),
                \Filament\Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-m-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Question')
                    ->modalDescription('Are you sure you want to delete this question? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete it'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Questions')
                        ->modalDescription('Are you sure you want to delete the selected questions? This action cannot be undone.')
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->poll('60s')
            ->deferLoading()
            ->toggleColumnsTriggerAction(null)
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record]))
            ->emptyStateHeading('No questions found')
            ->emptyStateDescription('Start by creating your first question.')
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->emptyStateActions([
                \Filament\Tables\Actions\Action::make('create')
                    ->label('Create Question')
                    ->url(static::getUrl('create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        
        // Load necessary relationships to avoid N+1 queries and ensure correct data
        $query->with(['course', 'subject', 'day.course', 'questionType', 'test']);
        
        // If user is a teacher, filter questions by their assigned subjects
        if (auth()->user()->isTeacher()) {
            $teacherSubjectIds = auth()->user()->subjects()->pluck('subjects.id');
            $query->whereIn('subject_id', $teacherSubjectIds);
        }
        
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
            'view' => Pages\ViewQuestion::route('/{record}'),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Temporarily hidden - Hide for accounts users only
        return false; // !(auth()->check() && auth()->user()->isAccounts());
    }
}