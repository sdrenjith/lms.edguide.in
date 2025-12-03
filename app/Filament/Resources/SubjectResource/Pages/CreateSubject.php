<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Filament\Resources\SubjectResource;
use App\Models\Subject;
use Filament\Resources\Pages\CreateRecord;

class CreateSubject extends CreateRecord
{
    protected static string $resource = SubjectResource::class;

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();
        return array_filter($actions, function ($action) {
            return $action->getName() !== 'createAnother';
        });
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Debug: Log the received data
        \Log::info('CreateSubject - Received data:', $data);
        
        $subject = static::getModel()::create([
            'name' => $data['name'],
            'course_id' => $data['course_id'],
        ]);

        // Sync teachers if provided
        if (isset($data['teachers']) && is_array($data['teachers'])) {
            \Log::info('CreateSubject - Syncing teachers:', $data['teachers']);
            $subject->teachers()->sync($data['teachers']);
        } else {
            \Log::warning('CreateSubject - No teachers data received or invalid format');
        }

        return $subject;
    }
}
