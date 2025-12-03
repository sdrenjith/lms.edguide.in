<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Models\Note;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Content Management';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('topic')
                            ->label('Topic')
                            ->placeholder('Enter the topic covered in this note')
                            ->helperText('Brief description of the main topic or concept covered')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('pdf_path')
                            ->label('PDF File')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240) // 10MB
                            ->required(fn ($record) => !$record) // Required only for creation
                            ->helperText(fn ($record) => $record ? 'Upload a new PDF to replace the existing one. Leave empty to keep the current PDF.' : 'Upload the PDF file for this note.')
                            ->disk('public')
                            ->directory('notes')
                            ->visibility('public')
                            ->openable()
                            ->downloadable()
                            ->previewable(false)
                            ->columnSpanFull()
                            ->extraInputAttributes([
                                'class' => 'fi-fo-file-upload fi-fo-file-upload-wrapper'
                            ])
                            ->extraAttributes([
                                'style' => 'border: 2px dashed #d1d5db; border-radius: 0.5rem; padding: 1rem; background-color: #f9fafb; min-height: 120px;'
                            ]),
                        Forms\Components\Grid::make(['default' => 1, 'md' => 2])
                            ->schema([
                                Forms\Components\Select::make('course_id')
                                    ->label('Course')
                                    ->relationship('course', 'name')
                                    ->required()
                                    ->placeholder('Select course')
                                    ->preload(),
                                Forms\Components\Select::make('subject_id')
                                    ->label('Subject')
                                    ->relationship('subject', 'name')
                                    ->required()
                                    ->placeholder('Select subject')
                                    ->preload(),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('topic')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('course.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course')
                    ->relationship('course', 'name'),
                Tables\Filters\SelectFilter::make('subject')
                    ->relationship('subject', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Note Preview')
                    ->modalContent(fn($record) => view('filament.resources.note-resource.preview', ['record' => $record]))
                    ->color('info')
                    ->modalSubmitActionLabel('Go Back')
                    ->modalCancelAction(false),
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
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Show for all users except dataentry users
        return !(auth()->check() && auth()->user()->isDataEntry());
    }
} 