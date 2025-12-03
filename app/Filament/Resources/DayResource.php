<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DayResource\Pages;
use App\Filament\Resources\DayResource\RelationManagers;
use App\Models\Day;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DayResource extends Resource
{
    protected static ?string $model = Day::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day_number')
                    ->label('Day Number')
                    ->options(array_combine(range(1, 200), range(1, 200)))
                    ->required()
                    ->reactive(),
                Forms\Components\Hidden::make('title')
                    ->default(fn($get) => 'Day ' . $get('day_number')),
                Forms\Components\DatePicker::make('date')
                    ->label('Day Date')
                    ->required(),
                Forms\Components\Select::make('course_id')
                    ->label('Assign to Course')
                    ->relationship('course', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Day Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Course Name')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // You can add filters here if needed
            ])
            ->defaultSort('date', 'asc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListDays::route('/'),
            'create' => Pages\CreateDay::route('/create'),
            'edit' => Pages\EditDay::route('/{record}/edit'),
            'view' => Pages\ViewDay::route('/{record}'),
        ];
    }

    public static function getViewFormSchema(): array
    {
        return [
            \Filament\Forms\Components\TextInput::make('title')
                ->label('Day Name')
                ->disabled(),
            \Filament\Forms\Components\TextInput::make('date')
                ->label('Day Date')
                ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('Y-m-d'))
                ->disabled(),
            \Filament\Forms\Components\TextInput::make('course.name')
                ->label('Course Name')
                ->disabled(),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
