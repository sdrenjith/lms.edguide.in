<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Models\Day;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DayRelationManager extends RelationManager
{
    protected static string $relationship = 'days';
    protected static ?string $recordTitleAttribute = 'title';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Day Title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('gray'),
                Tables\Actions\EditAction::make()->color('primary'),
                Tables\Actions\DeleteAction::make()->color('danger'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
} 