<?php

namespace App\Filament\Resources\BatchResource\Pages;

use App\Filament\Resources\BatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBatches extends ListRecords
{
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];
        
        // Only show create button for admins
        if (auth()->user()->isAdmin()) {
            $actions[] = Actions\CreateAction::make();
        }
        
        return $actions;
    }
}
