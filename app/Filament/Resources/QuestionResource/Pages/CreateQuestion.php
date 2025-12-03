<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Day;
use App\Models\Level;
use App\Models\Subject;
use App\Models\QuestionType;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;

class CreateQuestion extends Page
{
    protected static string $resource = QuestionResource::class;
    protected static string $view = 'filament.resources.question-resource.pages.create-custom';

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
    public $number_input = 1;
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
    public $audio_image_text_image_uploads = [];
    public $audio_image_text_right_options = ['', ''];
    public $audio_image_text_correct_pairs = [
        ['left' => '', 'right' => ''],
        ['left' => '', 'right' => ''],
    ];

    // Properties for Audio Image Text Multiple
    public $audio_files = [null];
    public $image_files = [null];
    public $audio_image_text_multiple_right_options = ['', ''];
    public $audio_image_text_multiple_correct_pairs = [
        ['left' => '', 'right' => ''],
        ['left' => '', 'right' => ''],
    ];

    // Properties for Audio Fill in the Blanks
    public $audio_fill_paragraph = '';
    public $audio_fill_answer_key = [''];
    public $audio_file = null;

    // Properties for Picture Fill in the Blanks
    public $picture_fill_image = null;
    public $picture_fill_paragraph = '';
    public $picture_fill_answer_key = [''];

    // Properties for Video Fill in the Blanks
    public $video_fill_video = null;
    public $video_fill_paragraph = '';
    public $video_fill_answer_key = [''];

    // Properties for Audio + Picture Matching
    public $audio_picture_audios = [null];
    public $audio_picture_images = [null];
    public $audio_picture_pairs = [ ['left' => '', 'right' => ''] ];

    public $test_id = null;

    protected $listeners = ['refreshComponent' => '$refresh'];

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
        'audio_mcq_file' => 'nullable|max:25600',
        'audio_mcq_sub_questions' => 'array',
        'audio_mcq_sub_questions.*.question' => 'string|nullable',
        'audio_mcq_sub_questions.*.options' => 'array',
        'audio_mcq_sub_questions.*.options.*' => 'string|nullable',
        'audio_mcq_sub_questions.*.correct_indices' => 'array',
        'audio_mcq_sub_questions.*.correct_indices.*' => 'integer|nullable',
        
        // Audio Image Text validation rules
        'audio_image_text_audio_file' => 'nullable|max:25600',
        'audio_image_text_image_uploads.*' => 'nullable|image|max:2048',
        'audio_image_text_right_options' => 'array',
        'audio_image_text_right_options.*' => 'string|nullable',
        'audio_image_text_correct_pairs' => 'array',
        'audio_image_text_correct_pairs.*.left' => 'nullable',
        'audio_image_text_correct_pairs.*.right' => 'nullable',
        
        // Audio Image Text Multiple validation rules
        'audio_files.*' => 'nullable|max:25600',
        'image_files.*' => 'nullable|image|max:2048',
        'audio_image_text_multiple_right_options' => 'array',
        'audio_image_text_multiple_right_options.*' => 'string|nullable',
        'audio_image_text_multiple_correct_pairs' => 'array',
        'audio_image_text_multiple_correct_pairs.*.left' => 'nullable',
        'audio_image_text_multiple_correct_pairs.*.right' => 'nullable',
    ];

    public function mount(): void
    {
        $this->is_active = true;
        $this->points = 1;
        $this->answer_indices = [0];
        $this->opinion_answer = '';
        
        // Initialize MCQ Multiple with one sub-question
        $this->sub_questions = [
            [
                'question' => '',
                'options' => ['', ''],
                'correct_indices' => [0]
            ]
        ];
        // Initialize True/False Multiple with one question
        $this->true_false_questions = [
            [
                'statement' => '',
                'correct_answer' => ''
            ]
        ];
        // Initialize Simple True/False
        $this->true_false_statement = '';
        $this->true_false_answer = '';
        // Initialize Reorder with two fragments
        $this->reorder_fragments = ['', ''];
        $this->reorder_answer_key = '';
        // Initialize Form Fill
        $this->form_fill_paragraph = '';
        $this->form_fill_options = ['', ''];
        $this->form_fill_answer_key = [''];
        // Initialize Picture MCQ
        $this->picture_mcq_images = [];
        $this->picture_mcq_right_options = ['', ''];
        $this->picture_mcq_correct_pairs = [
            ['left' => '', 'right' => ''],
            ['left' => '', 'right' => ''],
        ];
        $this->picture_mcq_image_uploads = [null];
        // Initialize Audio MCQ Single
        $this->audio_mcq_file = null;
        $this->audio_mcq_sub_questions = [
            [
                'question' => '',
                'options' => ['', ''],
                'correct_indices' => [0]
            ]
        ];
        // Initialize Audio Image Text Single
        $this->audio_image_text_audio_file = null;
        $this->audio_image_text_image_uploads = [null];
        $this->audio_image_text_right_options = ['', ''];
        $this->audio_image_text_correct_pairs = [
            ['left' => '', 'right' => ''],
            ['left' => '', 'right' => ''],
        ];
        // Initialize Audio Image Text Multiple with separate arrays
        $this->audio_files = [null];
        $this->image_files = [null];
        $this->audio_image_text_multiple_right_options = ['', ''];
        $this->audio_image_text_multiple_correct_pairs = [
            ['left' => '', 'right' => ''],
            ['left' => '', 'right' => ''],
        ];
        // Initialize Audio Fill in the Blanks
        $this->audio_fill_paragraph = '';
        $this->audio_fill_answer_key = [''];
        $this->audio_file = null;
        // Initialize Picture Fill in the Blanks
        $this->picture_fill_image = null;
        $this->picture_fill_paragraph = '';
        $this->picture_fill_answer_key = [''];
        // Initialize Video Fill in the Blanks
        $this->video_fill_video = null;
        $this->video_fill_paragraph = '';
        $this->video_fill_answer_key = [''];
        // Initialize Audio + Picture Matching
        $this->audio_picture_audios = [null];
        $this->audio_picture_images = [null];
        $this->audio_picture_pairs = [ ['left' => '', 'right' => ''] ];
    }

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

    // FIXED: Picture MCQ methods with proper filtering
    public function addPictureMcqImage()
    {
        $this->picture_mcq_image_uploads[] = null;
    }

    public function removePictureMcqImage($index)
    {
        if (count($this->picture_mcq_image_uploads) > 1) {
            unset($this->picture_mcq_image_uploads[$index]);
            $this->picture_mcq_image_uploads = array_values($this->picture_mcq_image_uploads);
            
            // Reset any correct pairs that reference this index or higher
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
            
            // Reset any correct pairs that reference this index or higher
            foreach ($this->picture_mcq_correct_pairs as &$pair) {
                if (isset($pair['right']) && $pair['right'] >= $index) {
                    $pair['right'] = '';
                }
            }
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

    public function clearAllPictureMcqPairs()
    {
        $this->picture_mcq_correct_pairs = [['left' => '', 'right' => ''], ['left' => '', 'right' => '']];
    }

    // FIXED: Picture MCQ filtering methods
    public function getFilteredPictureMcqImages()
    {
        $filteredImages = [];
        foreach ($this->picture_mcq_image_uploads as $index => $imageFile) {
            if ($imageFile !== null) {
                $filteredImages[$index] = $imageFile;
            }
        }
        return $filteredImages;
    }

    public function getFilteredPictureMcqRightOptions()
    {
        $filteredOptions = [];
        foreach ($this->picture_mcq_right_options as $index => $option) {
            if (trim($option ?? '') !== '') {
                $filteredOptions[$index] = $option;
            }
        }
        return $filteredOptions;
    }

    // FIXED: Audio Image Text Single methods
    public function addAudioImageTextImage()
    {
        $this->audio_image_text_image_uploads[] = null;
    }

    public function removeAudioImageTextImage($index)
    {
        if (count($this->audio_image_text_image_uploads) > 1) {
            unset($this->audio_image_text_image_uploads[$index]);
            $this->audio_image_text_image_uploads = array_values($this->audio_image_text_image_uploads);
            
            // Reset any correct pairs that reference this index or higher
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
            
            // Reset any correct pairs that reference this index or higher
            foreach ($this->audio_image_text_correct_pairs as &$pair) {
                if (isset($pair['right']) && $pair['right'] >= $index) {
                    $pair['right'] = '';
                }
            }
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

    public function clearAllAudioImageTextPairs()
    {
        $this->audio_image_text_correct_pairs = [['left' => '', 'right' => ''], ['left' => '', 'right' => '']];
    }

    // FIXED: Audio Image Text Single filtering methods
    public function getFilteredAudioImageTextImages()
    {
        $filteredImages = [];
        foreach ($this->audio_image_text_image_uploads as $index => $imageFile) {
            if ($imageFile !== null) {
                $filteredImages[$index] = $imageFile;
            }
        }
        return $filteredImages;
    }

    public function getFilteredAudioImageTextRightOptions()
    {
        $filteredOptions = [];
        foreach ($this->audio_image_text_right_options as $index => $option) {
            if (trim($option ?? '') !== '') {
                $filteredOptions[$index] = $option;
            }
        }
        return $filteredOptions;
    }

    // Audio Image Text Multiple methods
    public function addAudioImageTextMultiplePair()
    {
        $this->audio_files[] = null;
        $this->image_files[] = null;
    }

    public function removeAudioImageTextMultiplePair($index)
    {
        if (count($this->audio_files) > 1) {
            unset($this->audio_files[$index]);
            unset($this->image_files[$index]);
            $this->audio_files = array_values($this->audio_files);
            $this->image_files = array_values($this->image_files);
            
            // Reset any correct pairs that reference this index or higher
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
            
            // Reset any correct pairs that reference this index or higher
            foreach ($this->audio_image_text_multiple_correct_pairs as &$pair) {
                if (isset($pair['right']) && $pair['right'] >= $index) {
                    $pair['right'] = '';
                }
            }
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

    public function clearAllAudioImageTextMultiplePairs()
    {
        $this->audio_image_text_multiple_correct_pairs = [['left' => '', 'right' => ''], ['left' => '', 'right' => '']];
    }

    // FIXED: Audio Image Text Multiple filtering methods
    public function getFilteredAudioImageTextMultiplePairs()
    {
        $audioFiles = $this->audio_files ?? [];
        $imageFiles = $this->image_files ?? [];
        
        $filteredPairs = [];
        $maxCount = max(count($audioFiles), count($imageFiles));
        
        for ($i = 0; $i < $maxCount; $i++) {
            $audioFile = $audioFiles[$i] ?? null;
            $imageFile = $imageFiles[$i] ?? null;
            
            // Include if at least audio file exists (audio is required, image is optional)
            if ($audioFile !== null) {
                $filteredPairs[$i] = [
                    'audio' => $audioFile,
                    'image' => $imageFile
                ];
            }
        }
        
        return $filteredPairs;
    }

    public function getFilteredAudioImageTextMultipleRightOptions()
    {
        $filteredOptions = [];
        foreach ($this->audio_image_text_multiple_right_options as $index => $option) {
            if (trim($option ?? '') !== '') {
                $filteredOptions[$index] = $option;
            }
        }
        return $filteredOptions;
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

    // Statement Match Pair Management Methods
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

    // FIXED: Statement Match filtering methods
    public function getFilteredLeftOptions()
    {
        $filteredOptions = [];
        foreach ($this->left_options as $index => $option) {
            if (trim($option ?? '') !== '') {
                $filteredOptions[$index] = $option;
            }
        }
        return $filteredOptions;
    }

    public function getFilteredRightOptions()
    {
        $filteredOptions = [];
        foreach ($this->right_options as $index => $option) {
            if (trim($option ?? '') !== '') {
                $filteredOptions[$index] = $option;
            }
        }
        return $filteredOptions;
    }

    // Add methods for statement match left/right options
    public function addLeftOption()
    {
        $this->left_options[] = '';
    }

    public function removeLeftOption($index)
    {
        unset($this->left_options[$index]);
        $this->left_options = array_values($this->left_options);
        // Reset any correct pairs that reference this index or higher
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
        // Reset any correct pairs that reference this index or higher
        foreach ($this->correct_pairs as &$pair) {
            if (isset($pair['right']) && $pair['right'] >= $index) {
                $pair['right'] = '';
            }
        }
    }

    // Method to automatically adjust answer keys based on blank count
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

    private function validateAudioFiles()
    {
        $errors = [];
        
        // Validate audio files
        foreach ($this->audio_files as $index => $audioFile) {
            if ($audioFile && $audioFile instanceof \Illuminate\Http\UploadedFile) {
                if ($audioFile->getSize() > 26214400) {
                    $errors["audio_files.{$index}"] = "Audio file must be smaller than 25MB.";
                    continue;
                }
                
                $extension = strtolower($audioFile->getClientOriginalExtension());
                $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
                
                if (!in_array($extension, $allowedExtensions)) {
                    $errors["audio_files.{$index}"] = "Audio file must be mp3, wav, ogg, m4a, or aac format.";
                    continue;
                }
                
                $mimeType = $audioFile->getMimeType();
                $allowedMimes = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/aac', 'audio/x-m4a'];
                
                if (!in_array($mimeType, $allowedMimes)) {
                    $errors["audio_files.{$index}"] = "Invalid audio file format.";
                }
            }
        }
        
        // Validate image files
        foreach ($this->image_files as $index => $imageFile) {
            if ($imageFile && $imageFile instanceof \Illuminate\Http\UploadedFile) {
                if ($imageFile->getSize() > 2097152) {
                    $errors["image_files.{$index}"] = "Image file must be smaller than 2MB.";
                }
                
                $extension = strtolower($imageFile->getClientOriginalExtension());
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (!in_array($extension, $allowedExtensions)) {
                    $errors["image_files.{$index}"] = "Image file must be jpg, jpeg, png, gif, or webp format.";
                }
            }
        }
        
        return $errors;
    }

    public function create()
    {
        // Create Day with fallback approach
        try {
            $day = \App\Models\Day::firstOrCreate(
                [
                    'number' => $this->number_input,
                    'course_id' => $this->course_id,
                ],
                [
                    'name' => 'Day ' . $this->number_input,
                    'title' => 'Day ' . $this->number_input,
                ]
            );
        } catch (\Exception $e) {
            try {
                $day = \App\Models\Day::firstOrCreate(
                    [
                        'number' => $this->number_input,
                    ],
                    [
                        'title' => 'Day ' . $this->number_input,
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
                            'title' => 'Day ' . $this->number_input,
                        ]
                    );
                    
                    try {
                        $day->day_number = $this->number_input;
                        $day->course_id = $this->course_id;
                        $day->save();
                    } catch (\Exception $e3) {
                        // Some columns don't exist, continue
                    }
                } catch (\Exception $e3) {
                    try {
                        $dayId = \DB::table('days')->insertGetId([
                            'title' => 'Day ' . $this->number_input,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        try {
                            \DB::table('days')->where('id', $dayId)->update([
                                'number' => $this->number_input,
                                'course_id' => $this->course_id,
                            ]);
                        } catch (\Exception $e4) {
                            // Additional fields don't exist, continue
                        }
                        
                        $day = \App\Models\Day::find($dayId);
                    } catch (\Exception $e4) {
                        $day = new \App\Models\Day();
                        $day->title = 'Day ' . $this->number_input;
                        
                        try {
                            $day->day_number = $this->number_input;
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
            return $this->createAudioFillBlank();
        } else if ($isPictureFillBlank) {
            return $this->createPictureFillBlank();
        } else if ($isAudioImageTextSingle) {
            return $this->createAudioImageTextSingle();
        } else if ($isAudioImageTextMultiple) {
            return $this->createAudioImageTextMultiple();
        } else if ($isAudioMcqSingle) {
            return $this->createAudioMcqSingle();
        } else if ($isPictureMcq) {
            return $this->createPictureMcq();
        } else if ($isTrueFalse) {
            return $this->createTrueFalse();
        } else if ($isFormFill) {
            return $this->createFormFill();
        } else if ($isStatementMatch) {
            return $this->createStatementMatch();
        } else if ($isOpinion) {
            return $this->createOpinion();
        } else if ($isReorder) {
            return $this->createReorder();
        } else if ($isTrueFalseMultiple) {
            return $this->createTrueFalseMultiple();
        } else if ($isMcqMultiple) {
            return $this->createMcqMultiple();
        } else if ($isVideoFillBlank) {
            return $this->createVideoFillBlank();
        } else if ($isAudioPictureMatch) {
            return $this->createAudioPictureMatch();
        } else {
            return $this->createDefaultMcq();
        }
    }

    private function createAudioFillBlank()
    {
        // Validate required fields
        if (!$this->audio_file) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please upload an audio file.')
                ->danger()
                ->send();
            return;
        }
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
        // Store audio file
        $audioPath = $this->audio_file->store('audio', 'public');
        // Store explanation file if provided
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        // Get or create question type
        $questionType = QuestionType::firstOrCreate(['name' => 'audio_fill_blank']);
        $questionTypeId = $questionType->id;
        // Create the question
        $question = Question::create([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'topic' => $this->topic,
            'question_type_id' => $questionTypeId,
            'instruction' => $this->instruction,
            'explanation' => $explanationFilePath,
            'points' => $this->points ?: 1,
            'is_active' => $this->is_active,
            'question_data' => json_encode([
                'audio_file' => $audioPath,
                'paragraph' => $paragraph,
                'blank_count' => $blankCount
            ]),
            'answer_data' => json_encode([
                'answer_keys' => array_values($answerKeys),
                'blank_count' => $blankCount
            ]),
            'test_id' => $this->test_id,
        ]);
        Notification::make()
            ->title('Question created successfully!')
            ->body('The audio fill in the blanks question has been created.')
            ->success()
            ->send();
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createAudioImageTextSingle()
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

        if (!$this->audio_image_text_audio_file) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please upload an audio file for the audio image text question.')
                ->danger()
                ->send();
            return;
        }

        $uploadedImages = [];
        $validImageCount = 0;
        
        foreach ($this->audio_image_text_image_uploads as $index => $imageFile) {
            if ($imageFile) {
                $validImageCount++;
                $imagePath = $imageFile->store('question-images', 'public');
                $uploadedImages[$index] = $imagePath;
            }
        }

        if ($validImageCount < 2 || count($rightOptions) < 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least 2 images and 2 text options for audio image text question.')
                ->danger()
                ->send();
            return;
        }

        if (count($pairs) < 1) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select at least 1 correct pair for audio image text question.')
                ->danger()
                ->send();
            return;
        }

        $imageCount = $validImageCount;
        $rightCount = count($rightOptions);
        
        foreach ($pairs as $index => $pair) {
            if ($pair['left'] >= $imageCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Image index {$pair['left']} is out of bounds (max: " . ($imageCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
            if ($pair['right'] >= $rightCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Text option index {$pair['right']} is out of bounds (max: " . ($rightCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
        }

        $audioFilePath = $this->audio_image_text_audio_file->store('question-audio', 'public');

        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        $questionType = QuestionType::firstOrCreate(['name' => 'audio_image_text_single']);
        $questionTypeId = $questionType->id;

        $question = Question::create([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'topic' => $this->topic,
            'question_type_id' => $questionTypeId,
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
            'left_options' => null,
            'right_options' => array_values($rightOptions),
            'correct_pairs' => array_values($pairs),
            'audio_image_text_images' => array_values($uploadedImages),
            'audio_image_text_audio_file' => $audioFilePath,
            'test_id' => $this->test_id,
        ]);

        Notification::make()
            ->title('Question created successfully!')
            ->body('The audio image text question with ' . $validImageCount . ' images and ' . count($rightOptions) . ' text options has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createAudioImageTextMultiple()
    {
        $fileErrors = $this->validateAudioFiles();
        if (!empty($fileErrors)) {
            foreach ($fileErrors as $field => $message) {
                $this->addError($field, $message);
            }
            return;
        }
        
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

        $uploadedPairs = [];
        $validPairCount = 0;
        
        for ($index = 0; $index < max(count($this->audio_files), count($this->image_files)); $index++) {
            $audioFile = $this->audio_files[$index] ?? null;
            $imageFile = $this->image_files[$index] ?? null;
            
            if (!empty($audioFile) && $audioFile instanceof \Illuminate\Http\UploadedFile) {
                $validPairCount++;
                $pairData = [];
                
                try {
                    $audioPath = $audioFile->store('question-audio', 'public');
                    $pairData['audio'] = $audioPath;
                    
                    if (!empty($imageFile) && $imageFile instanceof \Illuminate\Http\UploadedFile) {
                        $imagePath = $imageFile->store('question-images', 'public');
                        $pairData['image'] = $imagePath;
                    } else {
                        $pairData['image'] = null;
                    }
                    
                    $uploadedPairs[$index] = $pairData;
                    
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('File Upload Error')
                        ->body('Failed to upload files for pair ' . ($index + 1) . ': ' . $e->getMessage())
                        ->danger()
                        ->send();
                    return;
                }
            }
        }

        if ($validPairCount < 2 || count($rightOptions) < 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least 2 audio pairs and 2 text options for audio image text multiple question.')
                ->danger()
                ->send();
            return;
        }

        if (count($pairs) < 1) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select at least 1 correct pair for audio image text multiple question.')
                ->danger()
                ->send();
            return;
        }

        $pairCount = $validPairCount;
        $rightCount = count($rightOptions);
        
        foreach ($pairs as $index => $pair) {
            if ($pair['left'] >= $pairCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Audio pair index {$pair['left']} is out of bounds (max: " . ($pairCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
            if ($pair['right'] >= $rightCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Text option index {$pair['right']} is out of bounds (max: " . ($rightCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
        }

        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        $questionType = QuestionType::firstOrCreate(['name' => 'audio_image_text_multiple']);
        $questionTypeId = $questionType->id;

        $question = Question::create([
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
                'main_instruction' => $this->instruction,
                'audio_pairs' => array_values($uploadedPairs),
                'right_options' => array_values($rightOptions)
            ]),
            'answer_data' => json_encode([
                'correct_pairs' => array_values($pairs)
            ]),
            'left_options' => null,
            'right_options' => array_values($rightOptions),
            'correct_pairs' => array_values($pairs),
            'audio_image_text_multiple_pairs' => array_values($uploadedPairs),
            'test_id' => $this->test_id,
        ]);

        Notification::make()
            ->title('Question created successfully!')
            ->body('The audio image text multiple question with ' . $validPairCount . ' audio pairs and ' . count($rightOptions) . ' text options has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createAudioMcqSingle()
    {
        $validatedAudioSubQuestions = [];
        
        if (!$this->audio_mcq_file) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please upload an audio file for the audio MCQ question.')
                ->danger()
                ->send();
            return;
        }
        
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
        
        $audioFilePath = $this->audio_mcq_file->store('question-audio', 'public');
        
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'audio_mcq_single']);
        $questionTypeId = $questionType->id;
        
        $question = Question::create([
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
                'main_instruction' => $this->instruction,
                'audio_file' => $audioFilePath,
                'sub_questions' => $validatedAudioSubQuestions
            ]),
            'answer_data' => json_encode([
                'sub_questions_answers' => array_map(function($subQ) {
                    return $subQ['correct_indices'];
                }, $validatedAudioSubQuestions)
            ]),
            'left_options' => null,
            'right_options' => null,
            'correct_pairs' => null,
            'test_id' => $this->test_id,
        ]);
        
        Notification::make()
            ->title('Question created successfully!')
            ->body('The Audio MCQ question with ' . count($validatedAudioSubQuestions) . ' sub-questions has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createPictureMcq()
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

        $uploadedImages = [];
        $validImageCount = 0;
        
        foreach ($this->picture_mcq_image_uploads as $index => $imageFile) {
            if ($imageFile) {
                $validImageCount++;
                $imagePath = $imageFile->store('question-images', 'public');
                $uploadedImages[$index] = $imagePath;
            }
        }

        if ($validImageCount < 2 || count($rightOptions) < 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least 2 images and 2 text options for picture MCQ.')
                ->danger()
                ->send();
            return;
        }

        if (count($pairs) < 1) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select at least 1 correct pair for picture MCQ.')
                ->danger()
                ->send();
            return;
        }

        $imageCount = $validImageCount;
        $rightCount = count($rightOptions);
        
        foreach ($pairs as $index => $pair) {
            if ($pair['left'] >= $imageCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Image index {$pair['left']} is out of bounds (max: " . ($imageCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
            if ($pair['right'] >= $rightCount) {
                Notification::make()
                    ->title('Validation Error')
                    ->body("Pair " . ($index + 1) . ": Text option index {$pair['right']} is out of bounds (max: " . ($rightCount - 1) . ")")
                    ->danger()
                    ->send();
                return;
            }
        }

        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        $questionType = QuestionType::firstOrCreate(['name' => 'picture_mcq']);
        $questionTypeId = $questionType->id;

        $question = Question::create([
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
                'main_instruction' => $this->instruction,
                'images' => array_values($uploadedImages),
                'right_options' => array_values($rightOptions)
            ]),
            'answer_data' => json_encode([
                'correct_pairs' => array_values($pairs)
            ]),
            'left_options' => null,
            'right_options' => array_values($rightOptions),
            'correct_pairs' => array_values($pairs),
            'picture_mcq_images' => array_values($uploadedImages),
            'test_id' => $this->test_id,
        ]);

        Notification::make()
            ->title('Question created successfully!')
            ->body('The picture MCQ question with ' . $validImageCount . ' images and ' . count($rightOptions) . ' text options has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createTrueFalse()
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
        
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'true_false']);
        $questionTypeId = $questionType->id;
        
        $question = Question::create([
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
            'test_id' => $this->test_id,
        ]);
        
        Notification::make()
            ->title('Question created successfully!')
            ->body('The True/False question has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createFormFill()
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
        
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'form_fill']);
        $questionTypeId = $questionType->id;
        
        $question = Question::create([
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
            'test_id' => $this->test_id,
        ]);
        
        Notification::make()
            ->title('Question created successfully!')
            ->body('The form fill question with ' . $blankCount . ' blank(s) and ' . count($options) . ' options has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createStatementMatch()
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

        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        $questionType = QuestionType::firstOrCreate(['name' => 'statement_match']);
        $questionTypeId = $questionType->id;

        $question = Question::create([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => $questionTypeId,
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
            'test_id' => $this->test_id,
        ]);

        Notification::make()
            ->title('Question created successfully!')
            ->body('The statement match question has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createOpinion()
    {
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'opinion']);
        $questionTypeId = $questionType->id;
        
        $question = Question::create([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'topic' => $this->topic,
            'question_type_id' => $questionTypeId,
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
            'test_id' => $this->test_id,
        ]);
        
        Notification::make()
            ->title('Question created successfully!')
            ->body('The opinion question has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createReorder()
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
        
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'reorder']);
        $questionTypeId = $questionType->id;
        
        $question = Question::create([
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
            'test_id' => $this->test_id,
        ]);
        
        Notification::make()
            ->title('Question created successfully!')
            ->body('The sentence reordering question with ' . count($fragments) . ' fragments has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createTrueFalseMultiple()
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
        
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'true_false_multiple']);
        $questionTypeId = $questionType->id;
        
        $question = Question::create([
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
            'test_id' => $this->test_id,
        ]);
        
        Notification::make()
            ->title('Question created successfully!')
            ->body('The True/False Multiple question with ' . count($validatedTrueFalseQuestions) . ' statements has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createMcqMultiple()
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
        
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        
        $questionType = QuestionType::firstOrCreate(['name' => 'mcq_multiple']);
        $questionTypeId = $questionType->id;
        
        $question = Question::create([
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
            'test_id' => $this->test_id,
        ]);
        
        Notification::make()
            ->title('Question created successfully!')
            ->body('The MCQ Multiple question with ' . count($validatedSubQuestions) . ' sub-questions has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createDefaultMcq()
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

        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }

        $question = Question::create([
            'day_id' => $this->day_id,
            'course_id' => $this->course_id,
            'subject_id' => $this->subject_id,
            'topic' => $this->topic,
            'question_type_id' => is_numeric($this->question_type_id) ? $this->question_type_id : \App\Models\QuestionType::where('name', $this->question_type_id)->first()?->id ?? 1,
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
            'test_id' => $this->test_id,
        ]);

        Notification::make()
            ->title('Question created successfully!')
            ->body('The question has been created.')
            ->success()
            ->send();
        
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createPictureFillBlank()
    {
        // Validate required fields
        if (!$this->picture_fill_image) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please upload an image file.')
                ->danger()
                ->send();
            return;
        }
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
        // Store image file
        $imagePath = $this->picture_fill_image->store('images', 'public');
        // Store explanation file if provided
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        // Get or create question type
        $questionType = QuestionType::firstOrCreate(['name' => 'picture_fill_blank']);
        $questionTypeId = $questionType->id;
        // Create the question
        $question = Question::create([
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
                'image_file' => $imagePath,
                'paragraph' => $paragraph,
                'blank_count' => $blankCount
            ]),
            'answer_data' => json_encode([
                'answer_keys' => array_values($answerKeys),
                'blank_count' => $blankCount
            ]),
            'test_id' => $this->test_id,
        ]);
        Notification::make()
            ->title('Question created successfully!')
            ->body('The picture fill in the blanks question has been created.')
            ->success()
            ->send();
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    private function createVideoFillBlank()
    {
        // Validate required fields
        if (!$this->video_fill_video) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please upload a video file.')
                ->danger()
                ->send();
            return;
        }
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
        // Store video file
        $videoPath = $this->video_fill_video->store('videos', 'public');
        // Store explanation file if provided
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        // Get or create question type
        $questionType = QuestionType::firstOrCreate(['name' => 'video_fill_blank']);
        $questionTypeId = $questionType->id;
        // Create the question
        $question = Question::create([
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
                'video_file' => $videoPath,
                'paragraph' => $paragraph,
                'blank_count' => $blankCount
            ]),
            'answer_data' => json_encode([
                'answer_keys' => array_values($answerKeys),
                'blank_count' => $blankCount
            ]),
            'test_id' => $this->test_id,
        ]);
        Notification::make()
            ->title('Question created successfully!')
            ->body('The video fill in the blanks question has been created.')
            ->success()
            ->send();
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    // Properties for Audio + Picture Matching
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
        $this->audio_picture_pairs = [ ['left' => '', 'right' => ''] ];
    }

    private function createAudioPictureMatch()
    {
        // Validate audios and images
        $audioFiles = array_filter($this->audio_picture_audios, fn($a) => $a instanceof \Illuminate\Http\UploadedFile);
        $imageFiles = array_filter($this->audio_picture_images, fn($i) => $i instanceof \Illuminate\Http\UploadedFile);
        $pairs = array_filter($this->audio_picture_pairs, function($pair) {
            return isset($pair['left'], $pair['right']) && $pair['left'] !== '' && $pair['right'] !== '' && $pair['left'] !== null && $pair['right'] !== null;
        });
        if (count($audioFiles) < 2 || count($imageFiles) < 2) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please add at least 2 audio files and 2 image files for audio-picture matching.')
                ->danger()
                ->send();
            return;
        }
        if (count($pairs) < 1) {
            Notification::make()
                ->title('Validation Error')
                ->body('Please select at least 1 correct pair for audio-picture matching.')
                ->danger()
                ->send();
            return;
        }
        // Store audio files
        $audioPaths = [];
        foreach ($this->audio_picture_audios as $idx => $audioFile) {
            if ($audioFile instanceof \Illuminate\Http\UploadedFile) {
                $audioPaths[$idx] = $audioFile->store('audio', 'public');
            } else {
                $audioPaths[$idx] = $audioFile;
            }
        }
        // Store image files
        $imagePaths = [];
        foreach ($this->audio_picture_images as $idx => $imageFile) {
            if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
                $imagePaths[$idx] = $imageFile->store('images', 'public');
            } else {
                $imagePaths[$idx] = $imageFile;
            }
        }
        // Store explanation file if provided
        $explanationFilePath = null;
        if ($this->explanation_file) {
            $explanationFilePath = $this->explanation_file->store('explanations', 'public');
        }
        // Get or create question type
        $questionType = QuestionType::firstOrCreate(['name' => 'audio_picture_match']);
        $questionTypeId = $questionType->id;
        // Save the question
        $question = Question::create([
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
                'audios' => array_values($audioPaths),
                'images' => array_values($imagePaths),
            ]),
            'answer_data' => json_encode([
                'correct_pairs' => array_values($pairs)
            ]),
            'test_id' => $this->test_id,
        ]);
        Notification::make()
            ->title('Question created successfully!')
            ->body('The audio + picture matching question has been created.')
            ->success()
            ->send();
        return redirect()->to(QuestionResource::getUrl('index'));
    }

    protected function getViewData(): array
    {
        return [
            'days' => Day::all(),
            'courses' => \App\Models\Course::all(),
            'subjects' => Subject::all(),
            'questionTypes' => QuestionType::all(),
            'tests' => \App\Models\Test::all(),
        ];
    }

    public function getTitle(): string
    {
        return 'New Question';
    }

    public function getHeading(): string
    {
        return 'Question Builder';
    }

    public function getSubheading(): ?string
    {
        return 'Create and configure new questions for your assessment bank.';
    }

    protected function hasLogo(): bool
    {
        return false;
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin/questions') => 'Questions',
            '' => 'New Question',
        ];
    }

    // Update methods for validation
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

    public function updatedAudioFiles($value, $key)
    {
        \Log::info('Audio file upload:', [
            'key' => $key,
            'has_value' => !empty($value),
            'type' => gettype($value)
        ]);
    }

    public function updatedImageFiles($value, $key)
    {
        \Log::info('Image file upload:', [
            'key' => $key,
            'has_value' => !empty($value),
            'type' => gettype($value)
        ]);
    }

    public function updatedAudioFillParagraph()
    {
        $this->adjustAudioFillAnswerKeysToBlankCount();
    }

    // Rename this method to match the call above
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

    public function updatedPictureFillParagraph()
    {
        $this->adjustPictureFillAnswerKeysToBlankCount();
    }

    public function addPictureFillAnswerKey()
    {
        $this->picture_fill_answer_key[] = '';
    }

    public function removePictureFillAnswerKey($index)
    {
        if (count($this->picture_fill_answer_key) > 1) {
            array_splice($this->picture_fill_answer_key, $index, 1);
            // Reindex the array to ensure sequential indices
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

    public function updatedVideoFillParagraph()
    {
        $this->adjustVideoFillAnswerKeysToBlankCount();
    }

    public function addVideoFillAnswerKey()
    {
        $this->video_fill_answer_key[] = '';
    }

    public function removeVideoFillAnswerKey($index)
    {
        if (count($this->video_fill_answer_key) > 1) {
            array_splice($this->video_fill_answer_key, $index, 1);
            // Reindex the array to ensure sequential indices
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
}