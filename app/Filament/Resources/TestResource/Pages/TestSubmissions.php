<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use App\Models\StudentAnswer;
use App\Models\User;
use App\Models\Test;
use Illuminate\Support\Facades\DB;

class TestSubmissions extends Page
{
    protected static string $resource = TestResource::class;

    protected static string $view = 'filament.resources.test-resource.pages.test-submissions';

    public $record;

    public function mount($record): void
    {
        $this->record = Test::with(['course', 'subject'])->findOrFail($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_test')
                ->label('Back to Test')
                ->url(route('filament.admin.resources.tests.view', $this->record))
                ->color('gray'),
        ];
    }

    public function getTitle(): string
    {
        return "Test Submissions: {$this->record->name}";
    }

    public function getSubmissions()
    {
        $test = $this->record;
        $testQuestionIds = $test->questions()->where('is_active', true)->pluck('id');
        
        // Get all students assigned to this test's course through their batch
        $assignedStudents = User::whereHas('batch.courses', function ($query) use ($test) {
            $query->where('courses.id', $test->course_id);
        })->get();

        $submissions = collect();
        
        foreach ($assignedStudents as $student) {
            $studentAnswers = StudentAnswer::where('user_id', $student->id)
                ->whereIn('question_id', $testQuestionIds)
                ->get()
                ->keyBy('question_id');
            
            $totalQuestions = $test->questions()->where('is_active', true)->count();
            $answeredCount = $studentAnswers->count();
            
            // Check for opinion questions
            $opinionQuestions = $test->questions()->where('is_active', true)
                ->whereHas('questionType', function ($query) {
                    $query->where('name', 'opinion');
                })->get();
            
            $hasOpinion = $opinionQuestions->count() > 0;
            $allOpinionVerified = true;
            
            if ($hasOpinion) {
                foreach ($opinionQuestions as $question) {
                    $ans = $studentAnswers->get($question->id);
                    if (!$ans || !in_array($ans->verification_status, ['verified_correct', 'verified_incorrect'])) {
                        $allOpinionVerified = false;
                        break;
                    }
                }
            }
            
            // Only include students who have actually started the test (answered at least one question)
            if ($answeredCount > 0) {
                // Calculate status and result
                $status = 'not_started';
                $result = null;
                $score = null;
                
                if ($answeredCount === $totalQuestions) {
                    $status = 'completed';
                    
                    if (!$hasOpinion || $allOpinionVerified) {
                        // Calculate earned points
                        $earnedPoints = 0;
                        foreach ($test->questions()->where('is_active', true)->get() as $question) {
                            if ($studentAnswers->has($question->id)) {
                                $studentAnswer = $studentAnswers->get($question->id);
                                $isCorrect = false;
                                
                                if ($question->questionType && $question->questionType->name === 'opinion') {
                                    $isCorrect = $studentAnswer->verification_status === 'verified_correct';
                                } else {
                                    $isCorrect = $studentAnswer->is_correct === true;
                                }
                                
                                if ($isCorrect) {
                                    $earnedPoints += $question->points ?? 1;
                                }
                            }
                        }
                        
                        $score = $earnedPoints . '/' . $test->total_score;
                        $result = $earnedPoints >= $test->passmark ? 'Pass' : 'Fail';
                    } else {
                        $result = 'Pending';
                    }
                } elseif ($answeredCount > 0) {
                    $status = 'in_progress';
                }
                
                $submissions->push((object) [
                    'user' => $student,
                    'user_id' => $student->id,
                    'submitted_at' => $studentAnswers->max('submitted_at'),
                    'status' => $status,
                    'score' => $score,
                    'result' => $result,
                ]);
            }
        }
        
        return $submissions->sortByDesc('submitted_at');
    }
} 