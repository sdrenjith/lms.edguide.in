<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Models\Question;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewQuestion extends ViewRecord
{
    protected static string $resource = QuestionResource::class;
    protected static string $view = 'filament.resources.question-resource.pages.view-custom';

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Load all necessary relationships
        $this->record = Question::with([
            'course',
            'subject', 
            'day',
            'questionType'
        ])->findOrFail($record);
    }

    protected function getViewData(): array
    {
        $record = $this->record;
        
        // Process question data (for regular questions)
        $questionData = null;
        $options = [];
        if ($record->question_data) {
            $questionData = is_string($record->question_data) 
                ? json_decode($record->question_data, true) 
                : $record->question_data;
            $options = $questionData['options'] ?? [];
        }

        // Process answer data (for regular questions)
        $answerData = null;
        $correctIndices = [];
        if ($record->answer_data) {
            $answerData = is_string($record->answer_data) 
                ? json_decode($record->answer_data, true) 
                : $record->answer_data;
            $correctIndices = $answerData['correct_indices'] ?? [];
        }

        // Process statement match data
        $leftOptions = $record->left_options ?? [];
        $rightOptions = $record->right_options ?? [];
        $correctPairs = $record->correct_pairs ?? [];

        // Determine question type
        $isStatementMatch = ($record->questionType->name ?? '') === 'statement_match';

        // Process explanation file
        $explanationFile = null;
        $explanationFileName = null;
        $explanationUrl = null;
        if ($record->explanation) {
            $explanationFile = $record->explanation;
            $explanationFileName = basename($record->explanation);
            $explanationUrl = Storage::url($record->explanation);
        }

        return [
            'record' => $record,
            
            // Basic question details
            'day_number' => $record->day->day_number ?? null,
            'course_name' => $record->course->name ?? 'Unknown Course',
            'subject_name' => $record->subject->name ?? 'Unknown Subject',
            'question_type_name' => $record->questionType->name ?? 'Unknown Type',
            'points' => $record->points ?? 1,
            'is_active' => $record->is_active ?? false,
            'instruction' => $record->instruction ?? '',
            'topic' => $record->topic ?? null,  // Add topic to view data
            
            // File information
            'explanation_file' => $explanationFile,
            'explanation_file_name' => $explanationFileName,
            'explanation_url' => $explanationUrl,
            
            // Question type flag
            'is_statement_match' => $isStatementMatch,
            
            // Regular question data
            'question_data' => $questionData,
            'options' => $options,
            'answer_data' => $answerData,
            'correct_indices' => $correctIndices,
            
            // Statement match data
            'left_options' => $leftOptions,
            'right_options' => $rightOptions,
            'correct_pairs' => $correctPairs,
            
            // Additional computed data
            'total_options' => count($options),
            'total_left_options' => count($leftOptions),
            'total_right_options' => count($rightOptions),
            'total_correct_answers' => $isStatementMatch ? count($correctPairs) : count($correctIndices),
            
            // Answer previews for regular questions
            'correct_answer_previews' => $this->getCorrectAnswerPreviews($options, $correctIndices),
            
            // Pair previews for statement match
            'pair_previews' => $this->getPairPreviews($leftOptions, $rightOptions, $correctPairs),
            'test_name' => $record->test->name ?? null,
        ];
    }

    /**
     * Get preview text for correct answers in regular questions
     */
    private function getCorrectAnswerPreviews(array $options, array $correctIndices): array
    {
        $previews = [];
        foreach ($correctIndices as $index) {
            $previews[] = [
                'index' => $index,
                'text' => $options[$index] ?? 'Option not found'
            ];
        }
        return $previews;
    }

    /**
     * Get preview text for correct pairs in statement match
     */
    private function getPairPreviews(array $leftOptions, array $rightOptions, array $correctPairs): array
    {
        $previews = [];
        foreach ($correctPairs as $pairIndex => $pair) {
            $leftIndex = $pair['left'] ?? null;
            $rightIndex = $pair['right'] ?? null;
            
            $previews[] = [
                'pair_number' => $pairIndex + 1,
                'left_index' => $leftIndex,
                'left_text' => isset($leftIndex) && isset($leftOptions[$leftIndex]) 
                    ? $leftOptions[$leftIndex] 
                    : 'Option not found',
                'right_index' => $rightIndex,
                'right_text' => isset($rightIndex) && isset($rightOptions[$rightIndex]) 
                    ? $rightOptions[$rightIndex] 
                    : 'Option not found',
            ];
        }
        return $previews;
    }

    // Custom page title (appears in browser tab)
    public function getTitle(): string
    {
        return 'View Question #' . $this->record->id;
    }

    // Custom page heading (main title on page)
    public function getHeading(): string
    {
        $questionType = $this->record->questionType->name ?? 'Question';
        return 'View ' . ucfirst(str_replace('_', ' ', $questionType)) . ' Question';
    }

    // Custom subheading (description below title)
    public function getSubheading(): ?string
    {
        $course = $this->record->course->name ?? 'Unknown Course';
        $subject = $this->record->subject->name ?? 'Unknown Subject';
        $day = $this->record->day->day_number ?? 'Unknown Day';
        
        return "Course: {$course} | Subject: {$subject} | Day: {$day} | Points: {$this->record->points}";
    }

    // Custom breadcrumbs
    public function getBreadcrumbs(): array
    {
        return [
            url('/admin/questions') => 'Questions',
            '' => 'View Question #' . $this->record->id,
        ];
    }

    // Hide the back button if you want to use custom buttons
    protected function hasLogo(): bool
    {
        return false;
    }

    // Additional helper methods that can be called from the view
    public function getQuestionSummary(): string
    {
        $record = $this->record;
        $summary = [];
        
        $summary[] = "Type: " . ($record->questionType->name ?? 'Unknown');
        $summary[] = "Points: " . ($record->points ?? 1);
        $summary[] = "Status: " . ($record->is_active ? 'Active' : 'Inactive');
        
        if ($record->questionType->name === 'statement_match') {
            $leftCount = count($record->left_options ?? []);
            $rightCount = count($record->right_options ?? []);
            $pairCount = count($record->correct_pairs ?? []);
            $summary[] = "Left Options: {$leftCount}";
            $summary[] = "Right Options: {$rightCount}";
            $summary[] = "Correct Pairs: {$pairCount}";
        } else {
            $questionData = is_string($record->question_data) 
                ? json_decode($record->question_data, true) 
                : $record->question_data;
            $optionCount = count($questionData['options'] ?? []);
            
            $answerData = is_string($record->answer_data) 
                ? json_decode($record->answer_data, true) 
                : $record->answer_data;
            $correctCount = count($answerData['correct_indices'] ?? []);
            
            $summary[] = "Options: {$optionCount}";
            $summary[] = "Correct Answers: {$correctCount}";
        }
        
        return implode(' | ', $summary);
    }

    public function isValidQuestion(): bool
    {
        $record = $this->record;
        
        // Check basic required fields
        if (empty($record->instruction) || empty($record->course_id) || empty($record->subject_id)) {
            return false;
        }
        
        // Check question type specific requirements
        if ($record->questionType->name === 'statement_match') {
            $leftOptions = $record->left_options ?? [];
            $rightOptions = $record->right_options ?? [];
            $correctPairs = $record->correct_pairs ?? [];
            
            return count($leftOptions) >= 2 && 
                   count($rightOptions) >= 2 && 
                   count($correctPairs) >= 1;
        } else {
            $questionData = is_string($record->question_data) 
                ? json_decode($record->question_data, true) 
                : $record->question_data;
            $answerData = is_string($record->answer_data) 
                ? json_decode($record->answer_data, true) 
                : $record->answer_data;
            
            $options = $questionData['options'] ?? [];
            $correctIndices = $answerData['correct_indices'] ?? [];
            
            return count($options) >= 1 && count($correctIndices) >= 1;
        }
    }
}