@php
    $formAction = route('filament.student.pages.tests.question.submit', [$test, $question]);
    $answer = $studentAnswer && $studentAnswer->answer_data 
        ? (is_array($studentAnswer->answer_data) 
            ? $studentAnswer->answer_data 
            : (is_string($studentAnswer->answer_data) 
                ? (json_decode($studentAnswer->answer_data, true) ?: $studentAnswer->answer_data) 
                : $studentAnswer->answer_data)) 
        : null;
    
    // Prepare additional parameters
    $questionType = $question->questionType->name ?? null;
    $questionData = is_string($question->question_data) 
        ? json_decode($question->question_data, true) 
        : ($question->question_data ?? []);
@endphp

@extends('filament.student.pages.answer-question', [
    'question' => $question,
    'studentAnswer' => $studentAnswer,
    'test' => $test,
    'existingAnswer' => $studentAnswer,
    'formAction' => $formAction,
    'answer' => $answer,
    'type' => $questionType,
    'qdata' => $questionData,
    'isReAttempt' => false, // Explicitly set for tests
    'submittedAnswer' => $studentAnswer, // Add this to match course question template
    'is_test' => true, // Add a flag to distinguish test questions
    'isViewMode' => $isViewMode // Pass the view mode explicitly
]) 