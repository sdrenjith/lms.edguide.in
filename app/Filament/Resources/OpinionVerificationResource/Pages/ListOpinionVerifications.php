<?php

namespace App\Filament\Resources\OpinionVerificationResource\Pages;

use App\Filament\Resources\OpinionVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOpinionVerifications extends ListRecords
{
    protected static string $resource = OpinionVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action needed for this resource
        ];
    }
} 