<?php

namespace App\Filament\Resources\BatchResource\Pages;

use App\Filament\Resources\BatchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatch extends EditRecord
{
    protected static string $resource = BatchResource::class;

    protected array $toggleStates = [];

    /**
     * Get available subjects based on user role
     */
    protected function getAvailableSubjects()
    {
        if (auth()->user()->isTeacher()) {
            // For teachers: only show their assigned subjects
            return auth()->user()->subjects;
        } else {
            // For admins: show all subjects
            return \App\Models\Subject::all();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $batch = $this->record;
        
        // Set course toggles based on current assignments - dynamic courses
        $assignedCourseIds = $batch->courses->pluck('id')->toArray();
        $courses = \App\Models\Course::all();
        foreach ($courses as $course) {
            $data["course_{$course->id}"] = in_array($course->id, $assignedCourseIds);
        }
        
        
        // Set subject toggles based on assignment
        $assignedSubjectIds = $batch->subjects->pluck('id')->toArray();
        $availableSubjects = $this->getAvailableSubjects();
        
        foreach ($availableSubjects as $subject) {
            // Toggle is ON if subject is assigned to the batch
            $isAssigned = in_array($subject->id, $assignedSubjectIds);
            $data["subject_{$subject->id}"] = $isAssigned;
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store toggle states for later use in afterSave
        $this->toggleStates = [];
        
        // Store course toggle states - dynamic courses
        $courses = \App\Models\Course::all();
        foreach ($courses as $course) {
            $this->toggleStates["course_{$course->id}"] = $data["course_{$course->id}"] ?? false;
        }
        
        // Store subject toggle states
        $availableSubjects = $this->getAvailableSubjects();
        foreach ($availableSubjects as $subject) {
            $this->toggleStates["subject_{$subject->id}"] = $data["subject_{$subject->id}"] ?? false;
        }
        
        // Remove toggle fields from data as they're not part of the model - dynamic courses
        foreach ($courses as $course) {
            unset($data["course_{$course->id}"]);
        }
        foreach ($availableSubjects as $subject) {
            unset($data["subject_{$subject->id}"]);
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $batch = $this->record;
        
        // Handle course assignments - dynamic courses
        $selectedCourseIds = [];
        $courses = \App\Models\Course::all();
        foreach ($courses as $course) {
            if ($this->toggleStates["course_{$course->id}"] ?? false) {
                $selectedCourseIds[] = $course->id;
            }
        }
        $batch->courses()->sync($selectedCourseIds);
        
        // Handle subject assignments
        $selectedSubjectIds = [];
        $availableSubjects = $this->getAvailableSubjects();
        
        foreach ($availableSubjects as $subject) {
            $isToggleOn = $this->toggleStates["subject_{$subject->id}"] ?? false;
            
            if ($isToggleOn) {
                // Toggle is ON: assign subject
                $selectedSubjectIds[] = $subject->id;
            }
        }
        
        // Sync subject assignments
        $batch->subjects()->sync($selectedSubjectIds);
    }
}
