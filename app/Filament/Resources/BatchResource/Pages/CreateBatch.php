<?php

namespace App\Filament\Resources\BatchResource\Pages;

use App\Filament\Resources\BatchResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBatch extends CreateRecord
{
    protected static string $resource = BatchResource::class;
    
    protected array $toggleStates = [];

    /**
     * Get available days based on user role
     */
    protected function getAvailableDays()
    {
        if (auth()->user()->isTeacher()) {
            // For teachers: only show days that have questions for their assigned subjects
            $teacherSubjectIds = auth()->user()->subjects()->pluck('subjects.id')->toArray();
            
            if (empty($teacherSubjectIds)) {
                return collect(); // No subjects assigned, no days to show
            }
            
            return \App\Models\Day::whereHas('questions', function ($query) use ($teacherSubjectIds) {
                $query->whereIn('subject_id', $teacherSubjectIds);
            })->get();
        } else {
            // For admins: show all days that have questions
            return \App\Models\Day::whereHas('questions')->get();
        }
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If user is a teacher, automatically assign the batch to them
        if (auth()->user()->isTeacher()) {
            $data['teacher_id'] = auth()->id();
        }
        
        // Store toggle states for later use in afterCreate
        $this->toggleStates = [];
        
        // Store course toggle states
        $this->toggleStates['course_1'] = $data['course_1'] ?? false;
        $this->toggleStates['course_2'] = $data['course_2'] ?? false;
        $this->toggleStates['course_3'] = $data['course_3'] ?? false;
        $this->toggleStates['course_4'] = $data['course_4'] ?? false;
        
        // Store day toggle states (toggle ON means assigned AND completed)
        $availableDays = $this->getAvailableDays();
        foreach ($availableDays as $day) {
            $this->toggleStates["day_{$day->id}"] = $data["day_{$day->id}"] ?? false;
        }
        
        // Remove toggle fields from data as they're not part of the model
        foreach (['course_1', 'course_2', 'course_3', 'course_4'] as $field) {
            unset($data[$field]);
        }
        foreach ($availableDays as $day) {
            unset($data["day_{$day->id}"]);
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $batch = $this->record;
        
        // Handle course assignments
        $selectedCourseIds = [];
        foreach ([1, 2, 3, 4] as $courseId) {
            if ($this->toggleStates["course_{$courseId}"] ?? false) {
                $selectedCourseIds[] = $courseId;
            }
        }
        $batch->courses()->sync($selectedCourseIds);
        
        // Handle day assignments and completion
        $selectedDayIds = [];
        $availableDays = $this->getAvailableDays();
        
        foreach ($availableDays as $day) {
            $isToggleOn = $this->toggleStates["day_{$day->id}"] ?? false;
            
            if ($isToggleOn) {
                // Toggle is ON: assign day AND mark as completed
                $selectedDayIds[] = $day->id;
                
                // Mark as completed
                $batch->dayProgress()->updateOrCreate(
                    ['day_id' => $day->id],
                    [
                        'is_completed' => true,
                        'completed_at' => now(),
                        'completed_by' => auth()->id()
                    ]
                );
            } else {
                // Toggle is OFF: assign day but mark as NOT completed
                $selectedDayIds[] = $day->id;
                
                // Mark as not completed
                $batch->dayProgress()->updateOrCreate(
                    ['day_id' => $day->id],
                    [
                        'is_completed' => false,
                        'completed_at' => null,
                        'completed_by' => null
                    ]
                );
            }
        }
        
        // Sync day assignments (assign ALL selected days, regardless of completion status)
        $batch->days()->sync($selectedDayIds);
    }
}
