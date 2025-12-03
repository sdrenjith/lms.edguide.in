<?php

namespace App\Filament\Resources\VerificationCodeResource\Pages;

use App\Filament\Resources\VerificationCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVerificationCode extends CreateRecord
{
    protected static string $resource = VerificationCodeResource::class;

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();
        return array_filter($actions, function ($action) {
            return $action->getName() !== 'createAnother';
        });
    }
}

