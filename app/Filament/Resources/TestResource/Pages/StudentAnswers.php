<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use App\Models\Test;
use App\Models\User;
use App\Models\StudentAnswer;
use Illuminate\Support\Collection;

class StudentAnswers extends Page
{
    protected static string $resource = TestResource::class;

    protected static string $view = 'filament.resources.test-resource.pages.student-answers';

    public $test;
    public $student;
    public $studentAnswers;
    public $testResults;

    public function mount($record, $student): void
    {
        $this->test = Test::findOrFail($record);
        $this->student = User::findOrFail($student);
        
        $this->loadStudentAnswers();
        $this->calculateTestResults();
    }

    protected function loadStudentAnswers(): void
    {
        $testQuestionIds = $this->test->questions()->where('is_active', true)->pluck('id');
        
        $this->studentAnswers = StudentAnswer::where('user_id', $this->student->id)
            ->whereIn('question_id', $testQuestionIds)
            ->with(['question.questionType'])
            ->get()
            ->keyBy('question_id');
    }

    protected function calculateTestResults(): void
    {
        $questions = $this->test->questions()->where('is_active', true)->get();
        $totalQuestions = $questions->count();
        $answeredCount = $this->studentAnswers->count();
        
        // Check for opinion questions
        $opinionQuestions = $questions->filter(function($q) {
            return $q->questionType && $q->questionType->name === 'opinion';
        });
        
        $hasOpinion = $opinionQuestions->count() > 0;
        $allOpinionVerified = true;
        
        if ($hasOpinion) {
            foreach ($opinionQuestions as $question) {
                $ans = $this->studentAnswers->get($question->id);
                if (!$ans || !in_array($ans->verification_status, ['verified_correct', 'verified_incorrect'])) {
                    $allOpinionVerified = false;
                    break;
                }
            }
        }
        
        $earnedPoints = 0;
        $correctAnswers = 0;
        
        foreach ($questions as $question) {
            if ($this->studentAnswers->has($question->id)) {
                $studentAnswer = $this->studentAnswers->get($question->id);
                $isCorrect = false;
                
                if ($question->questionType && $question->questionType->name === 'opinion') {
                    $isCorrect = $studentAnswer->verification_status === 'verified_correct';
                } else {
                    $isCorrect = $studentAnswer->is_correct === true;
                }
                
                if ($isCorrect) {
                    $correctAnswers++;
                    $earnedPoints += $question->points ?? 1;
                }
            }
        }
        
        $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
        $result = null;
        
        if ($answeredCount === $totalQuestions) {
            if (!$hasOpinion || $allOpinionVerified) {
                $result = $earnedPoints >= $this->test->passmark ? 'Pass' : 'Fail';
            } else {
                $result = 'Pending';
            }
        }
        
        $this->testResults = [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredCount,
            'correct_answers' => $correctAnswers,
            'earned_points' => $earnedPoints,
            'total_points' => $this->test->total_score,
            'passmark' => $this->test->passmark,
            'percentage' => $percentage,
            'result' => $result,
            'status' => $answeredCount === 0 ? 'Not Started' : 
                       ($answeredCount === $totalQuestions ? 'Completed' : 'In Progress'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_submissions')
                ->label('Back to Submissions')
                ->url(route('filament.admin.resources.tests.submissions', $this->test))
                ->color('gray'),
        ];
    }

    public function getTitle(): string
    {
        return "Student Answers: {$this->student->name} - {$this->test->name}";
    }
} 