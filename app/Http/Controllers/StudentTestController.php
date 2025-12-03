<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class StudentTestController extends Controller
{
    // Show all active tests for the student
    public function index(Request $request)
    {
        $user = auth()->user();
        $assignedCourseIds = $user->assignedCourses()->pluck('id')->toArray();
        $tests = Test::with('subject')
            ->where('is_active', true)
            ->whereIn('course_id', $assignedCourseIds)
            ->get();

        $testStats = [];
        foreach ($tests as $test) {
            $questions = $test->questions()->where('is_active', true)->get();
            $totalQuestions = $questions->count();
            $questionIds = $questions->pluck('id');
            $studentAnswers = \App\Models\StudentAnswer::where('user_id', $user->id)
                ->whereIn('question_id', $questionIds)
                ->get()->keyBy('question_id');
            $answeredCount = $studentAnswers->count();

            // Check for opinion questions
            $opinionQuestions = $questions->filter(function($q) {
                return $q->questionType && $q->questionType->name === 'opinion';
            });
            $opinionQuestionIds = $opinionQuestions->pluck('id');
            $hasOpinion = $opinionQuestions->count() > 0;
            $allOpinionVerified = true;
            if ($hasOpinion) {
                foreach ($opinionQuestionIds as $qid) {
                    $ans = $studentAnswers->get($qid);
                    if (!$ans || !in_array($ans->verification_status, ['verified_correct', 'verified_incorrect'])) {
                        $allOpinionVerified = false;
                        break;
                    }
                }
            }

            // Only show score/result if all opinion answers are verified (or no opinion questions)
            $score = null;
            $result = null;
            $earnedPoints = 0;
            
            if (!$hasOpinion || $allOpinionVerified) {
                // Calculate earned points from correct answers
                foreach ($questions as $question) {
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
                
                $totalPoints = $test->total_score; // Use test's total_score from admin panel
                $passmark = $test->passmark; // Use test's passmark from admin panel
                $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
                
                // Determine pass/fail based on earned points vs passmark (points)
                if ($answeredCount === $totalQuestions) {
                    $result = $earnedPoints >= $passmark ? 'Pass' : 'Fail';
                } else {
                    $result = null; // Not completed yet
                }
                
                // For display in the listing, show points format
                $score = $earnedPoints . '/' . $totalPoints;
            } else {
                $result = 'Pending';
            }

            $status = $answeredCount === 0 ? 'Not Started' : ($answeredCount === $totalQuestions ? 'Completed' : 'In Progress');

            $testStats[$test->id] = [
                'total_questions' => $totalQuestions,
                'answered' => $answeredCount,
                'status' => $status,
                'result' => $result,
                'score' => $score,
            ];
        }

        return view('filament.student.pages.tests', [
            'tests' => $tests,
            'testStats' => $testStats,
        ]);
    }

    // Show a specific test and its subject-wise questions
    public function show(Test $test)
    {
        $questions = $test->questions()->with('subject', 'questionType')->where('is_active', true)->get();
        $questionsBySubject = $questions->groupBy(function($q) {
            return $q->subject ? $q->subject->name : 'General';
        });
        
        $user = auth()->user();
        $questionIds = $questions->pluck('id');
        $studentAnswers = StudentAnswer::where('user_id', $user->id)
            ->whereIn('question_id', $questionIds)
            ->get()->keyBy('question_id');
        
        $totalQuestions = $questions->count();
        $answeredCount = $studentAnswers->count();

        // Check for opinion questions
        $opinionQuestions = $questions->filter(function($q) {
            return $q->questionType && $q->questionType->name === 'opinion';
        });
        $opinionQuestionIds = $opinionQuestions->pluck('id');
        $hasOpinion = $opinionQuestions->count() > 0;
        $allOpinionVerified = true;
        
        if ($hasOpinion) {
            foreach ($opinionQuestionIds as $qid) {
                $ans = $studentAnswers->get($qid);
                if (!$ans || !in_array($ans->verification_status, ['verified_correct', 'verified_incorrect'])) {
                    $allOpinionVerified = false;
                    break;
                }
            }
        }

        // Only show score/result if all opinion answers are verified (or no opinion questions)
        $score = null;
        $result = null;
        $earnedPoints = 0;
        $totalPoints = $test->total_score; // Always define totalPoints
        $passmark = $test->passmark; // Always define passmark
        $percentage = 0; // Initialize percentage
        
        if (!$hasOpinion || $allOpinionVerified) {
            // Calculate earned points from correct answers
            foreach ($questions as $question) {
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
            
            $score = $earnedPoints; // Use earned points
            
            // Calculate percentage for display purposes
            $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
            
            // Determine pass/fail based on earned points vs passmark (points)
            if ($answeredCount === $totalQuestions) {
                $result = $earnedPoints >= $passmark ? 'Pass' : 'Fail';
            } else {
                $result = null; // Not completed yet
            }
        } else {
            // If any opinion answer is pending, show Pending
            $result = 'Pending';
        }

        // Get unanswered questions
        $unansweredQuestions = $questions->filter(function($question) use ($studentAnswers) {
            return !$studentAnswers->has($question->id);
        });

        return view('filament.student.pages.test-detail', [
            'test' => $test,
            'questionsBySubject' => $questionsBySubject,
            'questions' => $questions,
            'unansweredQuestions' => $unansweredQuestions,
            'studentAnswers' => $studentAnswers, // Add this line
            'progress' => $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100, 2) : 0,
            'score' => $score,
            'earnedPoints' => $earnedPoints,
            'totalPoints' => $totalPoints,
            'passmark' => $passmark,
            'percentage' => $percentage,
            'result' => $result,
            'answeredCount' => $answeredCount,
            'totalQuestions' => $totalQuestions
        ]);
    }

    // Show a specific question in a test
    public function question(Request $request, Test $test, Question $question)
    {
        $user = auth()->user();
        $studentAnswer = StudentAnswer::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->first();
        
        // Determine view mode based on existing answer with submitted data
        $isViewMode = $studentAnswer !== null && 
            ($studentAnswer->answer_data !== null || 
             $studentAnswer->submitted_at !== null);
        
        // If no previous answer, create a placeholder to avoid null errors
        if (!$studentAnswer) {
            $studentAnswer = new StudentAnswer([
                'user_id' => $user->id,
                'question_id' => $question->id,
                'answer_data' => null,
                'is_correct' => null
            ]);
        }
        
        return view('filament.student.pages.test-question', [
            'test' => $test,
            'question' => $question,
            'studentAnswer' => $studentAnswer,
            'isViewMode' => $isViewMode
        ]);
    }

    // Handle answer submission for a test question
    public function submitAnswer(Request $request, Test $test, Question $question)
    {
        $user = auth()->user();
        
        // Log the entire request for debugging
        \Log::info('Test Answer Submission', [
            'user_id' => $user->id,
            'test_id' => $test->id,
            'question_id' => $question->id,
            'question_type' => $question->questionType->name ?? 'unknown',
            'request_data' => $request->all()
        ]);
        
        // Validate the request
        $validationRules = $this->getValidationRules($question);
        $data = $request->validate($validationRules, $this->getValidationMessages());
        
        // Normalize answer data
        $studentAnswer = $this->normalizeAnswer($question, $data['answer'] ?? null);
        
        // Evaluate the answer
        $isCorrect = $this->checkAnswerCorrectness($question, $studentAnswer);
        
        // Prepare answer data for storage
        $updateData = [
            'answer_data' => json_encode($studentAnswer),
            'submitted_at' => now(),
            'is_correct' => $isCorrect,
            'user_id' => $user->id,
            'question_id' => $question->id
        ];
        
        // Handle opinion questions
        $isOpinion = $question->questionType && $question->questionType->name === 'opinion';
        if ($isOpinion) {
            $updateData['verification_status'] = 'pending';
        }
        
        // Save or update the answer
        $studentAnswer = StudentAnswer::updateOrCreate(
            [
                'user_id' => $user->id,
                'question_id' => $question->id
            ],
            $updateData
        );
        
        // Find next unanswered question
        $nextQuestion = $test->questions()->where('is_active', true)
            ->whereDoesntHave('studentAnswers', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orderBy('id')->first();
        
        // Redirect logic
        if ($nextQuestion) {
            return redirect()->route('filament.student.pages.tests.question', [$test, $nextQuestion])
                ->with('success', 'Answer submitted!');
        } else {
            return redirect()->route('filament.student.pages.tests.show', $test)
                ->with('success', 'Test completed!');
        }
    }

    // Add a method to list test questions
    public function testQuestions(Request $request)
    {
        $user = auth()->user();
        $assignedCourseIds = $user->assignedCourses()->pluck('id')->toArray();
        
        // Get active tests for the student's assigned courses
        $tests = Test::with('subject')
            ->where('is_active', true)
            ->whereIn('course_id', $assignedCourseIds)
            ->get();
        
        // Prepare test statistics
        $testStats = [];
        foreach ($tests as $test) {
            $questions = $test->questions()->where('is_active', true)->get();
            $totalQuestions = $questions->count();
            $questionIds = $questions->pluck('id');
            
            $studentAnswers = StudentAnswer::where('user_id', $user->id)
                ->whereIn('question_id', $questionIds)
                ->get()->keyBy('question_id');
            
            $answeredCount = $studentAnswers->count();
            
            $status = $answeredCount === 0 ? 'Not Started' : 
                      ($answeredCount === $totalQuestions ? 'Completed' : 'In Progress');
            
            $testStats[$test->id] = [
                'test' => $test,
                'total_questions' => $totalQuestions,
                'answered' => $answeredCount,
                'status' => $status,
            ];
        }
        
        return view('filament.student.pages.test-questions', [
            'testStats' => $testStats,
        ]);
    }

    // Get validation rules for a specific question
    private function getValidationRules(Question $question)
    {
        $questionType = $question->questionType->name ?? '';
        $isSpeakingSubject = $question->subject && strtolower($question->subject->name) === 'speaking';
        
        $rules = [];
        
        switch ($questionType) {
            case 'mcq_single':
                $rules = ['answer' => 'required|numeric'];
                break;
            case 'true_false':
                $rules = ['answer' => 'required|in:true,false'];
                break;
            case 'mcq_multiple':
                $rules = ['answer' => 'required|array'];
                break;
            case 'true_false_multiple':
                $rules = ['answer' => 'required|array'];
                break;
            case 'form_fill':
                $rules = ['answer' => 'required|array'];
                break;
            case 'audio_mcq_single':
                $rules = ['answer' => 'required|array'];
                break;
            case 'picture_mcq':
                $rules = ['answer' => 'required|array'];
                break;
            case 'audio_image_text_single':
                $rules = ['answer' => 'required|array'];
                break;
            case 'audio_image_text_multiple':
                $rules = ['answer' => 'required|array'];
                break;
            case 'audio_picture_match':
                $rules = ['answer' => 'required|array'];
                break;
            case 'audio_fill_blank':
                $rules = ['answer' => 'required|array'];
                break;
            case 'picture_fill_blank':
                $rules = ['answer' => 'required|array'];
                break;
            case 'video_fill_blank':
                $rules = ['answer' => 'required|array'];
                break;
            case 'reorder':
                $rules = ['answer' => 'required'];
                break;
            case 'statement_match':
                $rules = ['answer' => 'required|array'];
                break;
            case 'opinion':
                $rules = [
                    'answer' => $isSpeakingSubject ? 'nullable' : 'required_without:audio_video_file',
                    'audio_video_file' => $isSpeakingSubject 
                        ? 'nullable|file|mimes:mp3,wav,mp4,webm,ogg,m4a|max:51200' 
                        : 'nullable'
                ];
                break;
            default:
                $rules = ['answer' => 'required'];
        }
        
        return $rules;
    }

    // Get validation error messages
    private function getValidationMessages()
    {
        return [
            'answer.required' => 'Please select an answer.',
            'answer.numeric' => 'Please select a valid option.',
            'answer.in' => 'Please select either True or False.',
            'answer.array' => 'Please provide a valid answer.',
            'audio_video_file.max' => 'The file must not exceed 50MB.',
            'audio_video_file.mimes' => 'The file must be an audio or video file.',
            'answer.required_without' => 'Please provide either a written or audio/video response.'
        ];
    }

    // Normalize answer data based on question type
    private function normalizeAnswer(Question $question, $answer)
    {
        if ($answer === null) {
            return null;
        }
        
        $questionType = $question->questionType->name ?? '';
        
        // Ensure array for multi-answer types
        $multiTypes = [
            'mcq_multiple', 
            'true_false_multiple', 
            'form_fill', 
            'statement_match',
            'audio_mcq_single',
            'picture_mcq',
            'audio_image_text_single',
            'audio_image_text_multiple',
            'audio_picture_match',
            'audio_fill_blank',
            'picture_fill_blank',
            'video_fill_blank'
        ];
        
        // Special handling for MCQ Single to ensure numeric index
        if ($questionType === 'mcq_single') {
            return is_array($answer) ? (int)($answer[0] ?? -1) : (int)$answer;
        }
        
        if (in_array($questionType, $multiTypes)) {
            return is_array($answer) ? $answer : [$answer];
        }
        
        // Single answer types
        return is_array($answer) ? (count($answer) === 1 ? $answer[0] : $answer) : $answer;
    }

    // Check if the answer is correct (unified with course logic)
    private function checkAnswerCorrectness(Question $question, $studentAnswer)
    {
        $correctAnswer = is_string($question->answer_data) ? json_decode($question->answer_data, true) : $question->answer_data;
        $questionType = $question->questionType->name ?? '';

        switch ($questionType) {
            case 'mcq_single':
                $correctIndices = $correctAnswer['correct_indices'] ?? [];
                $studentIndex = is_array($studentAnswer) ? (int)($studentAnswer[0] ?? -1) : (int)$studentAnswer;
                return in_array($studentIndex, $correctIndices);
            case 'true_false':
                $correctAnswer_tf = $correctAnswer['correct_answer'] ?? '';
                $studentAnswer_tf = is_array($studentAnswer) ? ($studentAnswer[0] ?? '') : $studentAnswer;
                return strtolower(trim($correctAnswer_tf)) === strtolower(trim($studentAnswer_tf));
            case 'mcq_multiple':
                $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                $subQuestions = $questionData['sub_questions'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                $allCorrect = true;
                foreach ($subQuestions as $subIndex => $subQuestion) {
                    $correctIndices = $subQuestion['correct_indices'] ?? [];
                    $studentIndices = isset($studentAnswers[$subIndex]) ? array_map('intval', $studentAnswers[$subIndex]) : [];
                    sort($correctIndices);
                    sort($studentIndices);
                    if ($correctIndices !== $studentIndices) {
                        $allCorrect = false;
                        break;
                    }
                }
                return $allCorrect;
            case 'form_fill':
            case 'audio_fill_blank':
            case 'picture_fill_blank':
            case 'video_fill_blank':
                $correctAnswers = $correctAnswer['answer_keys'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? array_values($studentAnswer) : [$studentAnswer];
                if (count($correctAnswers) !== count($studentAnswers)) {
                    return false;
                }
                for ($i = 0; $i < count($correctAnswers); $i++) {
                    if (strtolower(trim($correctAnswers[$i])) !== strtolower(trim($studentAnswers[$i] ?? ''))) {
                        return false;
                    }
                }
                return true;
            case 'reorder':
                $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                $fragments = $question->reorder_fragments ?? $questionData['fragments'] ?? [];
                $correctSentence = $question->reorder_answer_key ?? '';
                
                // Parse student answer (numeric indices like "1,2,3,4" or "1, 2, 3, 4")
                $studentOrder = is_array($studentAnswer) ? implode(',', $studentAnswer) : $studentAnswer;
                $studentOrder = trim($studentOrder);
                
                // Convert student's numeric order to actual text sequence
                $studentSequence = '';
                if (!empty($fragments) && !empty($studentOrder)) {
                    // Split by comma and clean up
                    $orderArray = array_map('trim', explode(',', $studentOrder));
                    $orderArray = array_filter($orderArray, function($item) {
                        return !empty($item) && is_numeric($item);
                    });
                    
                    $studentFragments = [];
                    foreach ($orderArray as $orderNum) {
                        $index = (int)$orderNum - 1; // Convert to 0-based
                        if (isset($fragments[$index])) {
                            $studentFragments[] = $fragments[$index];
                        }
                    }
                    $studentSequence = implode(' ', $studentFragments);
                }
                
                // Compare the student's reconstructed sentence with the correct sentence
                // Normalize by removing extra spaces, punctuation differences, and converting to lowercase
                $studentNormalized = preg_replace('/[^\w\s]/u', '', preg_replace('/\s+/', ' ', strtolower(trim($studentSequence))));
                $correctNormalized = preg_replace('/[^\w\s]/u', '', preg_replace('/\s+/', ' ', strtolower(trim($correctSentence))));
                
                return $studentNormalized === $correctNormalized;
            case 'statement_match':
                $correctPairs = $question->correct_pairs ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                $studentPairs = [];
                foreach ($studentAnswers as $leftIndex => $rightIndex) {
                    if (!empty($rightIndex) && is_numeric($rightIndex)) {
                        $studentPairs[] = [
                            'left' => (int)$leftIndex,
                            'right' => (int)$rightIndex - 1
                        ];
                    }
                }
                $allCorrect = true;
                foreach ($correctPairs as $correctPair) {
                    $found = false;
                    foreach ($studentPairs as $studentPair) {
                        if ($studentPair['left'] === $correctPair['left'] && $studentPair['right'] === $correctPair['right']) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $allCorrect = false;
                    }
                }
                if (count($studentPairs) !== count($correctPairs)) {
                    $allCorrect = false;
                }
                return $allCorrect;
            case 'audio_picture_match':
                $correctPairs = $correctAnswer['correct_pairs'] ?? [];
                $studentPairs = is_array($studentAnswer) ? $studentAnswer : [];
                $allMatched = true;
                foreach ($correctPairs as $correctPair) {
                    $leftIndex = $correctPair['left'] ?? '';
                    $rightIndex = $correctPair['right'] ?? '';
                    if (!isset($studentPairs[$leftIndex]) || $studentPairs[$leftIndex] != $rightIndex) {
                        $allMatched = false;
                        break;
                    }
                }
                return $allMatched;
            case 'picture_mcq':
            case 'audio_image_text_single':
            case 'audio_image_text_multiple':
                $correctPairs = $correctAnswer['correct_pairs'] ?? [];
                $studentPairs = is_array($studentAnswer) ? $studentAnswer : [];
                
                // Check if all required matches are made
                $allMatched = true;
                foreach ($correctPairs as $correctPair) {
                    $leftIndex = $correctPair['left'] ?? '';
                    $rightIndex = $correctPair['right'] ?? '';
                    
                    // Handle both associative and sequential arrays
                    $studentImageIndex = null;
                    if (isset($studentPairs[$leftIndex])) {
                        $studentImageIndex = $studentPairs[$leftIndex];
                    } elseif (isset($studentPairs[(int)$leftIndex])) {
                        $studentImageIndex = $studentPairs[(int)$leftIndex];
                    }
                    
                    // Convert to string for comparison since form data might be strings
                    $studentImageIndexStr = $studentImageIndex !== null ? (string)$studentImageIndex : '';
                    $expectedImageIndex = (string)$rightIndex;
                    
                    if ($studentImageIndexStr !== $expectedImageIndex) {
                        $allMatched = false;
                        break;
                    }
                }
                
                return $allMatched;
            case 'audio_mcq_single':
                $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                $subQuestions = $questionData['sub_questions'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                $allCorrect = true;
                foreach ($subQuestions as $subIndex => $subQuestion) {
                    $correctIndices = $subQuestion['correct_indices'] ?? [];
                    $studentIndex = isset($studentAnswers[$subIndex]) ? (int)$studentAnswers[$subIndex] : -1;
                    if (!in_array($studentIndex, $correctIndices)) {
                        $allCorrect = false;
                        break;
                    }
                }
                return $allCorrect;
            case 'true_false_multiple':
                $correctAnswerData = is_string($question->answer_data) ? json_decode($question->answer_data, true) : ($question->answer_data ?? []);
                $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                $correctAnswers = $correctAnswerData['true_false_answers'] ?? $question->true_false_questions ?? $questionData['questions'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                if (empty($correctAnswers) || empty($studentAnswers)) {
                    return false;
                }
                if (count($correctAnswers) !== count($studentAnswers)) {
                    return false;
                }
                $allCorrect = true;
                foreach ($correctAnswers as $qIndex => $correctAnswerItem) {
                    $correctAnswer_tf = '';
                    if (is_array($correctAnswerItem)) {
                        $correctAnswer_tf = $correctAnswerItem['correct_answer'] ?? $correctAnswerItem['answer'] ?? '';
                    } else {
                        $correctAnswer_tf = $correctAnswerItem;
                    }
                    $studentAnswer_tf = $studentAnswers[$qIndex] ?? '';
                    if (empty($studentAnswer_tf)) {
                        $allCorrect = false;
                        break;
                    }
                    if (strtolower(trim($correctAnswer_tf)) !== strtolower(trim($studentAnswer_tf))) {
                        $allCorrect = false;
                        break;
                    }
                }
                return $allCorrect;
            case 'opinion':
                return true;
            default:
                return false;
        }
    }
} 