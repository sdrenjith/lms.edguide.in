<?php

namespace App\Filament\Resources\VerificationCodeResource\Pages;

use App\Filament\Resources\VerificationCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVerificationCode extends EditRecord
{
    protected static string $resource = VerificationCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

