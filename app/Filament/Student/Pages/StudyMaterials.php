<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use App\Models\Note;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;

class StudyMaterials extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static string $view = 'filament.student.pages.study-materials';
    protected static ?string $title = 'Study Materials';
    protected static ?string $navigationLabel = 'Study Materials';
    protected static ?string $slug = 'study-materials';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        
        // Get user's batch
        $batch = $user->batch;
        
        if (!$batch) {
            return [
                'studyMaterials' => [],
                'user' => $user
            ];
        }
        
        // Get assigned courses and days for the user's batch
        $assignedCourses = $batch->courses;
        $assignedDays = $batch->days;
        
        // Get course and day IDs for filtering
        $assignedCourseIds = $assignedCourses->pluck('id')->toArray();
        $assignedDayNumbers = $assignedDays->pluck('number')->toArray();
        
        // Get notes and videos for assigned courses, grouped by day
        $studyMaterials = [];
        
        // Get all notes for assigned courses
        $notes = Note::whereIn('course_id', $assignedCourseIds)
            ->with(['course', 'subject'])
            ->orderBy('day_number')
            ->orderBy('course_id')
            ->orderBy('subject_id')
            ->get();
        
        // Get all videos for assigned courses
        $videos = Video::whereIn('course_id', $assignedCourseIds)
            ->with(['course', 'subject'])
            ->orderBy('day_number')
            ->orderBy('course_id')
            ->orderBy('subject_id')
            ->get();
        
        // Group materials by day number
        $materialsByDay = [];
        
        // Process notes
        foreach ($notes as $note) {
            $dayNumber = $note->day_number;
            if (!isset($materialsByDay[$dayNumber])) {
                $materialsByDay[$dayNumber] = [
                    'day_number' => $dayNumber,
                    'notes' => [],
                    'videos' => []
                ];
            }
            $materialsByDay[$dayNumber]['notes'][] = $note;
        }
        
        // Process videos
        foreach ($videos as $video) {
            $dayNumber = $video->day_number;
            if (!isset($materialsByDay[$dayNumber])) {
                $materialsByDay[$dayNumber] = [
                    'day_number' => $dayNumber,
                    'notes' => [],
                    'videos' => []
                ];
            }
            $materialsByDay[$dayNumber]['videos'][] = $video;
        }
        
        // Sort by day number
        ksort($materialsByDay);
        
        // Convert to the format expected by the view
        $studyMaterials = [];
        foreach ($materialsByDay as $dayNumber => $dayData) {
            $studyMaterials[] = [
                'day_number' => $dayNumber,
                'materials' => $dayData
            ];
        }
        
        return [
            'studyMaterials' => $studyMaterials,
            'user' => $user
        ];
    }
} 