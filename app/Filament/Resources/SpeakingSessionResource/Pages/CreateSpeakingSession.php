<?php

namespace App\Filament\Resources\SpeakingSessionResource\Pages;

use App\Filament\Resources\SpeakingSessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpeakingSession extends CreateRecord
{
    protected static string $resource = SpeakingSessionResource::class;

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();
        return array_filter($actions, function ($action) {
            return $action->getName() !== 'createAnother';
        });
    }
} 