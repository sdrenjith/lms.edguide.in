<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Day;
use App\Models\Note;
use App\Models\Video;
use App\Models\SpeakingSession;
use App\Models\StudentActivity;

class StudentPageController extends Controller
{
    private function trackActivity()
    {
        if (Auth::check() && Auth::user()->role === 'student') {
            $user = Auth::user();
            
            // Check if user has an active session
            $activeActivity = StudentActivity::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first();
            
            if (!$activeActivity) {
                // Create new activity record for login
                StudentActivity::create([
                    'user_id' => $user->id,
                    'login_at' => now(),
                    'last_activity_at' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } else {
                // Update last activity timestamp
                $activeActivity->updateActivity();
            }
        }
    }

    public function profile()
    {
        $this->trackActivity();
        $user = Auth::user();
        
        // Process batch information if it exists
        $batchInfo = null;
        if ($user->batch) {
            if (is_string($user->batch)) {
                $batchInfo = json_decode($user->batch, true);
            } else {
                $batchInfo = $user->batch;
            }
        }
        
        return view('student.profile', compact('user', 'batchInfo'));
    }

    public function studyMaterials()
    {
        $this->trackActivity();
        $user = Auth::user();
        
        // Get assigned courses and days for the student
        $assignedCourses = $user->assignedCourses();
        $assignedDays = $user->assignedDays();
        $assignedCourseIds = $assignedCourses->pluck('id')->toArray();
        $assignedDayIds = $assignedDays->pluck('id')->toArray();
        
        // Get study materials data filtered by assigned courses
        $studyMaterials = [];
        
        // If there are assigned days, get materials from those days
        if ($assignedDayIds) {
            $days = Day::with(['notes', 'videos'])
                ->whereIn('id', $assignedDayIds)
                ->get();
            
            foreach ($days as $day) {
                $materials = [];
                
                // Add notes (filter by assigned courses)
                foreach ($day->notes as $note) {
                    if (in_array($note->course_id, $assignedCourseIds)) {
                        $materials[] = [
                            'type' => 'note',
                            'title' => $note->title,
                            'file_path' => $note->file_path,
                            'created_at' => $note->created_at,
                        ];
                    }
                }
                
                // Add videos (filter by assigned courses)
                foreach ($day->videos as $video) {
                    if (in_array($video->course_id, $assignedCourseIds)) {
                        $materials[] = [
                            'type' => 'video',
                            'title' => $video->title,
                            'file_path' => $video->file_path,
                            'created_at' => $video->created_at,
                        ];
                    }
                }
                
                if (!empty($materials)) {
                    $studyMaterials[] = [
                        'day' => $day,
                        'materials' => $materials,
                    ];
                }
            }
        }
        
        // If no assigned days or no materials found, get materials directly from assigned courses
        if (empty($studyMaterials) && $assignedCourseIds) {
            // Get notes for assigned courses
            $notes = \App\Models\Note::whereIn('course_id', $assignedCourseIds)
                ->with(['course', 'subject'])
                ->get();
            
            // Get videos for assigned courses  
            $videos = \App\Models\Video::whereIn('course_id', $assignedCourseIds)
                ->with(['course', 'subject'])
                ->get();
            
            // Group materials by day number (including null days)
            $materialsByDay = [];
            
            // Process notes
            foreach ($notes as $note) {
                $dayNumber = $note->day_number ?? 'general';
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
                $dayNumber = $video->day_number ?? 'general';
                if (!isset($materialsByDay[$dayNumber])) {
                    $materialsByDay[$dayNumber] = [
                        'day_number' => $dayNumber,
                        'notes' => [],
                        'videos' => []
                    ];
                }
                $materialsByDay[$dayNumber]['videos'][] = $video;
            }
            
            // Convert to the format expected by the view
            foreach ($materialsByDay as $dayNumber => $dayData) {
                $studyMaterials[] = [
                    'day_number' => $dayNumber,
                    'materials' => $dayData
                ];
            }
        }
        
        return view('student.study-materials', compact('user', 'studyMaterials'));
    }

    public function speakingSessions()
    {
        $this->trackActivity();
        $user = Auth::user();
        
        // Get speaking sessions for the student's batch
        if ($user->batch_id) {
            $speakingSessions = SpeakingSession::where('batch_id', $user->batch_id)
                ->where('is_active', true)
                ->orderBy('session_date', 'desc')
                ->orderBy('session_time', 'desc')
                ->get();
        } else {
            // If student doesn't have a batch assigned, return empty collection
            $speakingSessions = collect();
        }
        
        return view('student.speaking-sessions', compact('user', 'speakingSessions'));
    }
}
