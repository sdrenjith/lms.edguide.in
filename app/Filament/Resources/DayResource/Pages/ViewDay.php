<?php

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Resources\Pages\ViewRecord;

class ViewDay extends ViewRecord
{
    protected static string $resource = DayResource::class;

    protected function getFormSchema(): array
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
} 