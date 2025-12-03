<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get only assigned courses instead of all courses
        $assignedCourses = $user->assignedCourses();
        $assignedCourseIds = $assignedCourses->pluck('id')->toArray();
        
        $assignedDays = $user->assignedDays();
        $assignedDayIds = $assignedDays->pluck('id')->toArray();
        
        // Filter subjects based on user role
        if (auth()->check() && auth()->user()->isTeacher()) {
            $subjects = auth()->user()->subjects;
        } else {
            $subjects = \App\Models\Subject::all();
        }
        
        // Get all active questions (including those in tests)
        // Students should see all their assigned days regardless of test assignments
        $questions = \App\Models\Question::where('is_active', true)
            ->get()
            ->groupBy(function($q) {
                return $q->course_id . '-' . $q->subject_id . '-' . $q->day_id;
            });
        
        $answeredQuestionIds = [];
        if (auth()->check()) {
            $answeredQuestionIds = \DB::table('student_answers')
                ->where('user_id', auth()->id())
                ->pluck('question_id')
                ->toArray();
        }
        
        // Pass only assigned courses to the view
        return view('filament.student.pages.courses', compact('assignedCourses', 'assignedCourseIds', 'assignedDayIds', 'subjects', 'questions', 'answeredQuestionIds'));
    }

    public function questions(Request $request)
    {
        $courseId = $request->get('course');
        $subjectId = $request->get('subject');
        $dayId = $request->get('day');
        
        $user = auth()->user();
        $assignedCourseIds = $user->assignedCourses()->pluck('id')->toArray();
        $assignedDayIds = $user->assignedDays()->pluck('id')->toArray();
        $assignedSubjectIds = $user->assignedSubjects()->pluck('id')->toArray();
        
        // Check if student has access to this course and day
        if (!in_array($courseId, $assignedCourseIds)) {
            return redirect()->route('filament.student.pages.courses')
                ->with('error', 'You do not have access to this course. Please contact your administrator.');
        }
        
        if (!in_array($dayId, $assignedDayIds)) {
            return redirect()->route('filament.student.pages.courses')
                ->with('error', 'You do not have access to this day. Please contact your administrator.');
        }
        
        // Check if student has access to this subject (verification code expiration check)
        if (!in_array($subjectId, $assignedSubjectIds)) {
            return redirect()->route('filament.student.pages.courses')
                ->with('error', 'You do not have access to this subject. Your verification code may have expired. Please contact your administrator.');
        }
        
        // Get the course, subject, and day details
        $course = \App\Models\Course::findOrFail($courseId);
        $subject = \App\Models\Subject::findOrFail($subjectId);
        $day = \App\Models\Day::findOrFail($dayId);
        
        // Get questions for this specific combination, excluding those in tests
        $questions = \App\Models\Question::where('course_id', $courseId)
            ->where('subject_id', $subjectId)
            ->where('day_id', $dayId)
            ->where('is_active', true)
            // Only show questions not assigned to any active test
            ->whereDoesntHave('test', function($query) {
                $query->where('is_active', true);
            })
            ->orderBy('id')
            ->get();
        
        // Get answered question IDs and results for this user
        $answeredQuestionIds = [];
        $studentAnswers = [];
        if (auth()->check()) {
            $studentAnswers = \DB::table('student_answers')
                ->where('user_id', auth()->id())
                ->whereIn('question_id', $questions->pluck('id'))
                ->get();
            
            $answeredQuestionIds = $studentAnswers->pluck('question_id')->toArray();
        }
        
        // Calculate progress dynamically for this specific subject and day
        $totalQuestions = $questions->count();
        $completedQuestions = count($answeredQuestionIds);
        $progressPercentage = $totalQuestions > 0 ? round(($completedQuestions / $totalQuestions) * 100) : 0;
        
        // Check if all questions for this specific subject in the day are completed
        $allSubjectQuestionsCompleted = $this->checkAllSubjectQuestionsCompleted($courseId, $subjectId, $dayId, $user->id);
        
        // Calculate results if all subject questions are completed
        $subjectResults = null;
        if ($allSubjectQuestionsCompleted) {
            $subjectResults = $this->calculateSubjectResults($courseId, $subjectId, $dayId, $user->id);
        }
        
        return view('filament.student.pages.questions', compact(
            'course', 
            'subject', 
            'day', 
            'questions', 
            'answeredQuestionIds', 
            'studentAnswers',
            'allSubjectQuestionsCompleted', 
            'subjectResults',
            'totalQuestions',
            'completedQuestions',
            'progressPercentage'
        ));
    }
    
    private function checkAllSubjectQuestionsCompleted($courseId, $subjectId, $dayId, $userId)
    {
        $totalQuestions = \App\Models\Question::where('course_id', $courseId)
            ->where('subject_id', $subjectId)
            ->where('day_id', $dayId)
            ->where('is_active', true)
            ->count();
        
        $subjectQuestionIds = \App\Models\Question::where('course_id', $courseId)
            ->where('subject_id', $subjectId)
            ->where('day_id', $dayId)
            ->where('is_active', true)
            ->pluck('id');
        
        $answeredQuestions = \DB::table('student_answers')
            ->where('user_id', $userId)
            ->whereIn('question_id', $subjectQuestionIds)
            ->count();
        
        return $totalQuestions > 0 && $answeredQuestions >= $totalQuestions;
    }
    
    private function calculateSubjectResults($courseId, $subjectId, $dayId, $userId)
    {
        $questions = \App\Models\Question::where('course_id', $courseId)
            ->where('subject_id', $subjectId)
            ->where('day_id', $dayId)
            ->where('is_active', true)
            ->with('questionType')
            ->get();
        
        $studentAnswers = \DB::table('student_answers')
            ->where('user_id', $userId)
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');
        
        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        $totalPoints = 0;
        $earnedPoints = 0;
        
        foreach ($questions as $question) {
            $totalPoints += $question->points ?? 1;
            
            if (isset($studentAnswers[$question->id])) {
                $isCorrect = $studentAnswers[$question->id]->is_correct ?? false;
                if ($isCorrect) {
                    $correctAnswers++;
                    $earnedPoints += $question->points ?? 1;
                }
            }
        }
        
        $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
        
        return [
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $totalQuestions - $correctAnswers,
            'total_points' => $totalPoints,
            'earned_points' => $earnedPoints,
            'percentage' => $percentage,
            'grade' => $this->calculateGrade($percentage)
        ];
    }
    
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }
} 