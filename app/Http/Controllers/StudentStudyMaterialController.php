<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Video;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentStudyMaterialController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get user's batch
        $batch = $user->batch;
        
        if (!$batch) {
            return view('filament.student.pages.study-materials', [
                'studyMaterials' => [],
                'user' => $user
            ]);
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
        
        return view('filament.student.pages.study-materials', [
            'studyMaterials' => $studyMaterials,
            'user' => $user
        ]);
    }
} 