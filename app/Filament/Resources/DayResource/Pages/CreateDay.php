<?php

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDay extends CreateRecord
{
    protected static string $resource = DayResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();
        return array_filter($actions, function ($action) {
            return $action->getName() !== 'createAnother';
        });
    }
}
