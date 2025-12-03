<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class OpinionVerification extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.student.pages.opinion-verification';

    protected static ?string $title = 'Opinion Verification';

    protected static ?string $navigationLabel = 'Opinion Verification';

    protected static ?string $slug = 'opinion-verification';

    public static function getRouteName(?string $panel = null): string
    {
        return 'filament.student.pages.opinion-verification';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        
        // Get assigned courses and days
        $assignedCourses = $user->assignedCourses();
        $assignedDays = $user->assignedDays();
        $assignedDayIds = $assignedDays->pluck('id')->toArray();
        $assignedCourseIds = $assignedCourses->pluck('id')->toArray();
        
        // Get all opinion type questions that the student has answered
        $opinionAnswers = \App\Models\StudentAnswer::where('user_id', $user->id)
            ->whereHas('question', function($query) use ($assignedCourseIds, $assignedDayIds) {
                $query->whereIn('course_id', $assignedCourseIds)
                      ->whereIn('day_id', $assignedDayIds)
                      ->whereHas('questionType', function($q) {
                          $q->where('name', 'opinion');
                      });
            })
            ->with(['question.subject', 'question.course', 'question.day', 'verifiedBy'])
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        // Get statistics
        $totalOpinionQuestions = $opinionAnswers->count();
        $pendingVerification = $opinionAnswers->where('verification_status', 'pending')->count();
        $verifiedCorrect = $opinionAnswers->where('verification_status', 'verified_correct')->count();
        $verifiedIncorrect = $opinionAnswers->where('verification_status', 'verified_incorrect')->count();
        
        return [
            'user' => $user,
            'assignedCourses' => $assignedCourses,
            'assignedDays' => $assignedDays,
            'opinionAnswers' => $opinionAnswers,
            'totalOpinionQuestions' => $totalOpinionQuestions,
            'pendingVerification' => $pendingVerification,
            'verifiedCorrect' => $verifiedCorrect,
            'verifiedIncorrect' => $verifiedIncorrect,
        ];
    }
}
