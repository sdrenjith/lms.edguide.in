<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Filament\Resources\SubjectResource\RelationManagers;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Subject Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('course_id')
                            ->label('Course')
                            ->options(fn () => \App\Models\Course::pluck('name', 'id')->toArray())
                            ->required()
                            ->placeholder('Select a course')
                            ->helperText('Choose the course this subject belongs to'),
                        \App\Forms\Components\MultiSelectTeachers::make('teachers')
                            ->label('Assign Teachers')
                            ->helperText('Click to select/deselect teachers')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('teachers'))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Subject Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('course.name')->label('Course')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('teachers.name')
                    ->label('Assigned Teachers')
                    ->sortable()
                    ->searchable()
                    ->placeholder('No teachers assigned')
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return 'No teachers assigned';
                        }
                        
                        // If it's already a string, return it as is
                        if (is_string($state)) {
                            return $state;
                        }
                        
                        // If it's a collection, implode it
                        if (is_object($state) && method_exists($state, 'implode')) {
                            return $state->implode(', ');
                        }
                        
                        // If it's an array, implode it
                        if (is_array($state)) {
                            return implode(', ', $state);
                        }
                        
                        return 'No teachers assigned';
                    })
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Filter by Course')
                    ->relationship('course', 'name')
                    ->placeholder('All courses'),
                Tables\Filters\SelectFilter::make('teachers')
                    ->label('Filter by Teacher')
                    ->relationship('teachers', 'name', fn (Builder $query) => $query->where('role', 'teacher'))
                    ->placeholder('All teachers'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => !auth()->user()->isManager()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => !auth()->user()->isManager()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => !auth()->user()->isManager()),
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
        if (auth()->check() && auth()->user()->isManager()) {
            // For manager users, only show list page (read-only)
            return [
                'index' => Pages\ListSubjects::route('/'),
            ];
        }
        
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Show for admin and manager
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isManager());
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->isAdmin() && !auth()->user()->isManager();
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->check() && auth()->user()->isAdmin() && !auth()->user()->isManager();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->check() && auth()->user()->isAdmin() && !auth()->user()->isManager();
    }
}