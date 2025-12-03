<?php

namespace App\Filament\Resources\VerificationCodeResource\Pages;

use App\Filament\Resources\VerificationCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVerificationCodes extends ListRecords
{
    protected static string $resource = VerificationCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

