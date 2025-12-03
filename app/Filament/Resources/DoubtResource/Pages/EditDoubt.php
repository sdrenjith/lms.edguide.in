<?php

namespace App\Filament\Resources\DoubtResource\Pages;

use App\Filament\Resources\DoubtResource;
use Filament\Resources\Pages\EditRecord;

class EditDoubt extends EditRecord
{
    protected static string $resource = DoubtResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the user relationship and populate student_name field
        $record = $this->getRecord();
        $record->load('user');
        $data['student_name'] = $record->user?->name ?? 'Unknown Student';
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove student_name from data as it's not a database field
        unset($data['student_name']);
        
        if (!empty($data['reply'])) {
            $data['replied_at'] = now();
        }
        return $data;
    }
} 