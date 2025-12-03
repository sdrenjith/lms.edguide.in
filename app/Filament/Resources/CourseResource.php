<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('gray'),
                Tables\Actions\EditAction::make()
                    ->color('primary')
                    ->visible(fn () => !auth()->user()->isManager()),
                Tables\Actions\DeleteAction::make()
                    ->color('danger')
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
            RelationManagers\DayRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        if (auth()->check() && auth()->user()->isManager()) {
            // For manager users, only show list and view pages (read-only)
            return [
                'index' => Pages\ListCourses::route('/'),
                'view' => Pages\ViewCourse::route('/{record}'),
            ];
        }
        
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
            'view' => Pages\ViewCourse::route('/{record}'),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hide for datamanager, accounts, and dataentry users (show for teachers)
        return !(auth()->check() && (auth()->user()->isDataManager() || auth()->user()->isAccounts() || auth()->user()->isDataEntry()));
    }

    public static function canCreate(): bool
    {
        // Managers cannot create courses
        return !auth()->user()->isManager();
    }
}
