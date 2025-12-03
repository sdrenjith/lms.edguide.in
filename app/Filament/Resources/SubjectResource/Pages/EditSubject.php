<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Filament\Resources\SubjectResource;
use App\Models\Subject;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubject extends EditRecord
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Debug: Log the received data
        \Log::info('EditSubject - Received data:', $data);
        
        $record->update([
            'name' => $data['name'],
            'course_id' => $data['course_id'],
        ]);

        // Sync teachers if provided
        if (isset($data['teachers']) && is_array($data['teachers'])) {
            \Log::info('EditSubject - Syncing teachers:', $data['teachers']);
            $record->teachers()->sync($data['teachers']);
        } else {
            \Log::warning('EditSubject - No teachers data received or invalid format');
        }

        return $record;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing teachers for editing
        $data['teachers'] = $this->record->teachers->pluck('id')->toArray();
        
        return $data;
    }
}
