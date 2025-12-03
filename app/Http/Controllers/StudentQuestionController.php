<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class StudentQuestionController extends Controller
{
    public function answer($id, Request $request)
    {
        $question = \App\Models\Question::with('questionType', 'subject')->findOrFail($id);
        
        // Check if student has access to this course and day through their batch
        $user = auth()->user();
        $assignedCourseIds = $user->assignedCourses()->pluck('id')->toArray();
        $assignedDayIds = $user->assignedDays()->pluck('id')->toArray();
        $assignedSubjectIds = $user->assignedSubjects()->pluck('id')->toArray();
        
        if (!in_array($question->course_id, $assignedCourseIds)) {
            return redirect()->route('filament.student.pages.courses')
                ->with('error', 'You do not have access to this course. Please contact your administrator.');
        }
        
        if (!in_array($question->day_id, $assignedDayIds)) {
            return redirect()->route('filament.student.pages.courses')
                ->with('error', 'You do not have access to this day. Please contact your administrator.');
        }
        
        // Check if student has access to this subject (verification code expiration check)
        if (!in_array($question->subject_id, $assignedSubjectIds)) {
            return redirect()->route('filament.student.pages.courses')
                ->with('error', 'You do not have access to this subject. Your verification code may have expired. Please contact your administrator.');
        }
        
        // Determine if this is a test or course question
        $isTest = $request->input('is_test', false);
        
        // Always allow re-attempt, reset all previous answer checks
        $editMode = true;
        $studentAnswer = null;
        $isReAttempt = true;
        
        // Check for existing answer (especially important for opinion questions)
        $existingAnswer = null;
        $answer = null;
        $submittedAnswer = null;
        $questionType = $question->questionType->name ?? '';
        
        // Get the most recent submitted answer for this question
        $submittedAnswer = \DB::table('student_answers')
            ->where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($questionType === 'opinion' && $submittedAnswer) {
            $existingAnswer = $submittedAnswer;
            
            // Set the answer variable for pre-filling the form
            if ($existingAnswer) {
                $answer = is_string($existingAnswer->answer_data) ? json_decode($existingAnswer->answer_data, true) : $existingAnswer->answer_data;
            }
        }
        
        return view('filament.student.pages.answer-question', compact(
            'question', 
            'studentAnswer', 
            'editMode', 
            'isReAttempt',
            'existingAnswer',
            'answer',
            'submittedAnswer'
        ));
    }

    public function submitAnswer(Request $request, $id)
    {
        // Extensive logging for debugging
        \Log::info('Submit Answer Request', [
            'route_name' => $request->route()->getName(),
            'route_parameters' => $request->route()->parameters(),
            'all_input' => $request->all(),
            'id' => $id
        ]);
        
        $question = \App\Models\Question::with('questionType', 'subject')->findOrFail($id);
        
        // Check if student has access to this course and day through their batch
        $user = auth()->user();
        $assignedCourseIds = $user->assignedCourses()->pluck('id')->toArray();
        $assignedDayIds = $user->assignedDays()->pluck('id')->toArray();
        
        // Debug access control
        \Log::info('Access Control Debug', [
            'user_id' => $user->id,
            'question_course_id' => $question->course_id,
            'question_day_id' => $question->day_id,
            'assigned_course_ids' => $assignedCourseIds,
            'assigned_day_ids' => $assignedDayIds,
            'has_course_access' => in_array($question->course_id, $assignedCourseIds),
            'has_day_access' => in_array($question->day_id, $assignedDayIds)
        ]);
        
        if (!in_array($question->course_id, $assignedCourseIds)) {
            \Log::warning('Access Denied - Course', [
                'user_id' => $user->id,
                'course_id' => $question->course_id,
                'assigned_courses' => $assignedCourseIds
            ]);
            return redirect()->route('filament.student.pages.courses')
                ->with('error', 'You do not have access to this course. Please contact your administrator.');
        }
        
        if (!in_array($question->day_id, $assignedDayIds)) {
            \Log::warning('Access Denied - Day', [
                'user_id' => $user->id,
                'day_id' => $question->day_id,
                'assigned_days' => $assignedDayIds
            ]);
            return redirect()->route('filament.student.pages.courses')
                ->with('error', 'You do not have access to this day. Please contact your administrator.');
        }
        
        // Normalize re-attempt flag
        $isReAttempt = filter_var($request->input('is_reattempt', false), FILTER_VALIDATE_BOOLEAN);
        
        // For re-attempts, check if the answer has actually changed
        if ($isReAttempt) {
            $existingAnswer = \DB::table('student_answers')
                ->where('user_id', $user->id)
                ->where('question_id', $question->id)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($existingAnswer) {
                $existingAnswerData = json_decode($existingAnswer->answer_data, true);
                $newAnswerData = $data['answer'] ?? null;
                
                // If answer hasn't changed, show a message but allow submission
                if ($existingAnswerData == $newAnswerData) {
                    \Log::info('Re-attempt with same answer', [
                        'question_id' => $question->id,
                        'existing_answer' => $existingAnswerData,
                        'new_answer' => $newAnswerData
                    ]);
                }
            }
        }
        
        // Identify question type
        $questionType = $question->questionType->name ?? '';
        $isSpeakingSubject = $question->subject && strtolower($question->subject->name) === 'speaking';
        $isOpinionQuestion = $questionType === 'opinion';
        
        // Validation rules
        $validationRules = $this->getValidationRules($questionType, $isSpeakingSubject);
        
        // Debug validation
        \Log::info('Validation Debug', [
            'question_type' => $questionType,
            'validation_rules' => $validationRules,
            'request_data' => $request->all(),
            'is_speaking_subject' => $isSpeakingSubject
        ]);
        
        // Validate the request
        try {
            $data = $request->validate($validationRules, $this->getValidationMessages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Failed', [
                'errors' => $e->errors(),
                'question_id' => $question->id,
                'question_type' => $questionType
            ]);
            throw $e;
        }
        
        // Normalize answer data
        $studentAnswer = $this->normalizeAnswer($questionType, $data['answer'] ?? null);
        
        // Debug logging for answer processing
        \Log::info('Answer Processing Debug', [
            'question_type' => $questionType,
            'raw_answer_data' => $data['answer'] ?? null,
            'raw_answer_keys' => is_array($data['answer'] ?? null) ? array_keys($data['answer'] ?? []) : 'not_array',
            'normalized_answer' => $studentAnswer,
            'normalized_keys' => is_array($studentAnswer) ? array_keys($studentAnswer) : 'not_array',
            'is_array' => is_array($studentAnswer),
            'answer_count' => is_array($studentAnswer) ? count($studentAnswer) : 1,
            'validation_rules' => $validationRules,
            'is_reattempt' => $isReAttempt
        ]);
        
        // Handle file upload for speaking/opinion questions
        $fileUploadPath = $this->handleFileUpload($request, $isOpinionQuestion, $isSpeakingSubject);
        
        // Evaluate the answer
        $isCorrect = $this->evaluateAnswer($question, $studentAnswer);
        
        // Debug logging for evaluation
        \Log::info('Answer Evaluation Debug', [
            'question_type' => $questionType,
            'student_answer' => $studentAnswer,
            'is_correct' => $isCorrect,
            'question_id' => $question->id
        ]);
        
        $result = $this->getAnswerResult($question, $studentAnswer, $isCorrect);
        
        // Debug logging for result generation
        \Log::info('Result Generation Debug', [
            'question_type' => $questionType,
            'result' => $result,
            'student_answer_text' => $result['student_answer_text'] ?? null,
            'correct_answer_text' => $result['correct_answer_text'] ?? null
        ]);
        
        // Prepare answer data for storage
        $answerData = [
            'answer_data' => json_encode($studentAnswer),
            'is_correct' => $isCorrect,
            'submitted_at' => now(),
            'updated_at' => now(),
            'created_at' => now(),
            'is_reattempt' => $isReAttempt,
        ];

        // Add file upload path if exists
        if ($fileUploadPath) {
            $answerData['file_upload'] = $fileUploadPath;
        }

        // For opinion questions, set verification status to pending
        if ($isOpinionQuestion) {
            $answerData['verification_status'] = 'pending';
        }

        // Debug database insertion
        \Log::info('Database Insertion Debug', [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'answer_data' => $answerData,
            'is_reattempt' => $isReAttempt
        ]);
        
        // Save or update the answer
        try {
            \DB::table('student_answers')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'question_id' => $question->id,
                ],
                $answerData
            );
            \Log::info('Database Insertion Success');
        } catch (\Exception $e) {
            \Log::error('Database Insertion Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        \Log::info('Starting Redirect Process');
        
        // Set the answer result in session for the result modal
        session([
            'answer_result' => $result,
            'question_id' => $question->id,
            'course_id' => $question->course_id,
            'subject_id' => $question->subject_id,
            'day_id' => $question->day_id,
            'show_result_modal' => true
        ]);
        
        // For all question types, redirect back to the same question page to show result modal
        $sessionData = [];
        
        if ($result['is_correct']) {
            $sessionData['success'] = 'Correct Answer! Well done!';
        } else {
            $sessionData['info'] = $result['message'] . ' You can re-attempt this question if needed.';
        }
        
        if ($isOpinionQuestion) {
            $sessionData['info'] = 'Your opinion has been submitted and is awaiting verification by your teacher.';
        }
        
        // Debug logging
        \Log::info('Result Modal Redirect Debug', [
            'question_id' => $question->id,
            'result' => $result,
            'session_data' => $sessionData
        ]);
        
        // Redirect back to the same question page to show result modal
        return redirect()->route('student.questions.answer', ['id' => $question->id])->with($sessionData);
    }

    public function clearResultModal(Request $request)
    {
        // Clear the show_result_modal flag from session
        session()->forget('show_result_modal');
        return response()->json(['success' => true]);
    }

    private function evaluateAnswer($question, $studentAnswer)
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
                
                \Log::info('Reorder Evaluation', [
                    'fragments' => $fragments,
                    'correctSentence' => $correctSentence,
                    'studentAnswer' => $studentAnswer
                ]);
                
                // Parse student answer (numeric indices like "1,2,3,4" or "1, 2, 3, 4")
                $studentOrder = is_array($studentAnswer) ? implode(',', $studentAnswer) : $studentAnswer;
                $studentOrder = trim($studentOrder);
                
                \Log::info('Reorder Processing', [
                    'rawStudentOrder' => $studentOrder,
                    'isArray' => is_array($studentAnswer)
                ]);
                
                // Convert student's numeric order to actual text sequence
                $studentSequence = '';
                if (!empty($fragments) && !empty($studentOrder)) {
                    // Split by comma and clean up
                    $orderArray = array_map('trim', explode(',', $studentOrder));
                    $orderArray = array_filter($orderArray, function($item) {
                        return !empty($item) && is_numeric($item);
                    });
                    
                    \Log::info('Reorder Order Array', [
                        'orderArray' => $orderArray,
                        'fragmentsCount' => count($fragments)
                    ]);
                    
                    $studentFragments = [];
                    foreach ($orderArray as $orderNum) {
                        $index = (int)$orderNum - 1; // Convert to 0-based
                        if (isset($fragments[$index])) {
                            $studentFragments[] = $fragments[$index];
                        }
                    }
                    $studentSequence = implode(' ', $studentFragments);
                }
                
                \Log::info('Reorder Sequence Comparison', [
                    'studentSequence' => $studentSequence,
                    'correctSentence' => $correctSentence
                ]);
                
                // Compare the student's reconstructed sentence with the correct sentence
                // Normalize by removing extra spaces, punctuation differences, and converting to lowercase
                $studentNormalized = preg_replace('/[^\w\s]/u', '', preg_replace('/\s+/', ' ', strtolower(trim($studentSequence))));
                $correctNormalized = preg_replace('/[^\w\s]/u', '', preg_replace('/\s+/', ' ', strtolower(trim($correctSentence))));
                
                $isCorrect = $studentNormalized === $correctNormalized;
                
                \Log::info('Reorder Result', [
                    'studentNormalized' => $studentNormalized,
                    'correctNormalized' => $correctNormalized,
                    'isCorrect' => $isCorrect
                ]);
                
                return $isCorrect;

            case 'statement_match':
                $correctPairs = $question->correct_pairs ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                

                
                // Convert student answers to pairs format for comparison  
                $studentPairs = [];
                foreach ($studentAnswers as $leftIndex => $rightIndex) {
                    if (!empty($rightIndex) && is_numeric($rightIndex)) {
                        $studentPairs[] = [
                            'left' => (int)$leftIndex,
                            'right' => (int)$rightIndex - 1 // Convert 1-based to 0-based
                        ];
                    }
                }
                

                
                // Check if all correct pairs match student pairs
                $allCorrect = true;
                foreach ($correctPairs as $correctPair) {
                    $found = false;
                    foreach ($studentPairs as $studentPair) {
                        if ($studentPair['left'] === $correctPair['left'] && 
                            $studentPair['right'] === $correctPair['right']) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $allCorrect = false;
                    }
                }
                
                // Also check if student made extra incorrect matches
                if (count($studentPairs) !== count($correctPairs)) {
                    $allCorrect = false;
                }
                
                return $allCorrect;

            case 'audio_picture_match':
                $correctPairs = $correctAnswer['correct_pairs'] ?? [];
                $studentPairs = is_array($studentAnswer) ? $studentAnswer : [];
                
                \Log::info('Audio Picture Match Evaluation', [
                    'correctPairs' => $correctPairs,
                    'studentPairs' => $studentPairs
                ]);
                
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
                    
                    \Log::info("Comparing audio $leftIndex", [
                        'expected' => $expectedImageIndex,
                        'student' => $studentImageIndexStr,
                        'match' => $studentImageIndexStr === $expectedImageIndex
                    ]);
                    
                    if ($studentImageIndexStr !== $expectedImageIndex) {
                        $allMatched = false;
                        break;
                    }
                }
                
                \Log::info('Audio Picture Match Result', ['allMatched' => $allMatched]);
                return $allMatched;

            case 'picture_mcq':
            case 'audio_image_text_single':
            case 'audio_image_text_multiple':
                $correctPairs = $correctAnswer['correct_pairs'] ?? [];
                $studentPairs = is_array($studentAnswer) ? $studentAnswer : [];
                
                \Log::info('Picture MCQ Evaluation', [
                    'correctPairs' => $correctPairs,
                    'studentPairs' => $studentPairs,
                    'correctAnswer' => $correctAnswer
                ]);
                
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
                    
                    \Log::info("Comparing image $leftIndex", [
                        'expected' => $expectedImageIndex,
                        'student' => $studentImageIndexStr,
                        'match' => $studentImageIndexStr === $expectedImageIndex
                    ]);
                    
                    if ($studentImageIndexStr !== $expectedImageIndex) {
                        $allMatched = false;
                        break;
                    }
                }
                
                \Log::info('Picture MCQ Result', ['allMatched' => $allMatched]);
                return $allMatched;

            case 'audio_mcq_single':
                // Audio MCQ works like mcq_multiple with sub-questions
                $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                $subQuestions = $questionData['sub_questions'] ?? $correctAnswer['sub_questions'] ?? [];
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
                // Get correct answers from the answer_data structure
                $correctAnswerData = is_string($question->answer_data) ? json_decode($question->answer_data, true) : ($question->answer_data ?? []);
                $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                
                // The correct answers are stored in answer_data as 'true_false_answers', not 'questions'
                $correctAnswers = $correctAnswerData['true_false_answers'] ?? 
                                $question->true_false_questions ?? 
                                $questionData['questions'] ?? 
                                [];
                
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                

                
                // Check if we have correct answers and student answers
                if (empty($correctAnswers) || empty($studentAnswers)) {
                    return false;
                }
                
                // Check if we have answers for all questions
                if (count($correctAnswers) !== count($studentAnswers)) {
                    return false;
                }
                
                $allCorrect = true;
                foreach ($correctAnswers as $qIndex => $correctAnswerItem) {
                    // Handle correct answer - check different possible structures
                    $correctAnswer_tf = '';
                    if (is_array($correctAnswerItem)) {
                        $correctAnswer_tf = $correctAnswerItem['correct_answer'] ?? 
                                          $correctAnswerItem['answer'] ?? 
                                          '';
                    } else {
                        $correctAnswer_tf = $correctAnswerItem;
                    }
                    
                    $studentAnswer_tf = $studentAnswers[$qIndex] ?? '';
                    
                    // If student answer is empty/null, it's incorrect
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
                // Opinion questions are always correct (subjective)
                return true;

            default:
                return false;
        }
    }

    private function getAnswerResult($question, $studentAnswer, $isCorrect)
    {
        $result = [
            'is_correct' => $isCorrect,
            'message' => $isCorrect ? 'Correct Answer!' : 'Wrong Answer!',
            'student_answer' => $studentAnswer,
            'correct_answer' => null,
            'student_answer_text' => null,
            'correct_answer_text' => null,
        ];

        $questionType = $question->questionType->name ?? '';
        $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
        $correctAnswerData = is_string($question->answer_data) ? json_decode($question->answer_data, true) : ($question->answer_data ?? []);

        switch ($questionType) {
            case 'mcq_single':
                $options = $questionData['options'] ?? [];
                $correctIndices = $correctAnswerData['correct_indices'] ?? [];
                $studentIndices = is_array($studentAnswer) ? array_map('intval', $studentAnswer) : [(int)$studentAnswer];
                
                $result['correct_answer'] = $correctIndices;
                $result['student_answer_text'] = $studentIndices[0] !== -1 ? 
                    (isset($options[$studentIndices[0]]) ? $options[$studentIndices[0]] : '(No answer provided)') : 
                    '(No answer provided)';
                $result['correct_answer_text'] = array_map(function($index) use ($options) {
                    return $options[$index] ?? '';
                }, $correctIndices);
                break;
                
            case 'true_false':
                $correctAnswer_tf = $correctAnswerData['correct_answer'] ?? '';
                $studentAnswer_tf = is_array($studentAnswer) ? ($studentAnswer[0] ?? '') : $studentAnswer;
                
                $result['correct_answer'] = $correctAnswer_tf;
                $result['student_answer_text'] = $studentAnswer_tf ?: '(No answer provided)';
                $result['correct_answer_text'] = $correctAnswer_tf;
                break;

            case 'mcq_multiple':
                $subQuestions = $questionData['sub_questions'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                
                $result['correct_answer'] = $correctAnswerData;
                
                // Format student answers with text
                $studentAnswerText = [];
                foreach ($subQuestions as $subIndex => $subQuestion) {
                    $options = $subQuestion['options'] ?? [];
                    $studentSubAnswers = $studentAnswers[$subIndex] ?? [];
                    
                    $subAnswerText = array_map(function($optIndex) use ($options) {
                        return $options[$optIndex] ?? "Option $optIndex";
                    }, $studentSubAnswers);
                    
                    $studentAnswerText[] = $subAnswerText ? implode(', ', $subAnswerText) : '(No answer)';
                }
                
                $result['student_answer_text'] = $studentAnswerText;
                
                // Format correct answers with text
                $correctAnswerText = [];
                foreach ($subQuestions as $subIndex => $subQuestion) {
                    $options = $subQuestion['options'] ?? [];
                    $correctIndices = $subQuestion['correct_indices'] ?? [];
                    
                    $correctSubAnswerText = array_map(function($optIndex) use ($options) {
                        return $options[$optIndex] ?? "Option $optIndex";
                    }, $correctIndices);
                    
                    $correctAnswerText[] = $correctSubAnswerText ? implode(', ', $correctSubAnswerText) : '(No correct answer)';
                }
                
                $result['correct_answer_text'] = $correctAnswerText;
                break;

            case 'reorder':
                $fragments = $question->reorder_fragments ?? $questionData['fragments'] ?? [];
                $correctSentence = $question->reorder_answer_key ?? '';
                
                \Log::info('Reorder Result Generation', [
                    'fragments' => $fragments,
                    'correctSentence' => $correctSentence,
                    'studentAnswer' => $studentAnswer
                ]);
                
                // Parse student order (numeric indices like "1,2,3,4" or "1, 2, 3, 4")
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
                
                \Log::info('Reorder Result Processing', [
                    'studentSequence' => $studentSequence,
                    'studentOrder' => $studentOrder
                ]);
                
                // The correct answer is already stored as the complete sentence
                $result['correct_answer'] = $correctSentence;
                $result['student_answer_text'] = $studentSequence ?: '(No answer provided)';
                $result['correct_answer_text'] = $correctSentence;
                break;

            case 'statement_match':
                $leftOptions = $question->left_options ?? $questionData['left_options'] ?? [];
                $rightOptions = $question->right_options ?? $questionData['right_options'] ?? [];
                $correctPairs = $question->correct_pairs ?? [];
                $studentPairs = is_array($studentAnswer) ? $studentAnswer : [];
                
                $result['correct_answer'] = $correctPairs;
                
                // Format student answers - FIXED logic
                $studentAnswerText = [];
                if (!empty($studentPairs)) {
                    // Ensure all left options are represented
                    for ($i = 0; $i < count($leftOptions); $i++) {
                        $leftText = $leftOptions[$i] ?? "Item " . ($i + 1);
                        $rightIndex = $studentPairs[$i] ?? null;
                        
                        if (!empty($rightIndex) && $rightIndex !== '' && is_numeric($rightIndex)) {
                            // Convert 1-based student input to 0-based array index
                            $rightOptionIndex = (int)$rightIndex - 1;
                            $rightText = $rightOptions[$rightOptionIndex] ?? "Option " . $rightIndex;
                            $studentAnswerText[] = "$leftText → $rightText";
                        } else {
                            $studentAnswerText[] = "$leftText → (No selection)";
                        }
                    }
                }
                
                // If no student answers, show empty state
                if (empty($studentAnswerText)) {
                    $studentAnswerText = ['(No answers provided)'];
                }
                
                $result['student_answer_text'] = $studentAnswerText;
                
                // Format correct answers with improved structure
                $correctAnswerText = [];
                foreach ($correctPairs as $pair) {
                    $leftText = $leftOptions[$pair['left']] ?? "Item " . ($pair['left'] + 1);
                    $rightText = $rightOptions[$pair['right']] ?? "Option " . ($pair['right'] + 1);
                    $correctAnswerText[] = "$leftText → $rightText";
                }
                
                $result['correct_answer_text'] = $correctAnswerText;
                break;

            case 'audio_mcq_single':
                // FIXED: Audio MCQ Single - each sub-question has only one selected answer
                $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                $subQuestions = $questionData['sub_questions'] ?? $correctAnswerData['sub_questions'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                
                $result['correct_answer'] = $correctAnswerData;
                
                // Debug logging for audio_mcq_single
                \Log::info('Audio MCQ Single Debug', [
                    'studentAnswers' => $studentAnswers,
                    'subQuestions' => $subQuestions,
                    'studentAnswer' => $studentAnswer
                ]);
                
                // Format student answers with text - FIXED for single selection
                $studentAnswerText = [];
                foreach ($subQuestions as $subIndex => $subQuestion) {
                    $options = $subQuestion['options'] ?? [];
                    $studentIndex = isset($studentAnswers[$subIndex]) ? (int)$studentAnswers[$subIndex] : -1;
                    
                    \Log::info("Processing sub-question {$subIndex}", [
                        'studentIndex' => $studentIndex,
                        'options' => $options,
                        'hasOption' => isset($options[$studentIndex])
                    ]);
                    
                    if ($studentIndex >= 0 && isset($options[$studentIndex])) {
                        $studentAnswerText[] = $options[$studentIndex];
                    } else {
                        $studentAnswerText[] = '(No answer)';
                    }
                }
                
                $result['student_answer_text'] = $studentAnswerText;
                
                // Format correct answers with text - FIXED for single selection
                $correctAnswerText = [];
                foreach ($subQuestions as $subIndex => $subQuestion) {
                    $options = $subQuestion['options'] ?? [];
                    $correctIndices = $subQuestion['correct_indices'] ?? [];
                    
                    if (!empty($correctIndices) && isset($options[$correctIndices[0]])) {
                        $correctAnswerText[] = $options[$correctIndices[0]];
                    } else {
                        $correctAnswerText[] = '(No correct answer)';
                    }
                }
                
                $result['correct_answer_text'] = $correctAnswerText;
                break;

            case 'audio_fill_blank':
            case 'picture_fill_blank':
            case 'video_fill_blank':
                $correctAnswers = $correctAnswerData['answer_keys'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? array_values($studentAnswer) : [$studentAnswer];
                
                $result['correct_answer'] = $correctAnswers;
                $result['student_answer_text'] = $studentAnswers;
                $result['correct_answer_text'] = $correctAnswers;
                break;

            case 'picture_mcq':
            case 'audio_image_text_single':
            case 'audio_image_text_multiple':
                $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                $rightOptions = $question->right_options ?? $questionData['right_options'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                
                \Log::info('Picture MCQ Result Generation', [
                    'rightOptions' => $rightOptions,
                    'studentAnswers' => $studentAnswers,
                    'correctAnswerData' => $correctAnswerData
                ]);
                
                $result['correct_answer'] = $correctAnswerData;
                
                // Format student answers with text - handle both associative and sequential arrays
                $studentAnswerText = [];
                foreach ($studentAnswers as $imageIndex => $optionIndex) {
                    // Handle both associative and sequential arrays
                    $actualImageIndex = is_numeric($imageIndex) ? (int)$imageIndex : $imageIndex;
                    $actualOptionIndex = is_numeric($optionIndex) ? (int)$optionIndex : $optionIndex;
                    
                    if ($actualOptionIndex !== '' && $actualOptionIndex !== null && is_numeric($actualOptionIndex)) {
                        $optionText = $rightOptions[$actualOptionIndex] ?? 'Option ' . ($actualOptionIndex + 1);
                        $studentAnswerText[$actualImageIndex] = "Image " . ($actualImageIndex + 1) . ": " . $optionText;
                    } else {
                        $studentAnswerText[$actualImageIndex] = "Image " . ($actualImageIndex + 1) . ": (No selection)";
                    }
                }
                
                $result['student_answer_text'] = $studentAnswerText;
                
                // Format correct answers with text
                $correctAnswerText = [];
                if (isset($correctAnswerData['correct_pairs'])) {
                    foreach ($correctAnswerData['correct_pairs'] as $pair) {
                        $imageIndex = $pair['left'] ?? 0;
                        $optionIndex = $pair['right'] ?? 0;
                        $optionText = $rightOptions[$optionIndex] ?? 'Option ' . ($optionIndex + 1);
                        $correctAnswerText[$imageIndex] = "Image " . ($imageIndex + 1) . ": " . $optionText;
                    }
                }
                
                $result['correct_answer_text'] = $correctAnswerText;
                
                \Log::info('Picture MCQ Result', [
                    'studentAnswerText' => $studentAnswerText,
                    'correctAnswerText' => $correctAnswerText
                ]);
                break;

            case 'audio_picture_match':
                $audios = $questionData['audios'] ?? [];
                $images = $questionData['images'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                
                \Log::info('Audio Picture Match Debug', [
                    'audios' => $audios,
                    'images' => $images,
                    'studentAnswers' => $studentAnswers,
                    'correctAnswerData' => $correctAnswerData
                ]);
                
                $result['correct_answer'] = $correctAnswerData;
                
                // Format student answers with text - studentAnswers can be associative array [audioIndex => imageIndex] or sequential array
                $studentAnswerText = [];
                foreach ($audios as $audioIndex => $audioPath) {
                    // Convert audioIndex to string to match form data keys
                    $audioKey = (string)$audioIndex;
                    
                    // Handle both associative and sequential arrays
                    $selectedImageIndex = null;
                    if (isset($studentAnswers[$audioKey])) {
                        $selectedImageIndex = $studentAnswers[$audioKey];
                    } elseif (isset($studentAnswers[$audioIndex])) {
                        $selectedImageIndex = $studentAnswers[$audioIndex];
                    }
                    
                    \Log::info("Processing audio $audioKey", [
                        'selectedImageIndex' => $selectedImageIndex,
                        'is_numeric' => is_numeric($selectedImageIndex),
                        'is_empty' => empty($selectedImageIndex),
                        'studentAnswers' => $studentAnswers
                    ]);
                    
                    if ($selectedImageIndex !== '' && $selectedImageIndex !== null && is_numeric($selectedImageIndex)) {
                        $studentAnswerText[$audioKey] = 'Image ' . (intval($selectedImageIndex) + 1);
                    } else {
                        $studentAnswerText[$audioKey] = '(No selection)';
                    }
                }
                
                $result['student_answer_text'] = $studentAnswerText;
                
                // Format correct answers with text
                $correctAnswerText = [];
                if (isset($correctAnswerData['correct_pairs'])) {
                    foreach ($correctAnswerData['correct_pairs'] as $pair) {
                        $audioIndex = $pair['left'] ?? 0;
                        $imageIndex = $pair['right'] ?? 0;
                        // Convert string to int for proper indexing
                        $correctAnswerText[(string)$audioIndex] = 'Image ' . (intval($imageIndex) + 1);
                    }
                }
                
                $result['correct_answer_text'] = $correctAnswerText;
                
                \Log::info('Audio Picture Match Result', [
                    'studentAnswerText' => $studentAnswerText,
                    'correctAnswerText' => $correctAnswerText
                ]);
                break;

            case 'true_false_multiple':
                $questions = $question->true_false_questions ?? $questionData['questions'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? $studentAnswer : [];
                
                $result['correct_answer'] = $correctAnswerData;
                
                // Format student answers with text
                $studentAnswerText = [];
                foreach ($studentAnswers as $index => $answer) {
                    $studentAnswerText[] = ucfirst($answer ?: '(No answer)');
                }
                
                $result['student_answer_text'] = $studentAnswerText;
                
                // Format correct answers with text
                $correctAnswerText = [];
                foreach ($questions as $index => $question) {
                    $correctAnswerText[] = ucfirst($question['correct_answer'] ?? 'true');
                }
                
                $result['correct_answer_text'] = $correctAnswerText;
                break;

            case 'form_fill':
            case 'audio_fill_blank':
            case 'picture_fill_blank':
            case 'video_fill_blank':
                $correctAnswers = $correctAnswerData['answer_keys'] ?? [];
                $studentAnswers = is_array($studentAnswer) ? array_values($studentAnswer) : [$studentAnswer];
                
                \Log::info('Form Fill Result Generation', [
                    'correctAnswers' => $correctAnswers,
                    'studentAnswers' => $studentAnswers,
                    'correctAnswerData' => $correctAnswerData
                ]);
                
                $result['correct_answer'] = $correctAnswerData;
                
                // Format student answers as array of strings
                $studentAnswerText = [];
                foreach ($studentAnswers as $index => $answer) {
                    $studentAnswerText[] = $answer ?: '(No answer)';
                }
                
                $result['student_answer_text'] = $studentAnswerText;
                
                // Format correct answers as array of strings
                $correctAnswerText = [];
                foreach ($correctAnswers as $index => $answer) {
                    $correctAnswerText[] = $answer ?: '(No correct answer)';
                }
                
                $result['correct_answer_text'] = $correctAnswerText;
                
                \Log::info('Form Fill Result', [
                    'studentAnswerText' => $studentAnswerText,
                    'correctAnswerText' => $correctAnswerText
                ]);
                break;

            case 'opinion':
                $result['correct_answer'] = $correctAnswerData;
                $result['student_answer_text'] = is_array($studentAnswer) ? implode(' ', $studentAnswer) : $studentAnswer;
                $result['correct_answer_text'] = $correctAnswerData;
                break;

            default:
                // Generic fallback for any unhandled question types
                $result['correct_answer'] = $correctAnswerData;
                $result['student_answer_text'] = is_array($studentAnswer) ? implode(' ', $studentAnswer) : $studentAnswer;
                $result['correct_answer_text'] = $correctAnswerData;
                break;
        }

        return $result;
    }

    // Helper method to get validation rules
    private function getValidationRules($questionType, $isSpeakingSubject)
    {
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
                // For re-attempts, allow submission even if answer is the same
                $isReAttempt = request()->input('is_reattempt', false);
                $rules = ['answer' => $isReAttempt ? 'required|array' : 'required|array'];
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

    // Helper method to get validation error messages
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
    private function normalizeAnswer($questionType, $answer)
    {
        if ($answer === null) {
            return null;
        }
        
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
        
        if (in_array($questionType, $multiTypes)) {
            return is_array($answer) ? $answer : [$answer];
        }
        
        // Single answer types
        return is_array($answer) ? (count($answer) === 1 ? $answer[0] : $answer) : $answer;
    }

    // Handle file upload for speaking/opinion questions
    private function handleFileUpload($request, $isOpinionQuestion, $isSpeakingSubject)
    {
        $fileUploadPath = null;
        
        if ($isOpinionQuestion && $isSpeakingSubject) {
            // For speaking questions, require either file upload or written answer (or both)
            if (!$request->hasFile('audio_video_file') && empty($request->input('answer'))) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'answer' => 'For speaking questions, please provide either an audio/video response or a written answer (or both).'
                ]);
            }
            
            if ($request->hasFile('audio_video_file')) {
                $file = $request->file('audio_video_file');
                $fileUploadPath = $file->store('speaking_responses', 'public');
            }
        }
        
        return $fileUploadPath;
    }
}