<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\Subject;
use App\Models\QuestionType;

class EditQuestion extends Page
{
    protected static string $resource = QuestionResource::class;
    protected static string $view = 'filament.resources.question-resource.pages.edit-custom';

    public $record;
    public $day_id = '';
    public $course_id = '';
    public $subject_id = '';
    public $topic = '';
    public $question_type_id = '';
    public $points = 1;
    public $is_active = true;
    public $instruction = '';
    public $explanation = '';
    public $options = [''];
    public $answer_indices = [0];
    public $day_number_input = 1;
    public $explanation_file;
    public $left_options = [''];
    public $right_options = [''];
    public $correct_pairs = [
        ['left' => '', 'right' => ''],
        ['left' => '', 'right' => ''],
    ];
    public $opinion_answer = '';
    
    // Properties for MCQ Multiple
    public $sub_questions = [
        [
            'question' => '',
            'options' => ['', ''],
            'correct_indices' => [0]
        ]
    ];

    // Properties for True/False Multiple
    public $true_false_questions = [
        [
            'statement' => '',
            'correct_answer' => ''
        ]
    ];

    // Properties for Simple True/False
    public $true_false_statement = '';
    public $true_false_answer = '';

    // Properties for Reorder
    public $reorder_fragments = ['', ''];
    public $reorder_answer_key = '';

    // Properties for Form Fill
    public $form_fill_paragraph = '';
    public $form_fill_options = ['', ''];
    public $form_fill_answer_key = [''];

    // Properties for Picture MCQ
    public $picture_mcq_images = [];
    public $picture_mcq_right_options = ['', ''];
    public $picture_mcq_correct_pairs = [
        ['left' => '', 'right' => ''],
        ['left' => '', 'right' => ''],
    ];
    public $picture_mcq_image_uploads = [];

    // Properties for Audio MCQ Single
    public $audio_mcq_file = null;
    public $audio_mcq_sub_questions = [
        [
            'question' => '',
            'options' => ['', ''],
            'correct_indices' => [0]
        ]
    ];

    // Properties for Audio Image Text Single
    public $audio_image_text_audio_file = null;
    public $audio_image_text_images = []; // Existing images
    public $audio_image_text_image_uploads = [];
    public $audio_image_text_right_options = ['', ''];
    public $audio_image_text_correct_pairs = [
        ['left' => '', 'right' => ''],
        ['left' => '', 'right' => ''],
    ];

    // Properties for Audio Image Text Multiple
    public $audio_image_text_multiple_pairs = [];
    public $audio_image_text_multiple_existing_pairs = []; // Store existing pairs
    public $audio_image_text_multiple_right_options = ['', ''];
    public $audio_image_text_multiple_correct_pairs = [
        ['left' => '', 'right' => ''],
        ['left' => '', 'right' => ''],
    ];

    // Properties for Audio Fill Blank
    public $audio_fill_paragraph = '';
    public $audio_fill_answer_key = [''];
    public $audio_fill_audio_file = null;

    // Properties for Picture Fill Blank
    public $picture_fill_paragraph = '';
    public $picture_fill_answer_key = [''];
    public $picture_fill_image = null;

    // Properties for Video Fill Blank
    public $video_fill_paragraph = '';
    public $video_fill_answer_key = [''];
    public $video_fill_video = null;

    // Add these properties for Audio + Picture Matching
    public $audio_picture_audios = [null];
    public $audio_picture_images = [null];
    public $audio_picture_pairs = [ ['left' => '', 'right' => ''] ];

    public $rules = [
        'left_options' => 'array',
        'left_options.*' => 'string|nullable',
        'right_options' => 'array',
        'right_options.*' => 'string|nullable',
        'correct_pairs' => 'array',
        'correct_pairs.*.left' => 'nullable',
        'correct_pairs.*.right' => 'nullable',
        'opinion_answer' => 'string|nullable',
        // MCQ Multiple validation rules
        'sub_questions' => 'array',
        'sub_questions.*.question' => 'string|nullable',
        'sub_questions.*.options' => 'array',
        'sub_questions.*.options.*' => 'string|nullable',
        'sub_questions.*.correct_indices' => 'array',
        'sub_questions.*.correct_indices.*' => 'integer|nullable',
        // True/False Multiple validation rules
        'true_false_questions' => 'array',
        'true_false_questions.*.statement' => 'string|nullable',
        'true_false_questions.*.correct_answer' => 'string|nullable',
        // Simple True/False validation rules
        'true_false_statement' => 'string|nullable',
        'true_false_answer' => 'string|nullable',
        // Reorder validation rules
        'reorder_fragments' => 'array',
        'reorder_fragments.*' => 'string|nullable',
        'reorder_answer_key' => 'string|nullable',
        // Form Fill validation rules
        'form_fill_paragraph' => 'string|nullable',
        'form_fill_options' => 'array',
        'form_fill_options.*' => 'string|nullable',
        'form_fill_answer_key' => 'array',
        'form_fill_answer_key.*' => 'string|nullable',
        // Picture MCQ validation rules
        'picture_mcq_right_options' => 'array',
        'picture_mcq_right_options.*' => 'string|nullable',
        'picture_mcq_correct_pairs' => 'array',
        'picture_mcq_correct_pairs.*.left' => 'nullable',
        'picture_mcq_correct_pairs.*.right' => 'nullable',
        'picture_mcq_image_uploads.*' => 'nullable|image|max:2048',
        // Audio MCQ validation rules
        'audio_mcq_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:10240',
        'audio_mcq_sub_questions' => 'array',
        'audio_mcq_sub_questions.*.question' => 'string|nullable',
        'audio_mcq_sub_questions.*.options' => 'array',
        'audio_mcq_sub_questions.*.options.*' => 'string|nullable',
        'audio_mcq_sub_questions.*.correct_indices' => 'array',
        'audio_mcq_sub_questions.*.correct_indices.*' => 'integer|nullable',
        // Audio Image Text validation rules
        'audio_image_text_audio_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:10240',
        'audio_image_text_image_uploads.*' => 'nullable|image|max:2048',
        'audio_image_text_right_options' => 'array',
        'audio_image_text_right_options.*' => 'string|nullable',
        'audio_image_text_correct_pairs' => 'array',
        'audio_image_text_correct_pairs.*.left' => 'nullable',
        'audio_image_text_correct_pairs.*.right' => 'nullable',
        // Audio Image Text Multiple validation rules
        'audio_image_text_multiple_pairs.*.image' => 'nullable|image|max:2048',
        'audio_image_text_multiple_pairs.*.audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:10240',
        'audio_image_text_multiple_right_options' => 'array',
        'audio_image_text_multiple_right_options.*' => 'string|nullable',
        'audio_image_text_multiple_correct_pairs' => 'array',
        'audio_image_text_multiple_correct_pairs.*.left' => 'nullable',
        'audio_image_text_multiple_correct_pairs.*.right' => 'nullable',
        // Audio Fill Blank validation rules
        'audio_fill_paragraph' => 'string|nullable',
        'audio_fill_answer_key' => 'array',
        'audio_fill_answer_key.*' => 'string|nullable',
        'audio_fill_audio_file' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:10240',
        // Picture Fill Blank validation rules
        'picture_fill_paragraph' => 'string|nullable',
        'picture_fill_answer_key' => 'array',
        'picture_fill_answer_key.*' => 'string|nullable',
        'picture_fill_image' => 'nullable|image|max:4096',
        // Video Fill Blank validation rules
        'video_fill_paragraph' => 'string|nullable',
        'video_fill_answer_key' => 'array',
        'video_fill_answer_key.*' => 'string|nullable',
        'video_fill_video' => 'nullable|file|mimes:mp4,webm,ogg|max:20480',
        // Audio Picture Match validation rules
        'audio_picture_audios.*' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:10240',
        'audio_picture_images.*' => 'nullable|image|max:2048',
        'audio_picture_pairs' => 'array',
        'audio_picture_pairs.*.left' => 'nullable',
        'audio_picture_pairs.*.right' => 'nullable',
    ];

    public $test_id = null;

    public function mount($record)
    {
        if (is_string($record)) {
            $record = \App\Models\Question::findOrFail($record);
        }
        $this->record = $record;
        
        // Load basic data
        $this->day_id = $record->day_id;
        $this->course_id = $record->course_id;
        $this->subject_id = $record->subject_id;
        $this->topic = $record->topic;
        $this->points = $record->points;
        $this->is_active = $record->is_active;
        $this->instruction = $record->instruction;
        $this->explanation = $record->explanation;
        $this->day_number_input = $record->day->number ?? 1;
        $this->test_id = $record->test_id;

        // Determine question type and set question_type_id
        $questionTypeName = $record->questionType->name ?? '';
        if (in_array($questionTypeName, ['statement_match', 'opinion', 'mcq_multiple', 'true_false_multiple', 'true_false', 'reorder', 'form_fill', 'picture_mcq', 'audio_mcq_single', 'audio_image_text_single', 'audio_image_text_multiple', 'audio_fill_blank', 'picture_fill_blank', 'video_fill_blank', 'audio_picture_match'])) {
            $this->question_type_id = $questionTypeName;
        } else {
            $this->question_type_id = $record->question_type_id;
        }

        // Initialize all array properties with safe defaults to prevent undefined array key errors
        $this->initializeArrayProperties();

        // Load data based on question type
        $this->loadQuestionTypeData($record, $questionTypeName);
    }

    private function initializeArrayProperties()
    {
        // Initialize all array properties with safe defaults
        $this->options = [''];
        $this->answer_indices = [0];
        $this->left_options = [''];
        $this->right_options = [''];
        $this->correct_pairs = [['left' => '', 'right' => '']];
        $this->opinion_answer = '';
        $this->sub_questions = [
            [
                'question' => '',
                'options' => ['', ''],
                'correct_indices' => [0]
            ]
        ];
        $this->true_false_questions = [
            [
                'statement' => '',
                'correct_answer' => ''
            ]
        ];
        $this->true_false_statement = '';
        $this->true_false_answer = '';
        $this->reorder_fragments = ['', ''];
        $this->reorder_answer_key = '';
        $this->form_fill_paragraph = '';
        $this->form_fill_options = ['', ''];
        $this->form_fill_answer_key = [''];
        $this->picture_mcq_images = [];
        $this->picture_mcq_right_options = ['', ''];
        $this->picture_mcq_correct_pairs = [
            ['left' => '', 'right' => ''],
            ['left' => '', 'right' => ''],
        ];
        $this->picture_mcq_image_uploads = [];
        $this->audio_mcq_file = null;
        $this->audio_mcq_sub_questions = [
            [
                'question' => '',
                'options' => ['', ''],
                'correct_indices' => [0]
            ]
        ];
        $this->audio_image_text_audio_file = null;
        $this->audio_image_text_images = [];
        $this->audio_image_text_image_uploads = [];
        $this->audio_image_text_right_options = ['', ''];
        $this->audio_image_text_correct_pairs = [
            ['left' => '', 'right' => ''],
            ['left' => '', 'right' => ''],
        ];
        $this->audio_image_text_multiple_pairs = [];
        $this->audio_image_text_multiple_existing_pairs = [];
        $this->audio_image_text_multiple_right_options = ['', ''];
        $this->audio_image_text_multiple_correct_pairs = [
            ['left' => '', 'right' => ''],
            ['left' => '', 'right' => ''],
        ];
        $this->audio_fill_paragraph = '';
        $this->audio_fill_answer_key = [''];
        $this->audio_fill_audio_file = null;
        $this->picture_fill_paragraph = '';
        $this->picture_fill_answer_key = [''];
        $this->picture_fill_image = null;
        $this->video_fill_paragraph = '';
        $this->video_fill_answer_key = [''];
        $this->video_fill_video = null;
        $this->audio_picture_audios = [null];
        $this->audio_picture_images = [null];
        $this->audio_picture_pairs = [['left' => '', 'right' => '']];
    }

    private function loadQuestionTypeData($record, $questionTypeName)
    {
        switch ($questionTypeName) {
            case 'audio_fill_blank':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $answerData = is_string($record->answer_data) ? json_decode($record->answer_data, true) : $record->answer_data;
                $this->audio_fill_paragraph = $questionData['paragraph'] ?? '';
                $this->audio_fill_answer_key = $answerData['answer_keys'] ?? [''];
                break;

            case 'statement_match':
                $this->left_options = $record->left_options ?? [''];
                $this->right_options = $record->right_options ?? [''];
                $this->correct_pairs = $record->correct_pairs ?? [
                    ['left' => '', 'right' => ''],
                    ['left' => '', 'right' => ''],
                ];
                break;

            case 'opinion':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $this->opinion_answer = $questionData['opinion_answer'] ?? '';
                break;

            case 'mcq_multiple':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $this->sub_questions = $questionData['sub_questions'] ?? [
                    [
                        'question' => '',
                        'options' => ['', ''],
                        'correct_indices' => [0]
                    ]
                ];
                break;

            case 'true_false_multiple':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $this->true_false_questions = $questionData['questions'] ?? [
                    [
                        'statement' => '',
                        'correct_answer' => ''
                    ]
                ];
                break;

            case 'true_false':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $this->true_false_statement = $questionData['statement'] ?? '';
                $answerData = is_string($record->answer_data) ? json_decode($record->answer_data, true) : $record->answer_data;
                $this->true_false_answer = $answerData['correct_answer'] ?? '';
                break;

            case 'reorder':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $answerData = is_string($record->answer_data) ? json_decode($record->answer_data, true) : $record->answer_data;
                $this->reorder_fragments = $record->reorder_fragments ?? ($questionData['fragments'] ?? ['', '']);
                $this->reorder_answer_key = $record->reorder_answer_key ?? ($answerData['answer_key'] ?? '');
                break;

            case 'form_fill':
                $this->form_fill_paragraph = $record->form_fill_paragraph ?? '';
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $this->form_fill_options = $questionData['options'] ?? ['', ''];
                $answerData = is_string($record->answer_data) ? json_decode($record->answer_data, true) : $record->answer_data;
                $this->form_fill_answer_key = $answerData['answer_keys'] ?? [''];
                break;

            case 'picture_mcq':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $this->picture_mcq_images = $record->picture_mcq_images ?? ($questionData['images'] ?? []);
                $this->picture_mcq_right_options = $record->right_options ?? ($questionData['right_options'] ?? ['', '']);
                $this->picture_mcq_correct_pairs = $record->correct_pairs ?? [
                    ['left' => '', 'right' => ''],
                    ['left' => '', 'right' => ''],
                ];
                // Initialize upload array to match existing images
                $this->picture_mcq_image_uploads = array_fill(0, max(1, count($this->picture_mcq_images)), null);
                break;

            case 'audio_mcq_single':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $this->audio_mcq_sub_questions = $questionData['sub_questions'] ?? [
                    [
                        'question' => '',
                        'options' => ['', ''],
                        'correct_indices' => [0]
                    ]
                ];
                break;

            case 'audio_image_text_single':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $this->audio_image_text_images = $record->audio_image_text_images ?? ($questionData['images'] ?? []);
                $this->audio_image_text_right_options = $record->right_options ?? ($questionData['right_options'] ?? ['', '']);
                $this->audio_image_text_correct_pairs = $record->correct_pairs ?? [
                    ['left' => '', 'right' => ''],
                    ['left' => '', 'right' => ''],
                ];
                // Initialize upload arrays to match existing data
                $this->audio_image_text_image_uploads = array_fill(0, max(1, count($this->audio_image_text_images)), null);
                break;

            case 'audio_image_text_multiple':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $this->audio_image_text_multiple_existing_pairs = $record->audio_image_text_multiple_pairs ?? ($questionData['image_audio_pairs'] ?? []);
                $this->audio_image_text_multiple_right_options = $record->right_options ?? ($questionData['right_options'] ?? ['', '']);
                $this->audio_image_text_multiple_correct_pairs = $record->correct_pairs ?? [
                    ['left' => '', 'right' => ''],
                    ['left' => '', 'right' => ''],
                ];
                $this->audio_image_text_multiple_pairs = array_fill(0, max(1, count($this->audio_image_text_multiple_existing_pairs)), ['image' => null, 'audio' => null]);
                break;

            case 'picture_fill_blank':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $answerData = is_string($record->answer_data) ? json_decode($record->answer_data, true) : $record->answer_data;
                $this->picture_fill_paragraph = $questionData['paragraph'] ?? '';
                $this->picture_fill_answer_key = $answerData['answer_keys'] ?? [''];
                break;

            case 'video_fill_blank':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $answerData = is_string($record->answer_data) ? json_decode($record->answer_data, true) : $record->answer_data;
                $this->video_fill_paragraph = $questionData['paragraph'] ?? '';
                $this->video_fill_answer_key = $answerData['answer_keys'] ?? [''];
                break;

            case 'audio_picture_match':
                $questionData = is_string($record->question_data) ? json_decode($record->question_data, true) : $record->question_data;
                $answerData = is_string($record->answer_data) ? json_decode($record->answer_data, true) : $record->answer_data;
                
                // Initialize arrays with existing data
                $this->audio_picture_audios = $questionData['audios'] ?? [null];
                $this->audio_picture_images = $questionData['images'] ?? [null];
                $this->audio_picture_pairs = $answerData['correct_pairs'] ?? [['left' => '', 'right' => '']];
                break;

            default:
                // Regular MCQ or other types
                if ($record->question_data) {
                    $data = json_decode($record->question_data, true);
                    $this->options = $data['options'] ?? [''];
                }
                if ($record->answer_data) {
                    $data = json_decode($record->answer_data, true);
                    $this->answer_indices = $data['correct_indices'] ?? [0];
                }
                break;
        }
    }

    // File management methods
    public function removeExplanationFile()
    {
        $this->explanation = null;
        $this->record->update(['explanation' => null]);
        
        Notification::make()
            ->title('File removed')
            ->body('Explanation file has been removed.')
            ->success()
            ->send();
    }

    // Basic option methods
    public function addOption()
    {
        $this->options[] = '';
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function addAnswerIndex()
    {
        $this->answer_indices[] = 0;
    }

    public function removeAnswerIndex($index)
    {
        unset($this->answer_indices[$index]);
        $this->answer_indices = array_values($this->answer_indices);
    }

    // Statement match methods
    public function addLeftOption()
    {
        $this->left_options[] = '';
    }

    public function removeLeftOption($index)
    {
        unset($this->left_options[$index]);
        $this->left_options = array_values($this->left_options);
        foreach ($this->correct_pairs as &$pair) {
            if (isset($pair['left']) && $pair['left'] >= $index) {
                $pair['left'] = '';
            }
        }
    }

    public function addRightOption()
    {
        $this->right_options[] = '';
    }

    public function removeRightOption($index)
    {
        unset($this->right_options[$index]);
        $this->right_options = array_values($this->right_options);
        foreach ($this->correct_pairs as &$pair) {
            if (isset($pair['right']) && $pair['right'] >= $index) {
                $pair['right'] = '';
            }
        }
    }

    public function getFilteredLeftOptions()
    {
        return array_filter($this->left_options, function($option) {
            return trim($option) !== '';
        });
    }

    public function getFilteredRightOptions()
    {
        return array_filter($this->right_options, function($option) {
            return trim($option) !== '';
        });
    }

    // MCQ Multiple methods
    public function addSubQuestion()
    {
        $this->sub_questions[] = [
            'question' => '',
            'options' => ['', ''],
            'correct_indices' => [0]
        ];
    }

    public function removeSubQuestion($index)
    {
        unset($this->sub_questions[$index]);
        $this->sub_questions = array_values($this->sub_questions);
    }

    public function addSubQuestionOption($subIndex)
    {
        if (count($this->sub_questions[$subIndex]['options']) < 6) {
            $this->sub_questions[$subIndex]['options'][] = '';
        }
    }

    public function removeSubQuestionOption($subIndex, $optIndex)
    {
        if (count($this->sub_questions[$subIndex]['options']) > 2) {
            unset($this->sub_questions[$subIndex]['options'][$optIndex]);
            $this->sub_questions[$subIndex]['options'] = array_values($this->sub_questions[$subIndex]['options']);
            
            $maxIndex = count($this->sub_questions[$subIndex]['options']) - 1;
            $this->sub_questions[$subIndex]['correct_indices'] = array_filter(
                $this->sub_questions[$subIndex]['correct_indices'],
                function($index) use ($maxIndex) {
                    return $index <= $maxIndex;
                }
            );
            
            $this->sub_questions[$subIndex]['correct_indices'] = array_values($this->sub_questions[$subIndex]['correct_indices']);
            
            if (empty($this->sub_questions[$subIndex]['correct_indices'])) {
                $this->sub_questions[$subIndex]['correct_indices'] = [0];
            }
        }
    }

    public function addSubQuestionAnswerIndex($subIndex)
    {
        $this->sub_questions[$subIndex]['correct_indices'][] = 0;
    }

    public function removeSubQuestionAnswerIndex($subIndex, $ansIndex)
    {
        if (count($this->sub_questions[$subIndex]['correct_indices']) > 1) {
            unset($this->sub_questions[$subIndex]['correct_indices'][$ansIndex]);
            $this->sub_questions[$subIndex]['correct_indices'] = array_values($this->sub_questions[$subIndex]['correct_indices']);
        }
    }

    // True/False Multiple methods
    public function addTrueFalseQuestion()
    {
        $this->true_false_questions[] = [
            'statement' => '',
            'correct_answer' => ''
        ];
    }

    public function removeTrueFalseQuestion($index)
    {
        unset($this->true_false_questions[$index]);
        $this->true_false_questions = array_values($this->true_false_questions);
    }

    public function setTrueFalseAnswer($index, $answer)
    {
        $this->true_false_questions[$index]['correct_answer'] = $answer;
    }

    // Reorder methods
    public function addReorderFragment()
    {
        $this->reorder_fragments[] = '';
    }

    public function removeReorderFragment($index)
    {
        if (count($this->reorder_fragments) > 2) {
            unset($this->reorder_fragments[$index]);
            $this->reorder_fragments = array_values($this->reorder_fragments);
        }
    }
    public function addStatementMatchPair()
{
    $this->correct_pairs[] = ['left' => '', 'right' => ''];
}


public function removeStatementMatchPair($index)
{
    if (count($this->correct_pairs) > 1) {
        unset($this->correct_pairs[$index]);
        $this->correct_pairs = array_values($this->correct_pairs);
    }
}
    public function clearAllStatementMatchPairs()
    {
        $this->correct_pairs = [['left' => '', 'right' => ''], ['left' => '', 'right' => '']];
    }
    
    // Form Fill methods
    public function addFormFillOption()
    {
        $this->form_fill_options[] = '';
    }

    public function removeFormFillOption($index)
    {
        if (count($this->form_fill_options) > 2) {
            unset($this->form_fill_options[$index]);
            $this->form_fill_options = array_values($this->form_fill_options);
        }
    }

    public function addFormFillAnswerKey()
    {
        $this->form_fill_answer_key[] = '';
    }

    public function removeFormFillAnswerKey($index)
    {
        if (count($this->form_fill_answer_key) > 1) {
            unset($this->form_fill_answer_key[$index]);
            $this->form_fill_answer_key = array_values($this->form_fill_answer_key);
        }
    }

    public function adjustAnswerKeysToBlankCount()
    {
        if (!empty($this->form_fill_paragraph)) {
            $blankCount = substr_count($this->form_fill_paragraph, '___');
            $currentAnswerKeyCount = count($this->form_fill_answer_key);
            
            if ($blankCount > $currentAnswerKeyCount) {
                for ($i = $currentAnswerKeyCount; $i < $blankCount; $i++) {
                    $this->form_fill_answer_key[] = '';
                }
            } elseif ($blankCount < $currentAnswerKeyCount && $blankCount > 0) {
                $this->form_fill_answer_key = array_slice($this->form_fill_answer_key, 0, $blankCount);
            }
            
            if (empty($this->form_fill_answer_key) || $blankCount === 0) {
                $this->form_fill_answer_key = [''];
            }
        }
    }

    // Picture MCQ methods
    public function addPictureMcqImage()
    {
        $this->picture_mcq_image_uploads[] = null;
        $this->picture_mcq_images[] = '';
    }

    public function removePictureMcqImage($index)
    {
        if (count($this->picture_mcq_images) > 1) {
            unset($this->picture_mcq_images[$index]);
            unset($this->picture_mcq_image_uploads[$index]);
            $this->picture_mcq_images = array_values($this->picture_mcq_images);
            $this->picture_mcq_image_uploads = array_values($this->picture_mcq_image_uploads);
            
            foreach ($this->picture_mcq_correct_pairs as &$pair) {
                if (isset($pair['left']) && $pair['left'] >= $index) {
                    $pair['left'] = '';
                }
            }
        }
    }

    public function addPictureMcqRightOption()
    {
        $this->picture_mcq_right_options[] = '';
    }

    public function removePictureMcqRightOption($index)
    {
        if (count($this->picture_mcq_right_options) > 1) {
            unset($this->picture_mcq_right_options[$index]);
            $this->picture_mcq_right_options = array_values($this->picture_mcq_right_options);
            
            foreach ($this->picture_mcq_correct_pairs as &$pair) {
                if (isset($pair['right']) && $pair['right'] >= $index) {
                    $pair['right'] = '';
                }
            }
        }
    }

    // Audio MCQ Single methods
    public function addAudioMcqSubQuestion()
    {
        $this->audio_mcq_sub_questions[] = [
            'question' => '',
            'options' => ['', ''],
            'correct_indices' => [0]
        ];
    }

    public function removeAudioMcqSubQuestion($index)
    {
        if (count($this->audio_mcq_sub_questions) > 1) {
            unset($this->audio_mcq_sub_questions[$index]);
            $this->audio_mcq_sub_questions = array_values($this->audio_mcq_sub_questions);
        }
    }

    public function addAudioMcqSubQuestionOption($subIndex)
    {
        if (count($this->audio_mcq_sub_questions[$subIndex]['options']) < 6) {
            $this->audio_mcq_sub_questions[$subIndex]['options'][] = '';
        }
    }

    public function removeAudioMcqSubQuestionOption($subIndex, $optIndex)
    {
        if (count($this->audio_mcq_sub_questions[$subIndex]['options']) > 2) {
            unset($this->audio_mcq_sub_questions[$subIndex]['options'][$optIndex]);
            $this->audio_mcq_sub_questions[$subIndex]['options'] = array_values($this->audio_mcq_sub_questions[$subIndex]['options']);
            
            $maxIndex = count($this->audio_mcq_sub_questions[$subIndex]['options']) - 1;
            $this->audio_mcq_sub_questions[$subIndex]['correct_indices'] = array_filter(
                $this->audio_mcq_sub_questions[$subIndex]['correct_indices'],
                function($index) use ($maxIndex) {
                    return $index <= $maxIndex;
                }
            );
            
            $this->audio_mcq_sub_questions[$subIndex]['correct_indices'] = array_values($this->audio_mcq_sub_questions[$subIndex]['correct_indices']);
            
            if (empty($this->audio_mcq_sub_questions[$subIndex]['correct_indices'])) {
                $this->audio_mcq_sub_questions[$subIndex]['correct_indices'] = [0];
            }
        }
    }

    public function addAudioMcqSubQuestionAnswerIndex($subIndex)
    {
        $this->audio_mcq_sub_questions[$subIndex]['correct_indices'][] = 0;
    }

    public function removeAudioMcqSubQuestionAnswerIndex($subIndex, $ansIndex)
    {
        if (count($this->audio_mcq_sub_questions[$subIndex]['correct_indices']) > 1) {
            unset($this->audio_mcq_sub_questions[$subIndex]['correct_indices'][$ansIndex]);
            $this->audio_mcq_sub_questions[$subIndex]['correct_indices'] = array_values($this->audio_mcq_sub_questions[$subIndex]['correct_indices']);
        }
    }

    // Audio Image Text Single methods
    public function addAudioImageTextImage()
    {
        $this->audio_image_text_image_uploads[] = null;
        $this->audio_image_text_images[] = '';
    }

    public function removeAudioImageTextImage($index)
    {
        if (count($this->audio_image_text_image_uploads) > 1) {
            unset($this->audio_image_text_image_uploads[$index]);
            unset($this->audio_image_text_images[$index]);
            $this->audio_image_text_image_uploads = array_values($this->audio_image_text_image_uploads);
            $this->audio_image_text_images = array_values($this->audio_image_text_images);
            
            foreach ($this->audio_image_text_correct_pairs as &$pair) {
                if (isset($pair['left']) && $pair['left'] >= $index) {
                    $pair['left'] = '';
                }
            }
        }
    }

    public function addAudioImageTextRightOption()
    {
        $this->audio_image_text_right_options[] = '';
    }

    public function removeAudioImageTextRightOption($index)
    {
        if (count($this->audio_image_text_right_options) > 1) {
            unset($this->audio_image_text_right_options[$index]);
            $this->audio_image_text_right_options = array_values($this->audio_image_text_right_options);
            
            foreach ($this->audio_image_text_correct_pairs as &$pair) {
                if (isset($pair['right']) && $pair['right'] >= $index) {
                    $pair['right'] = '';
                }
            }
        }
    }

    // Audio Image Text Multiple methods
    public function addAudioImageTextMultiplePair()
    {
        $this->audio_image_text_multiple_pairs[] = [
            'image' => null,
            'audio' => null
        ];
    }

    public function removeAudioImageTextMultiplePair($index)
    {
        if (count($this->audio_image_text_multiple_pairs) > 1) {
            unset($this->audio_image_text_multiple_pairs[$index]);
            unset($this->audio_image_text_multiple_existing_pairs[$index]);
            $this->audio_image_text_multiple_pairs = array_values($this->audio_image_text_multiple_pairs);
            $this->audio_image_text_multiple_existing_pairs = array_values($this->audio_image_text_multiple_existing_pairs);
            
            foreach ($this->audio_image_text_multiple_correct_pairs as &$pair) {
                if (isset($pair['left']) && $pair['left'] >= $index) {
                    $pair['left'] = '';
                }
            }
        }
    }

    public function addAudioImageTextMultipleRightOption()
    {
        $this->audio_image_text_multiple_right_options[] = '';
    }

    public function removeAudioImageTextMultipleRightOption($index)
    {
        if (count($this->audio_image_text_multiple_right_options) > 1) {
            unset($this->audio_image_text_multiple_right_options[$index]);
            $this->audio_image_text_multiple_right_options = array_values($this->audio_image_text_multiple_right_options);
            
            foreach ($this->audio_image_text_multiple_correct_pairs as &$pair) {
                if (isset($pair['right']) && $pair['right'] >= $index) {
                    $pair['right'] = '';
                }
            }
        }
    }

    public function addAudioImageTextMultipleCorrectPair()
    {
        $this->audio_image_text_multiple_correct_pairs[] = ['left' => '', 'right' => ''];
    }

    public function removeAudioImageTextMultipleCorrectPair($index)
    {
        if (count($this->audio_image_text_multiple_correct_pairs) > 1) {
            unset($this->audio_image_text_multiple_correct_pairs[$index]);
            $this->audio_image_text_multiple_correct_pairs = array_values($this->audio_image_text_multiple_correct_pairs);
        }
    }

    // Picture MCQ Pair Management Methods
public function addPictureMcqPair()
{
    $this->picture_mcq_correct_pairs[] = ['left' => '', 'right' => ''];
}

public function removePictureMcqPair($index)
{
    if (count($this->picture_mcq_correct_pairs) > 1) {
        unset($this->picture_mcq_correct_pairs[$index]);
        $this->picture_mcq_correct_pairs = array_values($this->picture_mcq_correct_pairs);
    }
}

// Audio Image Text Single Pair Management Methods
public function addAudioImageTextPair()
{
    $this->audio_image_text_correct_pairs[] = ['left' => '', 'right' => ''];
}

public function removeAudioImageTextPair($index)
{
    if (count($this->audio_image_text_correct_pairs) > 1) {
        unset($this->audio_image_text_correct_pairs[$index]);
        $this->audio_image_text_correct_pairs = array_values($this->audio_image_text_correct_pairs);
    }
}

// Audio Image Text Multiple Pair Management Methods  
public function addAudioImageTextMultiplePair_Answer()
{
    $this->audio_image_text_multiple_correct_pairs[] = ['left' => '', 'right' => ''];
}

public function removeAudioImageTextMultiplePair_Answer($index)
{
    if (count($this->audio_image_text_multiple_correct_pairs) > 1) {
        unset($this->audio_image_text_multiple_correct_pairs[$index]);
        $this->audio_image_text_multiple_correct_pairs = array_values($this->audio_image_text_multiple_correct_pairs);
    }
}

    // Validation methods
    public function updatedLeftOptions()
    {
        $this->validateOnly('left_options');
    }

    public function updatedRightOptions()
    {
        $this->validateOnly('right_options');
    }

    public function updatedOpinionAnswer()
    {
        $this->validateOnly('opinion_answer');
    }

    public function updatedSubQuestions()
    {
        $this->validateOnly('sub_questions');
    }

    public function updatedTrueFalseQuestions()
    {
        $this->validateOnly('true_false_questions');
    }

    public function updatedTrueFalseStatement()
    {
        $this->validateOnly('true_false_statement');
    }

    public function updatedTrueFalseAnswer()
    {
        $this->validateOnly('true_false_answer');
    }

    public function updatedReorderFragments()
    {
        $this->validateOnly('reorder_fragments');
    }

    public function updatedReorderAnswerKey()
    {
        $this->validateOnly('reorder_answer_key');
    }

    public function updatedFormFillParagraph()
    {
        $this->adjustAnswerKeysToBlankCount();
        $this->validateOnly('form_fill_paragraph');
    }

    public function updatedFormFillOptions()
    {
        $this->validateOnly('form_fill_options');
    }

    public function updatedFormFillAnswerKey()
    {
        $this->validateOnly('form_fill_answer_key');
    }

    public function updatedPictureMcqRightOptions()
    {
        $this->validateOnly('picture_mcq_right_options');
    }

    public function updatedPictureMcqImageUploads()
    {
        $this->validateOnly('picture_mcq_image_uploads');
    }

    public function updatedAudioMcqSubQuestions()
    {
        $this->validateOnly('audio_mcq_sub_questions');
    }

    public function updatedAudioMcqFile()
    {
        $this->validateOnly('audio_mcq_file');
    }

    public function updatedAudioImageTextRightOptions()
    {
        $this->validateOnly('audio_image_text_right_options');
    }

    public function updatedAudioImageTextImageUploads()
    {
        $this->validateOnly('audio_image_text_image_uploads');
    }

    public function updatedAudioImageTextAudioFile()
    {
        $this->validateOnly('audio_image_text_audio_file');
    }

    public function updatedAudioImageTextMultipleRightOptions()
    {
        $this->validateOnly('audio_image_text_multiple_right_options');
    }

    public function updatedAudioImageTextMultiplePairs()
    {
        $this->validateOnly('audio_image_text_multiple_pairs');
    }

    public function update()
    {
        // Find or create the Day by day_number and course_id
        // Create Day with fallback approach
try {
    $day = \App\Models\Day::firstOrCreate(
        [
            'number' => $this->day_number_input,
            'course_id' => $this->course_id,
        ],
        [
            'name' => 'Day ' . $this->day_number_input,
            'title' => 'Day ' . $this->day_number_input,
        ]
    );
} catch (\Exception $e) {
    try {
        $day = \App\Models\Day::firstOrCreate(
            [
                'number' => $this->day_number_input,
            ],
            [
                'title' => 'Day ' . $this->day_number_input,
            ]
        );
        
        try {
            $day->course_id = $this->course_id;
            $day->save();
        } catch (\Exception $e2) {
            // course_id column doesn't exist, continue
        }
    } catch (\Exception $e2) {
        try {
            $day = \App\Models\Day::firstOrCreate(
                [
                    'title' => 'Day ' . $this->day_number_input,
                ]
            );
            
            try {
                $day->number = $this->day_number_input;
                $day->course_id = $this->course_id;
                $day->save();
            } catch (\Exception $e3) {
                // Some columns don't exist, continue
            }
        } catch (\Exception $e3) {
            try {
                $dayId = \DB::table('days')->insertGetId([
                    'title' => 'Day ' . $this->day_number_input,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                try {
                    \DB::table('days')->where('id', $dayId)->update([
                        'number' => $this->day_number_input,
                        'course_id' => $this->course_id,
                    ]);
                } catch (\Exception $e4) {
                    // Additional fields don't exist, continue
                }
                
                $day = \App\Models\Day::find($dayId);
            } catch (\Exception $e4) {
                $day = new \App\Models\Day();
                $day->title = 'Day ' . $this->day_number_input;
                
                try {
                    $day->number = $this->day_number_input;
                    $day->course_id = $this->course_id;
                } catch (\Exception $e5) {
                    // Fields don't exist, continue
                }
                
                $day->save();
                    }
                }
            }
        }
        $this->day_id = $day->id;

        // Validate required fields
        if (empty($this->day_id) || empty($this->course_id) || empty($this->subject_id) || 
            empty($this->question_type_id) || empty($this->instruction)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please fill in all required fields.')
                ->danger()
                ->send();
            return;
        }

        // Check question types
        $isStatementMatch = ($this->question_type_id === 'statement_match');
        $isOpinion = ($this->question_type_id === 'opinion');
        $isMcqMultiple = ($this->question_type_id === 'mcq_multiple');
        $isTrueFalseMultiple = ($this->question_type_id === 'true_false_multiple');
        $isTrueFalse = ($this->question_type_id === 'true_false');
        $isReorder = ($this->question_type_id === 'reorder');
        $isFormFill = ($this->question_type_id === 'form_fill');
        $isPictureMcq = ($this->question_type_id === 'picture_mcq');
        $isAudioMcqSingle = ($this->question_type_id === 'audio_mcq_single');
        $isAudioImageTextSingle = ($this->question_type_id === 'audio_image_text_single');
        $isAudioImageTextMultiple = ($this->question_type_id === 'audio_image_text_multiple');
        $isAudioFillBlank = ($this->question_type_id === 'audio_fill_blank');
        $isPictureFillBlank = ($this->question_type_id === 'picture_fill_blank');
        $isVideoFillBlank = ($this->question_type_id === 'video_fill_blank');
        $isAudioPictureMatch = ($this->question_type_id === 'audio_picture_match');

        if ($isAudioFillBlank) {
            return $this->updateAudioFillBlank();
        } elseif ($isAudioImageTextMultiple) {
            return $this->updateAudioImageTextMultiple();
        } elseif ($isAudioImageTextSingle) {
            return $this->updateAudioImageTextSingle();
        } elseif ($isAudioMcqSingle) {
            return $this->updateAudioMcqSingle();
        } elseif ($isPictureMcq) {
            return $this->updatePictureMcq();
        } elseif ($isTrueFalse) {
            return $this->updateTrueFalse();
        } elseif ($isFormFill) {
            return $this->updateFormFill();
        } elseif ($isReorder) {
            return $this->updateReorder();
        } elseif ($isTrueFalseMultiple) {
            return $this->updateTrueFalseMultiple();
        } elseif ($isMcqMultiple) {
            return $this->updateMcqMultiple();
        } elseif ($isOpinion) {
            return $this->updateOpinion();
        } elseif ($isStatementMatch) {
            return $this->updateStatementMatch();
        } elseif ($isPictureFillBlank) {
            return $this->updatePictureFillBlank();
        } elseif ($isVideoFillBlank) {
            return $this->updateVideoFillBlank();
        } elseif ($isAudioPictureMatch) {
            return $this->updateAudioPictureMatch();
        } else {
            return $this->updateRegularMcq();
        }
    }

    private function updateAudioImageTextMultiple()
{
    $rightOptions = array_filter($this->audio_image_text_multiple_right_options, fn($v) => trim($v) !== '');
    $pairs = array_filter($this->audio_image_text_multiple_correct_pairs, function($pair) {
        return isset($pair['left'], $pair['right']) && 
               $pair['left'] !== '' && $pair['right'] !== '' &&
               $pair['left'] !== null && $pair['right'] !== null;
    });
    $pairs = array_map(function($pair) {
        $pair['left'] = (int) $pair['left'];
        $pair['right'] = (int) $pair['right'];
        return $pair;
    }, $pairs);

    // Handle existing and new audio pairs (image is optional)
    $uploadedPairs = [];
    $validPairCount = 0;
    
    foreach ($this->audio_image_text_multiple_pairs as $index => $pair) {
        $hasNewImage = !empty($pair['image']);
        $hasNewAudio = !empty($pair['audio']);
        $hasExistingData = !empty($this->audio_image_text_multiple_existing_pairs[$index] ?? null);
        
        // Check existing audio and image
        $hasExistingAudio = $hasExistingData && !empty($this->audio_image_text_multiple_existing_pairs[$index]['audio']);
        $hasExistingImage = $hasExistingData && !empty($this->audio_image_text_multiple_existing_pairs[$index]['image']);
        
        // Audio is required (either new or existing), image is optional
        $finalAudio = null;
        $finalImage = null;
        
        // Handle audio (required)
        if ($hasNewAudio) {
            $finalAudio = $pair['audio']->store('question-audio', 'public');
        } elseif ($hasExistingAudio) {
            $finalAudio = $this->audio_image_text_multiple_existing_pairs[$index]['audio'];
        }
        
        // Handle image (optional)
        if ($hasNewImage) {
            $finalImage = $pair['image']->store('question-images', 'public');
        } elseif ($hasExistingImage) {
            $finalImage = $this->audio_image_text_multiple_existing_pairs[$index]['image'];
        }
        
        // Only add pair if it has audio (image is optional)
        if ($finalAudio) {
            $validPairCount++;
            $uploadedPairs[$index] = [
                'audio' => $finalAudio,
                'image' => $finalImage // Can be null
            ];
        }
    }

    if ($validPairCount < 2 || count($rightOptions) < 2) {
        Notification::make()
            ->title('Validation Error')
            ->body('Please ensure at least 2 audio pairs and 2 text options exist.')
            ->danger()
            ->send();
        return;
    }

    if (count($pairs) < 2) {
        Notification::make()
            ->title('Validation Error')
            ->body('Please select at least 2 correct pairs.')
            ->danger()
            ->send();
        return;
    }

    $explanationFilePath = $this->explanation;
    if ($this->explanation_file) {
        $explanationFilePath = $this->explanation_file->store('explanations', 'public');
    }

    $questionType = QuestionType::firstOrCreate(['name' => 'audio_image_text_multiple']);

    $this->record->update([
        'day_id' => $this->day_id,
        'course_id' => $this->course_id,
        'subject_id' => $this->subject_id,
            'topic' => $this->topic,
        'question_type_id' => $questionType->id,
        'instruction' => $this->instruction,
        'explanation' => $explanationFilePath,
        'points' => $this->points ?: 1,
        'is_active' => $this->is_active,
        'question_data' => json_encode([
            'main_instruction' => $this->instruction,
            'image_audio_pairs' => array_values($uploadedPairs),
            'right_options' => array_values($rightOptions)
        ]),
        'answer_data' => json_encode([
            'correct_pairs' => array_values($pairs)
        ]),
        'right_options' => array_values($rightOptions),
        'correct_pairs' => array_values($pairs),
        'audio_image_text_multiple_pairs' => array_values($uploadedPairs),
        'test_id' => $this->validateTestId($this->test_id),
    ]);

    $this->showSuccessAndRedirect('audio image text multiple question');
}

private function updateAudioImageTextSingle()
{
    $rightOptions = array_filter($this->audio_image_text_right_options, fn($v) => trim($v) !== '');
    $pairs = array_filter($this->audio_image_text_correct_pairs, function($pair) {
        return isset($pair['left'], $pair['right']) && 
               $pair['left'] !== '' && $pair['right'] !== '' &&
               $pair['left'] !== null && $pair['right'] !== null;
    });
    
    $pairs = array_map(function($pair) {
        $pair['left'] = (int) $pair['left'];
        $pair['right'] = (int) $pair['right'];
        return $pair;
    }, $pairs);

    // Handle existing and new images
    $uploadedImages = [];
    $validImageCount = 0;
    
    foreach ($this->audio_image_text_image_uploads as $index => $imageFile) {
        if ($imageFile) {
            // New image uploaded
            $validImageCount++;
            $imagePath = $imageFile->store('question-images', 'public');
            $uploadedImages[$index] = $imagePath;
        } elseif (isset($this->audio_image_text_images[$index])) {
            // Keep existing image
            $validImageCount++;
            $uploadedImages[$index] = $this->audio_image_text_images[$index];
        }
    }

    if ($validImageCount < 2 || count($rightOptions) < 2) {
        Notification::make()
            ->title('Validation Error')
            ->body('Please ensure at least 2 images and 2 text options exist.')
            ->danger()
            ->send();
        return;
    }

    // Changed validation: Allow at least 1 pair instead of exactly 2
    if (count($pairs) < 1) {
        Notification::make()
            ->title('Validation Error')
            ->body('Please select at least 1 correct pair.')
            ->danger()
            ->send();
        return;
    }

    $explanationFilePath = $this->explanation;
    if ($this->explanation_file) {
        $explanationFilePath = $this->explanation_file->store('explanations', 'public');
    }

    // Handle audio file
    $audioFilePath = null;
    if ($this->record->question_data) {
        $questionData = json_decode($this->record->question_data, true);
        $audioFilePath = $questionData['audio_file'] ?? null;
    }
    if (!$audioFilePath && $this->record->audio_image_text_audio_file) {
        $audioFilePath = $this->record->audio_image_text_audio_file;
    }
    
    if ($this->audio_image_text_audio_file) {
        $audioFilePath = $this->audio_image_text_audio_file->store('question-audio', 'public');
    }

    $questionType = QuestionType::firstOrCreate(['name' => 'audio_image_text_single']);
    
    $this->record->update([
        'day_id' => $this->day_id,
        'course_id' => $this->course_id,
        'subject_id' => $this->subject_id,
            'topic' => $this->topic,
        'question_type_id' => $questionType->id,
        'instruction' => $this->instruction,
        'explanation' => $explanationFilePath,
        'points' => $this->points ?: 1,
        'is_active' => $this->is_active,
        'question_data' => json_encode([
            'main_instruction' => $this->instruction,
            'audio_file' => $audioFilePath,
            'images' => array_values($uploadedImages),
            'right_options' => array_values($rightOptions)
        ]),
        'answer_data' => json_encode([
            'correct_pairs' => array_values($pairs)
        ]),
        'right_options' => array_values($rightOptions),
        'correct_pairs' => array_values($pairs),
        'audio_image_text_images' => array_values($uploadedImages),
        'audio_image_text_audio_file' => $audioFilePath,
        'test_id' => $this->validateTestId($this->test_id),
    ]);
    
    $this->showSuccessAndRedirect('audio image text single question');
}

    private function updateAudioMcqSingle()
    {
        $validatedAudioSubQuestions = [];
        
        foreach ($this->audio_mcq_sub_questions as $index => $subQuestion) {
            if (empty(trim($subQuestion['question'] ?? ''))) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Sub-question " . chr(97 + $index) . ") text is required.")
                    ->danger()
                    ->send();
                return;
            }
            
            $options = array_filter($subQuestion['options'] ?? [], fn($v) => trim($v) !== '');
            if (count($options) < 2) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Sub-question " . chr(97 + $index) . ") must have at least 2 options.")
                    ->danger()
                    ->send();
                return;
            }
            
            $correctIndices = array_filter($subQuestion['correct_indices'] ?? [], function($value) {
                return $value !== '' && $value !== null && is_numeric($value);
            });
            
            if (empty($correctIndices)) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Sub-question " . chr(97 + $index) . ") must have at least one correct answer.")
                    ->danger()
                    ->send();
                return;
            }
            
            foreach ($correctIndices as $correctIndex) {
                if ($correctIndex >= count($options)) {
                    Notification::make()
                        ->title('Validation Error')
                        ->body("Sub-question " . chr(97 + $index) . "): Answer index {$correctIndex} exceeds available options count.")
                        ->danger()
                        ->send();
                    return;
                }
            }
            
            $validatedAudioSubQuestions[] = [
                'question' => trim($subQuestion['question']),
                'options' => array_values($options),
                'correct_indices' => array_map('intval', array_values($correctIndices))
            ];
        }
        
        if (empty($validatedAudioSubQuestions)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least one sub-question.')
                ->danger()
                ->send();
            return;
        }
        
        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        // Handle audio file - keep existing if no new one uploaded
        $existingAudioFile = null;
        if ($this->record->question_data) {
            $questionData = json_decode($this->record->question_data, true);
            $existingAudioFile = $questionData['audio_file'] ?? null;
        }
        
        $audioFilePath = $existingAudioFile;
        if ($this->audio_mcq_file) {
            $audioFilePath = $this->audio_mcq_file->store('question-audio', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'audio_mcq_single']);
        
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'audio_file' => $audioFilePath,
                'sub_questions' => $validatedAudioSubQuestions
            ]),
            'answer_data' => json_encode([
                'sub_questions_answers' => array_map(function($subQ) {
                    return $subQ['correct_indices'];
                }, $validatedAudioSubQuestions)
            ]),
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        
        $this->showSuccessAndRedirect('Audio MCQ Single question');
    }

    private function updatePictureMcq()
    {
        $rightOptions = array_filter($this->picture_mcq_right_options, fn($v) => trim($v) !== '');
        $pairs = array_filter($this->picture_mcq_correct_pairs, function($pair) {
            return isset($pair['left'], $pair['right']) && 
                   $pair['left'] !== '' && $pair['right'] !== '' &&
                   $pair['left'] !== null && $pair['right'] !== null;
        });
        
        $pairs = array_map(function($pair) {
            $pair['left'] = (int) $pair['left'];
            $pair['right'] = (int) $pair['right'];
            return $pair;
        }, $pairs);

        // Handle images - use uploads if present, otherwise keep existing
        $uploadedImages = [];
        $validImageCount = 0;
        
        foreach ($this->picture_mcq_image_uploads as $index => $imageFile) {
            if ($imageFile) {
                // New image uploaded
                $validImageCount++;
                $imagePath = $imageFile->store('question-images', 'public');
                $uploadedImages[$index] = $imagePath;
            } elseif (isset($this->picture_mcq_images[$index]) && !empty($this->picture_mcq_images[$index])) {
                // Keep existing image
                $validImageCount++;
                $uploadedImages[$index] = $this->picture_mcq_images[$index];
            }
        }

        if ($validImageCount < 2 || count($rightOptions) < 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please ensure at least 2 images and 2 text options exist.')
                ->danger()
                ->send();
            return;
        }

        if (count($pairs) !== 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select exactly 2 correct pairs.')
                ->danger()
                ->send();
            return;
        }

        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        $questionType = QuestionType::firstOrCreate(['name' => 'picture_mcq']);
        
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'images' => array_values($uploadedImages),
                'right_options' => array_values($rightOptions)
            ]),
            'answer_data' => json_encode([
                'correct_pairs' => array_values($pairs)
            ]),
            'right_options' => array_values($rightOptions),
            'correct_pairs' => array_values($pairs),
            'picture_mcq_images' => array_values($uploadedImages),
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        
        $this->showSuccessAndRedirect('Picture MCQ question');
    }

    private function updateTrueFalse()
    {
        $statement = trim($this->true_false_statement);
        $answer = $this->true_false_answer;
        
        if (empty($statement)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please provide the true/false statement.')
                ->danger()
                ->send();
            return;
        }
        
        if (empty($answer) || !in_array($answer, ['true', 'false'])) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select the correct answer (True or False).')
                ->danger()
                ->send();
            return;
        }
        
        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'true_false']);
        
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'statement' => $statement,
                'options' => ['True', 'False']
            ]),
            'answer_data' => json_encode([
                'correct_answer' => $answer,
                'correct_indices' => $answer === 'true' ? [0] : [1]
            ]),
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        
        $this->showSuccessAndRedirect('True/False question');
    }

    private function updateFormFill()
    {
        $paragraph = trim($this->form_fill_paragraph);
        $options = array_filter($this->form_fill_options, fn($v) => trim($v) !== '');
        $answerKeys = array_filter($this->form_fill_answer_key, fn($v) => trim($v) !== '');
        
        if (empty($paragraph)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please provide the paragraph with blanks marked as ___ (three underscores).')
                ->danger()
                ->send();
            return;
        }
        
        $blankCount = substr_count($paragraph, '___');
        
        if ($blankCount === 0) {
            Notification::make()
                ->title('Validation Error')
                ->body('Paragraph must contain at least one blank marked as ___ (three underscores).')
                ->danger()
                ->send();
            return;
        }
        
        if (count($options) < 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least 2 options for the form fill question.')
                ->danger()
                ->send();
            return;
        }
        
        if (count($answerKeys) !== $blankCount) {
            Notification::make()
                ->title('Validation Error')
                ->body("Please provide exactly {$blankCount} answer key(s) to match the number of blanks in the paragraph.")
                ->danger()
                ->send();
            return;
        }
        
        foreach ($answerKeys as $index => $answerKey) {
            if (!in_array(trim($answerKey), $options)) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Answer key " . ($index + 1) . " ('{$answerKey}') does not match any of the provided options.")
                    ->danger()
                    ->send();
                return;
            }
        }
        
        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'form_fill']);
        
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'paragraph' => $paragraph,
                'options' => array_values($options),
                'blank_count' => $blankCount
            ]),
            'answer_data' => json_encode([
                'answer_keys' => array_values($answerKeys),
                'blank_count' => $blankCount
            ]),
            'form_fill_paragraph' => $paragraph,
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        
        $this->showSuccessAndRedirect('form fill question');
    }

    private function updateReorder()
    {
        $fragments = array_filter($this->reorder_fragments, fn($v) => trim($v) !== '');
        $answerKey = trim($this->reorder_answer_key);
        
        if (count($fragments) < 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least 2 sentence fragments for reordering.')
                ->danger()
                ->send();
            return;
        }
        
        if (empty($answerKey)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please provide the answer key (correct sentence).')
                ->danger()
                ->send();
            return;
        }
        
        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'reorder']);
        
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'fragments' => array_values($fragments)
            ]),
            'answer_data' => json_encode([
                'answer_key' => $answerKey,
                'fragments_count' => count($fragments)
            ]),
            'reorder_fragments' => array_values($fragments),
            'reorder_answer_key' => $answerKey,
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        
        $this->showSuccessAndRedirect('sentence reordering question');
    }

    private function updateTrueFalseMultiple()
    {
        $validatedTrueFalseQuestions = [];
        
        foreach ($this->true_false_questions as $index => $tfQuestion) {
            if (empty(trim($tfQuestion['statement'] ?? ''))) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Statement " . chr(97 + $index) . ") text is required.")
                    ->danger()
                    ->send();
                return;
            }
            
            if (empty($tfQuestion['correct_answer']) || !in_array($tfQuestion['correct_answer'], ['true', 'false'])) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Statement " . chr(97 + $index) . ") must have a correct answer selected (True or False).")
                    ->danger()
                    ->send();
                return;
            }
            
            $validatedTrueFalseQuestions[] = [
                'statement' => trim($tfQuestion['statement']),
                'text' => trim($tfQuestion['statement']),
                'options' => ['True', 'False'],
                'correct_answer' => $tfQuestion['correct_answer'],
                'correct_indices' => $tfQuestion['correct_answer'] === 'true' ? [0] : [1]
            ];
        }
        
        if (empty($validatedTrueFalseQuestions)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least one True/False statement.')
                ->danger()
                ->send();
            return;
        }
        
        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'true_false_multiple']);
        
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'questions' => $validatedTrueFalseQuestions
            ]),
            'answer_data' => json_encode([
                'true_false_answers' => array_map(function($tfQ) {
                    return [
                        'correct_answer' => $tfQ['correct_answer'],
                        'correct_indices' => $tfQ['correct_indices']
                    ];
                }, $validatedTrueFalseQuestions)
            ]),
            'true_false_questions' => $validatedTrueFalseQuestions,
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        
        $this->showSuccessAndRedirect('True/False Multiple question');
    }

    private function updateMcqMultiple()
    {
        $validatedSubQuestions = [];
        
        foreach ($this->sub_questions as $index => $subQuestion) {
            if (empty(trim($subQuestion['question'] ?? ''))) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Sub-question " . chr(97 + $index) . ") text is required.")
                    ->danger()
                    ->send();
                return;
            }
            
            $options = array_filter($subQuestion['options'] ?? [], fn($v) => trim($v) !== '');
            if (count($options) < 2) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Sub-question " . chr(97 + $index) . ") must have at least 2 options.")
                    ->danger()
                    ->send();
                return;
            }
            
            $correctIndices = array_filter($subQuestion['correct_indices'] ?? [], function($value) {
                return $value !== '' && $value !== null && is_numeric($value);
            });
            
            if (empty($correctIndices)) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Sub-question " . chr(97 + $index) . ") must have at least one correct answer.")
                    ->danger()
                    ->send();
                return;
            }
            
            foreach ($correctIndices as $correctIndex) {
                if ($correctIndex >= count($options)) {
                    Notification::make()
                        ->title('Validation Error')
                        ->body("Sub-question " . chr(97 + $index) . "): Answer index {$correctIndex} exceeds available options count.")
                        ->danger()
                        ->send();
                    return;
                }
            }
            
            $validatedSubQuestions[] = [
                'question' => trim($subQuestion['question']),
                'options' => array_values($options),
                'correct_indices' => array_map('intval', array_values($correctIndices))
            ];
        }
        
        if (empty($validatedSubQuestions)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least one sub-question.')
                ->danger()
                ->send();
            return;
        }
        
        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::where('name', 'mcq_multiple')->first();
        if (!$questionType) {
            Notification::make()
                ->title('Validation Error')
                ->body('MCQ Multiple question type not found in database.')
                ->danger()
                ->send();
            return;
        }
        
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'sub_questions' => $validatedSubQuestions
            ]),
            'answer_data' => json_encode([
                'sub_questions_answers' => array_map(function($subQ) {
                    return $subQ['correct_indices'];
                }, $validatedSubQuestions)
            ]),
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        
        $this->showSuccessAndRedirect('MCQ Multiple question');
    }

    private function updateOpinion()
    {
        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::where('name', 'opinion')->first();
        if (!$questionType) {
            Notification::make()
                ->title('Validation Error')
                ->body('Opinion question type not found in database.')
                ->danger()
                ->send();
            return;
        }
        
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'opinion_answer' => $this->opinion_answer ?? ''
            ]),
            'answer_data' => json_encode([
                'opinion_answer' => $this->opinion_answer ?? ''
            ]),
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        
        $this->showSuccessAndRedirect('opinion question');
    }

    private function updateStatementMatch()
    {
        $leftOptions = array_filter($this->left_options, fn($v) => trim($v) !== '');
        $rightOptions = array_filter($this->right_options, fn($v) => trim($v) !== '');
        $pairs = array_filter($this->correct_pairs, function($pair) {
            return isset($pair['left'], $pair['right']) && 
                   $pair['left'] !== '' && $pair['right'] !== '' &&
                   $pair['left'] !== null && $pair['right'] !== null;
        });
        
        $pairs = array_map(function($pair) {
            $pair['left'] = (int) $pair['left'];
            $pair['right'] = (int) $pair['right'];
            return $pair;
        }, $pairs);

        if (count($leftOptions) < 2 || count($rightOptions) < 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least 2 left and 2 right options for statement match.')
                ->danger()
                ->send();
            return;
        }

        if (count($pairs) < 1) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select at least 1 correct pair.')
                ->danger()
                ->send();
            return;
        }

        $leftCount = count($leftOptions);
        $rightCount = count($rightOptions);
        
        foreach ($pairs as $index => $pair) {
            if ($pair['left'] >= $leftCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Left index {$pair['left']} is out of bounds (max: " . ($leftCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
            if ($pair['right'] >= $rightCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Right index {$pair['right']} is out of bounds (max: " . ($rightCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
        }

        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        $questionType = QuestionType::where('name', 'statement_match')->first();
        if (!$questionType) {
            Notification::make()
                ->title('Validation Error')
                ->body('Statement match question type not found in database.')
                ->danger()
                ->send();
            return;
        }

        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'left_options' => array_values($leftOptions),
            'right_options' => array_values($rightOptions),
            'correct_pairs' => array_values($pairs),
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'left_options' => array_values($leftOptions),
                'right_options' => array_values($rightOptions)
            ]),
            'answer_data' => json_encode([
                'correct_pairs' => array_values($pairs)
            ]),
            'test_id' => $this->validateTestId($this->test_id),
        ]);

        $this->showSuccessAndRedirect('statement match question');
    }

    private function updateRegularMcq()
    {
        $options = array_filter($this->options, fn($v) => trim($v) !== '');
        $answerIndices = array_filter($this->answer_indices, function($value) {
            return $value !== '' && $value !== null;
        });

        if (empty($options)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least one option.')
                ->danger()
                ->send();
            return;
        }

        if (empty($answerIndices)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least one answer index.')
                ->danger()
                ->send();
            return;
        }

        foreach ($answerIndices as $index) {
            if ($index >= count($options)) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Answer index {$index} exceeds available options count.")
                    ->danger()
                    ->send();
                return;
            }
        }

        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        // Ensure question_type_id is always an integer
        $questionTypeId = is_numeric($this->question_type_id)
            ? $this->question_type_id
            : (\App\Models\QuestionType::where('name', $this->question_type_id)->first()?->id ?? 1);

        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionTypeId,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'question' => $this->instruction,
                'options' => array_values($options),
            ]),
            'answer_data' => json_encode([
                'correct_indices' => array_map('intval', $answerIndices),
            ]),
            'test_id' => $this->validateTestId($this->test_id),
        ]);

        $this->showSuccessAndRedirect('question');
    }

    private function showSuccessAndRedirect($questionType)
    {
        Notification::make()
            ->title('Question updated successfully!')
            ->body("The {$questionType} has been updated.")
            ->success()
            ->send();
            
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
            'courses' => \App\Models\Course::all(),
            'subjects' => Subject::all(),
            'questionTypes' => QuestionType::all(),
            'tests' => \App\Models\Test::all(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Question';
    }

    public function getHeading(): string
    {
        return 'Question Editor';
    }

    public function getSubheading(): ?string
    {
        return 'Modify and update existing questions in your assessment bank.';
    }

    protected function hasLogo(): bool
    {
        return false;
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin/questions') => 'Questions',
            '' => 'Edit Question',
        ];
    }

    private function updateAudioFillBlank()
    {
        $paragraph = trim($this->audio_fill_paragraph);
        $answerKeys = array_filter($this->audio_fill_answer_key, fn($v) => trim($v) !== '');
        
        if (empty($paragraph)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please provide the paragraph with blanks marked as ___ (three underscores).')
                ->danger()
                ->send();
            return;
        }
        
        $blankCount = substr_count($paragraph, '___');
        
        if ($blankCount === 0) {
            Notification::make()
                ->title('Validation Error')
                ->body('Paragraph must contain at least one blank marked as ___ (three underscores).')
                ->danger()
                ->send();
            return;
        }
        
        if (count($answerKeys) !== $blankCount) {
            Notification::make()
                ->title('Validation Error')
                ->body("Please provide exactly {$blankCount} answer key(s) to match the number of blanks in the paragraph.")
                ->danger()
                ->send();
            return;
        }

        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        // Handle audio file - keep existing if no new one uploaded
        $existingAudioFile = null;
        if ($this->record->question_data) {
            $questionData = json_decode($this->record->question_data, true);
            $existingAudioFile = $questionData['audio_file'] ?? null;
        }
        
        $audioFilePath = $existingAudioFile;
        if ($this->audio_fill_audio_file) {
            $audioFilePath = $this->audio_fill_audio_file->store('question-audio', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'audio_fill_blank']);
        
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'paragraph' => $paragraph,
                'audio_file' => $audioFilePath,
                'blank_count' => $blankCount
            ]),
            'answer_data' => json_encode([
                'answer_keys' => array_values($answerKeys),
                'blank_count' => $blankCount
            ]),
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        
        $this->showSuccessAndRedirect('audio fill in the blank question');
    }

    // Audio Fill Blank methods
    public function addAudioFillAnswerKey()
    {
        $this->audio_fill_answer_key[] = '';
    }

    public function removeAudioFillAnswerKey($index)
    {
        if (count($this->audio_fill_answer_key) > 1) {
            unset($this->audio_fill_answer_key[$index]);
            $this->audio_fill_answer_key = array_values($this->audio_fill_answer_key);
        }
    }

    public function adjustAudioFillAnswerKeysToBlankCount()
    {
        if (!empty($this->audio_fill_paragraph)) {
            $blankCount = substr_count($this->audio_fill_paragraph, '___');
            $currentAnswerKeyCount = count($this->audio_fill_answer_key);
            
            if ($blankCount > $currentAnswerKeyCount) {
                for ($i = $currentAnswerKeyCount; $i < $blankCount; $i++) {
                    $this->audio_fill_answer_key[] = '';
                }
            } elseif ($blankCount < $currentAnswerKeyCount && $blankCount > 0) {
                $this->audio_fill_answer_key = array_slice($this->audio_fill_answer_key, 0, $blankCount);
            }
            
            if (empty($this->audio_fill_answer_key) || $blankCount === 0) {
                $this->audio_fill_answer_key = [''];
            }
        }
    }

    public function updatedAudioFillParagraph()
    {
        $this->adjustAudioFillAnswerKeysToBlankCount();
        $this->validateOnly('audio_fill_paragraph');
    }

    public function updatedAudioFillAnswerKey()
    {
        $this->validateOnly('audio_fill_answer_key');
    }

    public function updatedAudioFillAudioFile()
    {
        $this->validateOnly('audio_fill_audio_file');
    }

    private function updatePictureFillBlank()
    {
        $paragraph = trim($this->picture_fill_paragraph);
        $answerKeys = array_filter($this->picture_fill_answer_key, fn($v) => trim($v) !== '');
        if (empty($paragraph)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please provide the paragraph with blanks marked as ___ (three underscores).')
                ->danger()
                ->send();
            return;
        }
        $blankCount = substr_count($paragraph, '___');
        if ($blankCount === 0) {
            Notification::make()
                ->title('Validation Error')
                ->body('Paragraph must contain at least one blank marked as ___ (three underscores).')
                ->danger()
                ->send();
            return;
        }
        if (count($answerKeys) !== $blankCount) {
            Notification::make()
                ->title('Validation Error')
                ->body("Please provide exactly {$blankCount} answer key(s) to match the number of blanks in the paragraph.")
                ->danger()
                ->send();
            return;
        }
        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        // Handle image file - keep existing if no new one uploaded
        $existingImageFile = null;
        if ($this->record->question_data) {
            $questionData = json_decode($this->record->question_data, true);
            $existingImageFile = $questionData['image_file'] ?? null;
        }
        $imageFilePath = $existingImageFile;
        if ($this->picture_fill_image) {
            $imageFilePath = $this->picture_fill_image->store('question-images', 'public');
        }
        $questionType = QuestionType::firstOrCreate(['name' => 'picture_fill_blank']);
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'paragraph' => $paragraph,
                'image_file' => $imageFilePath,
                'blank_count' => $blankCount
            ]),
            'answer_data' => json_encode([
                'answer_keys' => array_values($answerKeys),
                'blank_count' => $blankCount
            ]),
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        $this->showSuccessAndRedirect('picture fill in the blank question');
    }
    public function addPictureFillAnswerKey()
    {
        $this->picture_fill_answer_key[] = '';
    }
    public function removePictureFillAnswerKey($index)
    {
        if (count($this->picture_fill_answer_key) > 1) {
            unset($this->picture_fill_answer_key[$index]);
            $this->picture_fill_answer_key = array_values($this->picture_fill_answer_key);
        }
    }
    public function adjustPictureFillAnswerKeysToBlankCount()
    {
        if (!empty($this->picture_fill_paragraph)) {
            $blankCount = substr_count($this->picture_fill_paragraph, '___');
            $currentAnswerKeyCount = count($this->picture_fill_answer_key);
            if ($blankCount > $currentAnswerKeyCount) {
                for ($i = $currentAnswerKeyCount; $i < $blankCount; $i++) {
                    $this->picture_fill_answer_key[] = '';
                }
            } elseif ($blankCount < $currentAnswerKeyCount && $blankCount > 0) {
                $this->picture_fill_answer_key = array_slice($this->picture_fill_answer_key, 0, $blankCount);
            }
            if (empty($this->picture_fill_answer_key) || $blankCount === 0) {
                $this->picture_fill_answer_key = [''];
            }
        }
    }
    public function updatedPictureFillParagraph()
    {
        $this->adjustPictureFillAnswerKeysToBlankCount();
        $this->validateOnly('picture_fill_paragraph');
    }
    public function updatedPictureFillAnswerKey()
    {
        $this->validateOnly('picture_fill_answer_key');
    }
    public function updatedPictureFillImage()
    {
        $this->validateOnly('picture_fill_image');
    }

    private function updateVideoFillBlank()
    {
        $paragraph = trim($this->video_fill_paragraph);
        $answerKeys = array_filter($this->video_fill_answer_key, fn($v) => trim($v) !== '');
        if (empty($paragraph)) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please provide the paragraph with blanks marked as ___ (three underscores).')
                ->danger()
                ->send();
            return;
        }
        $blankCount = substr_count($paragraph, '___');
        if ($blankCount === 0) {
            Notification::make()
                ->title('Validation Error')
                ->body('Paragraph must contain at least one blank marked as ___ (three underscores).')
                ->danger()
                ->send();
            return;
        }
        if (count($answerKeys) !== $blankCount) {
            Notification::make()
                ->title('Validation Error')
                ->body("Please provide exactly {$blankCount} answer key(s) to match the number of blanks in the paragraph.")
                ->danger()
                ->send();
            return;
        }
        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        // Handle video file - keep existing if no new one uploaded
        $existingVideoFile = null;
        if ($this->record->question_data) {
            $questionData = json_decode($this->record->question_data, true);
            $existingVideoFile = $questionData['video_file'] ?? null;
        }
        $videoFilePath = $existingVideoFile;
        if ($this->video_fill_video) {
            $videoFilePath = $this->video_fill_video->store('question-videos', 'public');
        }
        $questionType = QuestionType::firstOrCreate(['name' => 'video_fill_blank']);
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'paragraph' => $paragraph,
                'video_file' => $videoFilePath,
                'blank_count' => $blankCount
            ]),
            'answer_data' => json_encode([
                'answer_keys' => array_values($answerKeys),
                'blank_count' => $blankCount
            ]),
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->validateTestId($this->test_id),
        ]);
        $this->showSuccessAndRedirect('video fill in the blank question');
    }
    public function addVideoFillAnswerKey()
    {
        $this->video_fill_answer_key[] = '';
    }
    public function removeVideoFillAnswerKey($index)
    {
        if (count($this->video_fill_answer_key) > 1) {
            unset($this->video_fill_answer_key[$index]);
            $this->video_fill_answer_key = array_values($this->video_fill_answer_key);
        }
    }
    public function adjustVideoFillAnswerKeysToBlankCount()
    {
        if (!empty($this->video_fill_paragraph)) {
            $blankCount = substr_count($this->video_fill_paragraph, '___');
            $currentAnswerKeyCount = count($this->video_fill_answer_key);
            if ($blankCount > $currentAnswerKeyCount) {
                for ($i = $currentAnswerKeyCount; $i < $blankCount; $i++) {
                    $this->video_fill_answer_key[] = '';
                }
            } elseif ($blankCount < $currentAnswerKeyCount && $blankCount > 0) {
                $this->video_fill_answer_key = array_slice($this->video_fill_answer_key, 0, $blankCount);
            }
            if (empty($this->video_fill_answer_key) || $blankCount === 0) {
                $this->video_fill_answer_key = [''];
            }
        }
    }
    public function updatedVideoFillParagraph()
    {
        $this->adjustVideoFillAnswerKeysToBlankCount();
        $this->validateOnly('video_fill_paragraph');
    }
    public function updatedVideoFillAnswerKey()
    {
        $this->validateOnly('video_fill_answer_key');
    }
    public function updatedVideoFillVideo()
    {
        $this->validateOnly('video_fill_video');
    }

    public function addAudioPictureAudio()
    {
        $this->audio_picture_audios[] = null;
    }

    public function removeAudioPictureAudio($index)
    {
        if (count($this->audio_picture_audios) > 1) {
            array_splice($this->audio_picture_audios, $index, 1);
            // Reindex the array to ensure sequential indices
            $this->audio_picture_audios = array_values($this->audio_picture_audios);
            // Update pairs to remove references to deleted audio
            foreach ($this->audio_picture_pairs as $key => $pair) {
                if ($pair['left'] == $index) {
                    $this->audio_picture_pairs[$key]['left'] = '';
                } elseif ($pair['left'] > $index) {
                    $this->audio_picture_pairs[$key]['left'] = $pair['left'] - 1;
                }
            }
        }
    }

    public function addAudioPictureImage()
    {
        $this->audio_picture_images[] = null;
    }

    public function removeAudioPictureImage($index)
    {
        if (count($this->audio_picture_images) > 1) {
            array_splice($this->audio_picture_images, $index, 1);
            // Reindex the array to ensure sequential indices
            $this->audio_picture_images = array_values($this->audio_picture_images);
            // Update pairs to remove references to deleted image
            foreach ($this->audio_picture_pairs as $key => $pair) {
                if ($pair['right'] == $index) {
                    $this->audio_picture_pairs[$key]['right'] = '';
                } elseif ($pair['right'] > $index) {
                    $this->audio_picture_pairs[$key]['right'] = $pair['right'] - 1;
                }
            }
        }
    }

    public function addAudioPicturePair()
    {
        $this->audio_picture_pairs[] = ['left' => '', 'right' => ''];
    }

    public function removeAudioPicturePair($index)
    {
        if (count($this->audio_picture_pairs) > 1) {
            array_splice($this->audio_picture_pairs, $index, 1);
            // Reindex the array to ensure sequential indices
            $this->audio_picture_pairs = array_values($this->audio_picture_pairs);
        }
    }

    public function clearAllAudioPicturePairs()
    {
        $this->audio_picture_pairs = [['left' => '', 'right' => '']];
    }

    private function updateAudioPictureMatch()
    {
        // Count both new uploads and existing files
        $audioFiles = array_filter($this->audio_picture_audios, function($a) {
            return ($a instanceof \Illuminate\Http\UploadedFile) || (!empty($a) && is_string($a));
        });
        $imageFiles = array_filter($this->audio_picture_images, function($i) {
            return ($i instanceof \Illuminate\Http\UploadedFile) || (!empty($i) && is_string($i));
        });
        $pairs = array_filter($this->audio_picture_pairs, function($pair) {
            return isset($pair['left'], $pair['right']) && 
                   $pair['left'] !== '' && $pair['right'] !== '' && 
                   $pair['left'] !== null && $pair['right'] !== null;
        });

        if (count($audioFiles) < 2 || count($imageFiles) < 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least 2 audio files and 2 image files for audio-picture matching.')
                ->danger()
                ->send();
            return;
        }

        // Store audio files
        $audioPaths = [];
        foreach ($this->audio_picture_audios as $idx => $audioFile) {
            if ($audioFile instanceof \Illuminate\Http\UploadedFile) {
                $audioPaths[$idx] = $audioFile->store('question-audio', 'public');
            } else {
                $audioPaths[$idx] = $audioFile;
            }
        }

        // Store image files
        $imagePaths = [];
        foreach ($this->audio_picture_images as $idx => $imageFile) {
            if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
                $imagePaths[$idx] = $imageFile->store('question-images', 'public');
            } else {
                $imagePaths[$idx] = $imageFile;
            }
        }

        // Validate that all pairs reference valid indices
        $audioCount = count($audioPaths);
        $imageCount = count($imagePaths);
        foreach ($pairs as $index => $pair) {
            if ($pair['left'] >= $audioCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Audio index {$pair['left']} is out of bounds (max: " . ($audioCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
            if ($pair['right'] >= $imageCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Image index {$pair['right']} is out of bounds (max: " . ($imageCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
        }

        if (count($pairs) < 1) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select at least 1 correct pair for audio-picture matching.')
                ->danger()
                ->send();
            return;
        }

        $explanationFilePath = $this->explanation;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        $questionType = QuestionType::firstOrCreate(['name' => 'audio_picture_match']);
        $this->record->update([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionType->id,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'main_instruction' => $this->instruction,
                'audios' => array_values($audioPaths),
                'images' => array_values($imagePaths)
            ]),
            'answer_data' => json_encode([
                'correct_pairs' => array_values($pairs)
            ]),
            'test_id' => $this->validateTestId($this->test_id),
        ]);

        $this->showSuccessAndRedirect('audio picture matching question');
    }

    private function validateTestId($testId)
    {
        // If testId is a string, try to find the test by name
        if (is_string($testId)) {
            $test = \App\Models\Test::where('name', $testId)->first();
            return $test ? $test->id : null;
        }
        
        // If it's numeric, convert to integer
        if (is_numeric($testId)) {
            return (int)$testId;
        }
        
        // If it's null or empty, return null
        return null;
    }
}