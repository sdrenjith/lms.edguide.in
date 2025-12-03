<?php

namespace App\Filament\Resources\SpeakingSessionResource\Pages;

use App\Filament\Resources\SpeakingSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpeakingSessions extends ListRecords
{
    protected static string $resource = SpeakingSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
} 