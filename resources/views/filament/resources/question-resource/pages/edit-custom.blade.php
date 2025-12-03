<x-filament-panels::page>
    @php
        $courses = $courses ?? \App\Models\Course::all();
        $subjects = $subjects ?? \App\Models\Subject::all();
        $questionTypes = $questionTypes ?? \App\Models\QuestionType::all();
        $tests = $tests ?? \App\Models\Test::all();
    @endphp
    <div class="modern-question-form">
        <form wire:submit="update">
            @csrf
            
            <!-- Single Full-Width Card -->
            <div class="modern-card">
                <div class="card-content">
                    <!-- Question Details Section -->
                    <div class="section-block">
                        <h3 class="section-title">Question Details</h3>
                        
                        <!-- File Preservation Notice -->
                        <div class="file-preservation-notice mb-6">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <strong>File Management:</strong> Existing images, audio files, and text options will be preserved unless you upload new files to replace them. Only upload new files if you want to change the current content.
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="modern-label">Day Number *</label>
                                <input type="number" wire:model="day_number_input" min="1" placeholder="1" class="modern-input">
                                @error('day_number_input') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="modern-label">Course *</label>
                                <select wire:model="course_id" class="modern-select" required>
                                    <option value="" disabled>Select course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                                @error('course_id') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="modern-label">Subject *</label>
                                <select wire:model="subject_id" class="modern-select">
                                    <option value="">Select subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                @error('subject_id') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="modern-label">Topic (Optional)</label>
                                <input type="text" wire:model="topic" placeholder="Enter topic (e.g., Grammar, Vocabulary, etc.)" class="modern-input">
                                @error('topic') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="modern-label">Question Type *</label>
                                <select wire:model="question_type_id" class="modern-select" id="question_type_id">
                                    <option value="">Select type</option>
                                    @foreach($questionTypes as $type)
                                        <option value="{{ $type->id }}" {{ $question_type_id == $type->id ? 'selected' : '' }}>{{ $type->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('question_type_id') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="modern-label">Marks</label>
                                <input type="number" wire:model="points" min="1" placeholder="1" class="modern-input">
                                @error('points') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="modern-label">Test (Optional)</label>
                                <select wire:model="test_id" class="modern-select">
                                    <option value="">Select test (optional)</option>
                                    @foreach($tests as $test)
                                        <option value="{{ $test->id }}">{{ $test->name }}</option>
                                    @endforeach
                                </select>
                                @error('test_id') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex items-center pt-6">
                                <label class="modern-checkbox-label">
                                    <input type="checkbox" wire:model="is_active" class="modern-checkbox">
                                    <span class="ml-2">Active Question</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Question Content Section -->
                    <div class="section-block">
                        <h3 class="section-title">Question Content</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="modern-label">Question Instruction *</label>
                                <textarea wire:model="instruction" rows="4" placeholder="Enter the question instruction..."
                                          class="modern-textarea"></textarea>
                                @error('instruction') <p class="error-text">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="modern-label">Explanation File (Optional)</label>
                                @if($explanation)
                                    <div class="mb-2 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                        <span class="text-sm text-blue-800">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Current: {{ basename($explanation) }}
                                        </span>
                                        <button type="button" wire:click="removeExplanationFile" class="text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                                <input type="file" wire:model="explanation_file" class="modern-input" accept="*/*">
                                <small class="text-gray-500">{{ $explanation ? 'Upload new file to replace current one' : 'Upload an explanation file' }}</small>
                                @error('explanation_file') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div x-data="{ type: $wire.entangle('question_type_id') }">
                       <!-- Audio Image Text Single Section -->
<div class="section-block" id="audio-image-text-single-section" x-show="type === 'audio_image_text_single'">
    <h3 class="section-title">Audio Image Text - Single Audio with Image to Text Matching</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Upload one audio file as context/hint, add images on the left, text options on the right, then match images to correct text options.
    </div>

    <!-- Audio Upload Section -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">🎵 Context Audio File</h4>
        <div class="audio-upload-section">
            <label class="modern-label">Upload Audio File (Context/Hint)</label>
            @php
                $currentAudioFile = $record->audio_image_text_audio_file ?? null;
                if (!$currentAudioFile && $record->question_data) {
                    $questionData = json_decode($record->question_data, true);
                    $currentAudioFile = $questionData['audio_file'] ?? null;
                }
            @endphp
            
            @if($currentAudioFile)
                <div class="mb-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-green-800">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                            Current: {{ basename($currentAudioFile) }}
                        </span>
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($currentAudioFile) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="mt-3 modern-audio-player">
                        <audio controls style="width: 100%;" class="rounded-lg">
                            <source src="{{ \Illuminate\Support\Facades\Storage::url($currentAudioFile) }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                </div>
            @endif
            
            <div wire:loading wire:target="audio_image_text_audio_file" class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="loading-spinner">
                        <svg class="w-6 h-6 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-yellow-800">Uploading audio file...</p>
                        <p class="text-sm text-yellow-700">Please wait while your audio file is being processed</p>
                    </div>
                </div>
            </div>

            <input type="file" wire:model="audio_image_text_audio_file" class="modern-input" accept="audio/*">
            @error('audio_image_text_audio_file') <p class="error-text">{{ $message }}</p> @enderror
            
            @if($audio_image_text_audio_file ?? null)
                <div class="mt-3 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-green-800">{{ $audio_image_text_audio_file->getClientOriginalName() }}</p>
                            <p class="text-sm text-green-600">New audio file uploaded - will replace current audio</p>
                            <div class="mt-3 modern-audio-player" x-data="{ audioReady: false }" x-init="setTimeout(() => audioReady = true, 1500)">
                                <div x-show="!audioReady" class="text-center p-4 bg-yellow-50 border border-yellow-200 rounded">
                                    <p class="text-sm text-yellow-700">⏳ Preparing audio controls...</p>
                                </div>
                                <audio x-show="audioReady" controls style="width: 100%;" class="rounded-lg">
                                    <source src="{{ $audio_image_text_audio_file->temporaryUrl() }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mt-2 text-sm text-gray-500">
                <strong>Supported formats:</strong> MP3, WAV, OGG, M4A (Max size: 10MB)<br>
                <strong>Purpose:</strong> This audio provides context or hints to help students match images to text options.<br>
                <strong>Note:</strong> <span class="text-blue-600">If no new audio is uploaded, the current audio file will be preserved.</span>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-2 gap-6">
        <!-- Left Side - Images -->
        <div>
            <h5 class="font-semibold mb-4 text-lg">📷 Images to Match</h5>
            <div id="audio-image-text-images-container">
                @foreach($audio_image_text_image_uploads as $idx => $imageUpload)
                    <div class="picture-mcq-image-item flex flex-col mb-4 p-4 border-2 border-dashed border-purple-300 rounded-lg" wire:key="audio_image_text_image_{{ $idx }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-700">Image {{ $idx + 1 }}</span>
                            @if($idx > 0)
                                <button type="button" wire:click="removeAudioImageTextImage({{ $idx }})" class="remove-btn-small">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Current Image Display -->
                        @if(isset($audio_image_text_images[$idx]))
                            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-blue-800 font-medium">Current Image:</span>
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($audio_image_text_images[$idx]) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                                <div class="image-preview-container">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($audio_image_text_images[$idx]) }}" 
                                         alt="Current Image {{ $idx + 1 }}" 
                                         class="image-preview-thumb">
                                </div>
                            </div>
                        @endif
                        
                        <!-- Loading indicator for image upload -->
                        <div wire:loading wire:target="audio_image_text_image_uploads.{{ $idx }}" class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="loading-spinner">
                                    <svg class="w-5 h-5 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-yellow-800">Uploading image...</span>
                            </div>
                        </div>

                        <input type="file" wire:model="audio_image_text_image_uploads.{{ $idx }}" class="modern-input" accept="image/*" placeholder="Upload image">
                        @error("audio_image_text_image_uploads.{$idx}") <p class="error-text">{{ $message }}</p> @enderror
                        
                        @if(isset($audio_image_text_image_uploads[$idx]) && $audio_image_text_image_uploads[$idx])
                            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800 font-medium mb-2">New Image Preview - will replace current image:</p>
                                <div class="image-preview-container">
                                    <img src="{{ $audio_image_text_image_uploads[$idx]->temporaryUrl() }}" 
                                         alt="New Preview {{ $idx + 1 }}" 
                                         class="image-preview-thumb">
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="button" wire:click="addAudioImageTextImage" class="add-btn mt-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Image
            </button>
        </div>
        
        <!-- Right Side - Text Options -->
        <div>
            <h5 class="font-semibold mb-4 text-lg">📝 Text Options</h5>
            <div id="audio-image-text-right-options-container">
                @foreach($audio_image_text_right_options as $idx => $option)
                    <div class="option-item flex items-center mb-2" wire:key="audio_image_text_right_option_{{ $idx }}">
                        <input type="text" wire:model.live="audio_image_text_right_options.{{ $idx }}" class="option-input flex-1 mr-2" placeholder="Enter text option {{ $idx + 1 }}">
                        @if($idx === 0)
                            <button type="button" wire:click="addAudioImageTextRightOption" class="add-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        @else
                            <button type="button" wire:click="removeAudioImageTextRightOption({{ $idx }})" class="remove-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                    @error("audio_image_text_right_options.{$idx}") <p class="error-text">{{ $message }}</p> @enderror
                @endforeach
            </div>
        </div>
    </div>

    <!-- Correct Answer Pairs for Audio Image Text -->
    <div class="mt-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="sub-question-title">Correct Answer Pairs</h4>
            <div class="flex space-x-2">
                <button type="button" wire:click="addAudioImageTextPair" class="add-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Pair
                </button>
                <button type="button" onclick="@this.set('audio_image_text_correct_pairs', Array(2).fill({left: '', right: ''}))" class="clear-all-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear All Pairs
                </button>
            </div>
        </div>
        <div class="info-banner-small">
            <span class="text-sm">Select pairs to match images with text options. Image indices: 0 = first image, 1 = second image, etc. Text indices: 0 = first text option, 1 = second text option, etc.</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4" wire:key="audio-image-text-correct-pairs-section">
            @foreach($audio_image_text_correct_pairs as $pairIdx => $pair)
                <div class="option-item" wire:key="audio-image-text-pair-{{ $pairIdx }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-semibold" style="color: #000 !important;">Correct Pair {{ $pairIdx+1 }}</div>
                        <button type="button" wire:click="removeAudioImageTextPair({{ $pairIdx }})" class="clear-pair-btn" title="Remove this pair">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="modern-label">Image</label>
                            <select class="option-input" wire:model.live="audio_image_text_correct_pairs.{{ $pairIdx }}.left" wire:key="audio-image-text-left-select-{{ $pairIdx }}-{{ count($audio_image_text_image_uploads) }}-{{ json_encode($audio_image_text_correct_pairs) }}">
                                <option value="">Select Image</option>
                                @php
                                    $totalImages = max(count($audio_image_text_images), count($audio_image_text_image_uploads));
                                @endphp
                                @for($idx = 0; $idx < $totalImages; $idx++)
                                    @php
                                        $hasExisting = isset($audio_image_text_images[$idx]);
                                        $hasUpload = isset($audio_image_text_image_uploads[$idx]) && $audio_image_text_image_uploads[$idx];
                                        $alreadySelected = false;
                                        foreach ($audio_image_text_correct_pairs as $otherIdx => $otherPair) {
                                            if ($otherIdx !== $pairIdx && isset($otherPair['left']) && $otherPair['left'] !== '' && $otherPair['left'] !== null && $otherPair['left'] == $idx) {
                                                $alreadySelected = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if(($hasExisting || $hasUpload) && !$alreadySelected)
                                        <option value="{{ $idx }}">{{ $idx }}. Image {{ $idx + 1 }}</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="modern-label">Text Option</label>
                            <select class="option-input" wire:model.live="audio_image_text_correct_pairs.{{ $pairIdx }}.right" wire:key="audio-image-text-right-select-{{ $pairIdx }}-{{ count($audio_image_text_right_options) }}-{{ json_encode($audio_image_text_correct_pairs) }}-{{ json_encode($audio_image_text_right_options) }}">
                                <option value="">Select Text Option</option>
                                @foreach($audio_image_text_right_options as $idx => $option)
                                    @php
                                        $alreadySelected = false;
                                        foreach ($audio_image_text_correct_pairs as $otherIdx => $otherPair) {
                                            if ($otherIdx !== $pairIdx && isset($otherPair['right']) && $otherPair['right'] !== '' && $otherPair['right'] !== null && $otherPair['right'] == $idx) {
                                                $alreadySelected = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if(!$alreadySelected && trim($option ?? '') !== '')
                                        <option value="{{ $idx }}">{{ $idx }}. {{ $option }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

                    <!-- Audio Image Text Multiple Section -->
<div class="section-block" id="audio-image-text-multiple-section" x-show="type === 'audio_image_text_multiple'">
    <h3 class="section-title">Multiple Audio, Multiple Images & Texts</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Create multiple audio files with optional images on the left side and text options on the right. Students will listen to audio files (and optionally see images) and match them to the correct text descriptions. <strong>Audio is required, images are optional.</strong>
    </div>
    
    <div class="grid grid-cols-2 gap-6">
        <!-- Left Side - Image + Audio Pairs -->
        <div>
            <h5 class="font-semibold mb-4 text-lg">🎭 Audio + Image Pairs (Audio Required, Image Optional)</h5>
            <div id="audio-image-text-multiple-pairs-container">
                @foreach($audio_image_text_multiple_pairs as $idx => $pair)
                    <div class="audio-image-pair-item flex flex-col mb-6 p-4 border-2 border-dashed border-indigo-300 rounded-lg bg-gradient-to-br from-indigo-50 to-purple-50" wire:key="audio_image_text_multiple_pair_{{ $idx }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-indigo-700">🎵 Pair {{ $idx + 1 }}</span>
                            @if($idx > 0)
                                <button type="button" wire:click="removeAudioImageTextMultiplePair({{ $idx }})" class="remove-btn-small">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Current Audio Display (Required) -->
                        @if(isset($audio_image_text_multiple_existing_pairs[$idx]['audio']))
                            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-green-800 font-medium">Current Audio: <span class="text-red-600">*</span></span>
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($audio_image_text_multiple_existing_pairs[$idx]['audio']) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                                <span class="text-sm font-medium text-green-700">{{ basename($audio_image_text_multiple_existing_pairs[$idx]['audio']) }}</span>
                                <div class="mt-2 modern-audio-player">
                                    <audio controls style="width: 100%;" class="rounded-lg">
                                        <source src="{{ \Illuminate\Support\Facades\Storage::url($audio_image_text_multiple_existing_pairs[$idx]['audio']) }}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Audio Upload (Required) -->
                        <div class="mb-3">
                            <label class="modern-label text-sm">🎵 Audio File <span class="text-red-600">*</span></label>
                            
                            <!-- Loading indicator for audio upload -->
                            <div wire:loading wire:target="audio_image_text_multiple_pairs.{{ $idx }}.audio" class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="loading-spinner">
                                        <svg class="w-5 h-5 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-yellow-800">Uploading audio...</span>
                                </div>
                            </div>

                            <input type="file" wire:model="audio_image_text_multiple_pairs.{{ $idx }}.audio" class="modern-input" accept="audio/*" placeholder="Upload audio (required)">
                            @error("audio_image_text_multiple_pairs.{$idx}.audio") <p class="error-text">{{ $message }}</p> @enderror
                            
                            @if(isset($audio_image_text_multiple_pairs[$idx]['audio']) && $audio_image_text_multiple_pairs[$idx]['audio'])
                                <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-green-700">{{ $audio_image_text_multiple_pairs[$idx]['audio']->getClientOriginalName() }}</span>
                                    </div>
                                    <p class="text-xs text-green-600 mb-2">New audio file - will replace current audio</p>
                                    <div class="mt-2 modern-audio-player" x-data="{ audioReady: false }" x-init="setTimeout(() => audioReady = true, 1500)">
                                        <div x-show="!audioReady" class="text-center p-4 bg-yellow-50 border border-yellow-200 rounded">
                                            <p class="text-sm text-yellow-700">⏳ Preparing audio controls...</p>
                                        </div>
                                        <audio x-show="audioReady" controls style="width: 100%;" class="rounded-lg">
                                            <source src="{{ $audio_image_text_multiple_pairs[$idx]['audio']->temporaryUrl() }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Current Image Display (Optional) -->
                        @if(isset($audio_image_text_multiple_existing_pairs[$idx]['image']))
                            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-blue-800 font-medium">Current Image (Optional):</span>
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($audio_image_text_multiple_existing_pairs[$idx]['image']) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                                <div class="image-preview-container">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($audio_image_text_multiple_existing_pairs[$idx]['image']) }}" 
                                         alt="Current Image {{ $idx + 1 }}" 
                                         class="image-preview-thumb">
                                </div>
                            </div>
                        @endif
                        
                        <!-- Image Upload (Optional) -->
                        <div class="mb-2">
                            <label class="modern-label text-sm">🖼️ Image File (Optional)</label>
                            
                            <!-- Loading indicator for image upload -->
                            <div wire:loading wire:target="audio_image_text_multiple_pairs.{{ $idx }}.image" class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="loading-spinner">
                                        <svg class="w-5 h-5 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-yellow-800">Uploading image...</span>
                                </div>
                            </div>

                            <input type="file" wire:model="audio_image_text_multiple_pairs.{{ $idx }}.image" class="modern-input" accept="image/*" placeholder="Upload image (optional)">
                            @error("audio_image_text_multiple_pairs.{$idx}.image") <p class="error-text">{{ $message }}</p> @enderror
                            
                            @if(isset($audio_image_text_multiple_pairs[$idx]['image']) && $audio_image_text_multiple_pairs[$idx]['image'])
                                <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-sm text-green-800 font-medium mb-2">New Image Preview - will replace current image:</p>
                                    <div class="image-preview-container">
                                        <img src="{{ $audio_image_text_multiple_pairs[$idx]['image']->temporaryUrl() }}" 
                                             alt="New Preview {{ $idx + 1 }}" 
                                             class="image-preview-thumb">
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="text-xs text-gray-500 mt-1">
                            <strong>Note:</strong> <span class="text-blue-600">Audio is required for each pair. Images are optional but enhance the learning experience. Upload new files to replace existing ones.</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" wire:click="addAudioImageTextMultiplePair" class="add-btn mt-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Audio Pair
            </button>
        </div>
        
        <!-- Right Side - Text Options -->
        <div>
            <h5 class="font-semibold mb-4 text-lg">📝 Text Options</h5>
            <div id="audio-image-text-multiple-right-options-container">
                @foreach($audio_image_text_multiple_right_options as $idx => $option)
                    <div class="option-item flex items-center mb-2" wire:key="audio_image_text_multiple_right_option_{{ $idx }}">
                        <input type="text" wire:model.live="audio_image_text_multiple_right_options.{{ $idx }}" class="option-input flex-1 mr-2" placeholder="Enter text option {{ $idx + 1 }}">
                        @if($idx === 0)
                            <button type="button" wire:click="addAudioImageTextMultipleRightOption" class="add-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        @else
                            <button type="button" wire:click="removeAudioImageTextMultipleRightOption({{ $idx }})" class="remove-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                    @error("audio_image_text_multiple_right_options.{$idx}") <p class="error-text">{{ $message }}</p> @enderror
                @endforeach
            </div>
        </div>
    </div>

    <!-- Correct Answer Pairs for Audio Image Text Multiple -->
    <div class="mt-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="sub-question-title">Correct Answer Pairs</h4>
            <div class="flex space-x-2">
                <button type="button" wire:click="addAudioImageTextMultiplePair_Answer" class="add-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Pair
                </button>
                <button type="button" onclick="@this.set('audio_image_text_multiple_correct_pairs', Array({{ max(2, count($audio_image_text_multiple_correct_pairs ?? []) ) }}).fill({left: '', right: ''}))" class="clear-all-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear All Pairs
                </button>
            </div>
        </div>
        <div class="info-banner-small">
            <span class="text-sm">Select at least 2 pairs. Pair indices: 0 = first audio pair, 1 = second pair, etc. Text indices: 0 = first text option, 1 = second text option, etc. Only audio is required for each pair.</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4" wire:key="audio-image-text-multiple-correct-pairs-section">
            @foreach($audio_image_text_multiple_correct_pairs as $pairIdx => $pair)
                <div class="option-item" wire:key="audio-image-text-multiple-pair-{{ $pairIdx }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-semibold" style="color: #000 !important;">Correct Pair {{ $pairIdx+1 }}</div>
                        <button type="button" wire:click="removeAudioImageTextMultiplePair_Answer({{ $pairIdx }})" class="clear-pair-btn" title="Remove this pair">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="modern-label">Audio Pair</label>
                            <select class="option-input" wire:model.live="audio_image_text_multiple_correct_pairs.{{ $pairIdx }}.left" wire:key="audio-image-text-multiple-left-select-{{ $pairIdx }}-{{ count($audio_image_text_multiple_pairs) }}-{{ json_encode($audio_image_text_multiple_correct_pairs) }}">
                                <option value="">Select Audio Pair</option>
                                @foreach($audio_image_text_multiple_existing_pairs as $idx => $existingPair)
                                    @php
                                        // Only check if audio exists (image is optional)
                                        $hasAudio = isset($existingPair['audio']) && !empty($existingPair['audio']);
                                        
                                        // Check if this pair index is already selected
                                        $alreadySelected = false;
                                        foreach ($audio_image_text_multiple_correct_pairs as $otherIdx => $correctPair) {
                                            if ($otherIdx !== $pairIdx && isset($correctPair['left']) && $correctPair['left'] !== '' && $correctPair['left'] !== null && $correctPair['left'] == $idx) {
                                                $alreadySelected = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if($hasAudio && !$alreadySelected)
                                        <option value="{{ $idx }}">{{ $idx }}. Audio Pair {{ $idx + 1 }}</option>
                                    @endif
                                @endforeach

                                @for($idx = count($audio_image_text_multiple_existing_pairs); $idx < count($audio_image_text_multiple_pairs); $idx++)
                                    @php
                                        // Only check if new audio exists (image is optional)
                                        $hasNewAudio = isset($audio_image_text_multiple_pairs[$idx]['audio']) && $audio_image_text_multiple_pairs[$idx]['audio'];
                                        
                                        // Check if already selected
                                        $alreadySelected = false;
                                        foreach ($audio_image_text_multiple_correct_pairs as $otherIdx => $correctPair) {
                                            if ($otherIdx !== $pairIdx && isset($correctPair['left']) && $correctPair['left'] !== '' && $correctPair['left'] !== null && $correctPair['left'] == $idx) {
                                                $alreadySelected = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if($hasNewAudio && !$alreadySelected)
                                        <option value="{{ $idx }}">{{ $idx }}. Audio Pair {{ $idx + 1 }}</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="modern-label">Text Option</label>
                            <select class="option-input" wire:model.live="audio_image_text_multiple_correct_pairs.{{ $pairIdx }}.right" wire:key="audio-image-text-multiple-right-select-{{ $pairIdx }}-{{ count($audio_image_text_multiple_right_options) }}-{{ json_encode($audio_image_text_multiple_correct_pairs) }}-{{ json_encode($audio_image_text_multiple_right_options) }}">
                                <option value="">Select Text Option</option>
                                @foreach($audio_image_text_multiple_right_options as $idx => $option)
                                    @php
                                        $alreadySelected = false;
                                        foreach ($audio_image_text_multiple_correct_pairs as $otherIdx => $correctPair) {
                                            if ($otherIdx !== $pairIdx && isset($correctPair['right']) && $correctPair['right'] !== '' && $correctPair['right'] !== null && $correctPair['right'] == $idx) {
                                                $alreadySelected = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if(!$alreadySelected && trim($option ?? '') !== '')
                                        <option value="{{ $idx }}">{{ $idx }}. {{ $option }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

                        <!-- Audio MCQ Single Section -->
                        <div class="section-block" id="audio-mcq-single-section" x-show="type === 'audio_mcq_single'">
                            <h3 class="section-title">Audio MCQ - Single Audio, Multiple Questions</h3>
                            <div class="info-banner">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Upload one audio file and create multiple sub-questions (a, b, c, etc.) based on that audio. Each sub-question can have multiple options and correct answers.
                            </div>

                            <!-- Audio Upload Section -->
                            <div class="mb-6">
                                <h4 class="sub-question-title mb-4">🎵 Audio File</h4>
                                <div class="audio-upload-section">
                                    <label class="modern-label">Upload Audio File</label>
                                    @php
                                        $existingAudioFile = null;
                                        if ($record->question_data) {
                                            $questionData = json_decode($record->question_data, true);
                                            $existingAudioFile = $questionData['audio_file'] ?? null;
                                        }
                                    @endphp
                                    
                                    @if($existingAudioFile)
                                        <div class="mb-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-blue-800">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                    </svg>
                                                    Current: {{ basename($existingAudioFile) }}
                                                </span>
                                                <a href="{{ \Illuminate\Support\Facades\Storage::url($existingAudioFile) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                            <div class="mt-3 modern-audio-player">
                                                <audio controls style="width: 100%;" class="rounded-lg">
                                                    <source src="{{ \Illuminate\Support\Facades\Storage::url($existingAudioFile) }}" type="audio/mpeg">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Loading indicator for audio upload -->
                                    <div wire:loading wire:target="audio_image_text_multiple_pairs.{{ $idx }}.audio" class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="loading-spinner">
                                                <svg class="w-5 h-5 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium" style="color: #92400e !important;">Uploading audio...</span>
                                        </div>
                                    </div>

                                    <input type="file" wire:model="audio_mcq_file" class="modern-input" accept="audio/*">
                                    @error('audio_mcq_file') <p class="error-text">{{ $message }}</p> @enderror
                                    
                                    @if($audio_mcq_file ?? null)
                                        <div class="mt-3 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                </svg>
                                                <div>
                                                    <p class="font-semibold text-green-800">{{ $audio_mcq_file->getClientOriginalName() }}</p>
                                                    <p class="text-sm text-green-600">New audio file uploaded - will replace current audio</p>
                                                    <div class="mt-3 modern-audio-player" x-data="{ audioReady: false }" x-init="setTimeout(() => audioReady = true, 1500)">
                                                        <div x-show="!audioReady" class="text-center p-4 bg-yellow-50 border border-yellow-200 rounded">
                                                            <p class="text-sm text-yellow-700">⏳ Preparing audio controls...</p>
                                                        </div>
                                                        <audio x-show="audioReady" controls style="width: 100%;" class="rounded-lg">
                                                            <source src="{{ $audio_mcq_file->temporaryUrl() }}" type="audio/mpeg">
                                                            Your browser does not support the audio element.
                                                        </audio>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-2 text-sm text-gray-500">
                                        <strong>Supported formats:</strong> MP3, WAV, OGG, M4A (Max size: 10MB)<br>
                                        <strong>Tip:</strong> Upload a new audio file to replace the current one, or leave empty to keep existing audio.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Audio MCQ Sub Questions Container -->
                            <div class="space-y-6">
                                @foreach($audio_mcq_sub_questions as $subIndex => $subQuestion)
                                    <div class="sub-question-item" wire:key="audio_mcq_sub_question_{{ $subIndex }}">
                                        <!-- Sub Question Header -->
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="sub-question-title">Sub-question {{ chr(97 + $subIndex) }})</h4>
                                            <div class="button-group">
                                                @if($subIndex === 0)
                                                    <button type="button" wire:click="addAudioMcqSubQuestion" class="add-btn">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                        Add Sub-question
                                                    </button>
                                                @else
                                                    <button type="button" wire:click="removeAudioMcqSubQuestion({{ $subIndex }})" class="remove-btn">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Remove Sub-question
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Sub Question Text -->
                                        <div class="mb-4">
                                            <label class="modern-label">Sub-question {{ chr(97 + $subIndex) }}) Text *</label>
                                            <textarea wire:model="audio_mcq_sub_questions.{{ $subIndex }}.question" rows="2" 
                                                    placeholder="Enter the sub-question text..." class="modern-textarea"></textarea>
                                            @error("audio_mcq_sub_questions.{$subIndex}.question") <p class="error-text">{{ $message }}</p> @enderror
                                        </div>

                                        <!-- Sub Question Options -->
                                        <div class="mb-4">
                                            <label class="modern-label">Options for Sub-question {{ chr(97 + $subIndex) }})</label>
                                            <div class="space-y-3">
                                                @foreach($subQuestion['options'] ?? [] as $optIndex => $option)
                                                    <div class="flex items-center space-x-3" wire:key="audio_mcq_sub_opt_{{ $subIndex }}_{{ $optIndex }}">
                                                        <div class="flex-1">
                                                            <input type="text" wire:model="audio_mcq_sub_questions.{{ $subIndex }}.options.{{ $optIndex }}" 
                                                                   placeholder="Option {{ $optIndex + 1 }}" class="option-input">
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($optIndex === 0 && count($subQuestion['options']) < 6)
                                                                <button type="button" wire:click="addAudioMcqSubQuestionOption({{ $subIndex }})" class="add-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                            @if($optIndex > 1)
                                                                <button type="button" wire:click="removeAudioMcqSubQuestionOption({{ $subIndex }}, {{ $optIndex }})" class="remove-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Correct Answer Indices for this sub-question -->
                                        <div class="mb-4">
                                            <label class="modern-label">Correct Answer Indices for Sub-question {{ chr(97 + $subIndex) }})</label>
                                            <div class="info-banner-small">
                                                <span class="text-sm">Use 0 for first option, 1 for second option, etc. You can select multiple correct answers.</span>
                                            </div>
                                            <div class="space-y-2">
                                                @foreach($subQuestion['correct_indices'] ?? [0] as $ansIndex => $correctIndex)
                                                    <div class="flex items-center space-x-3" wire:key="audio_mcq_sub_ans_{{ $subIndex }}_{{ $ansIndex }}">
                                                        <div class="flex-1">
                                                            <input type="number" wire:model="audio_mcq_sub_questions.{{ $subIndex }}.correct_indices.{{ $ansIndex }}" 
                                                                   min="0" max="{{ max(0, count($subQuestion['options']) - 1) }}" placeholder="0" class="index-input">
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($ansIndex === 0)
                                                                <button type="button" wire:click="addAudioMcqSubQuestionAnswerIndex({{ $subIndex }})" class="add-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                    </svg>
                                                                </button>
                                                            @else
                                                                <button type="button" wire:click="removeAudioMcqSubQuestionAnswerIndex({{ $subIndex }}, {{ $ansIndex }})" class="remove-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                       <!-- Picture MCQ Section -->
<div class="section-block" id="picture-mcq-section" x-show="type === 'picture_mcq'">
    <h3 class="section-title">Picture MCQ (Images to Text Matching)</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Upload images on the left side and create text options on the right side. Students will match each image with the correct text option.
    </div>
    
    <div class="grid grid-cols-2 gap-6">
        <!-- Left Side - Images -->
        <div>
            <h5 class="font-semibold mb-4 text-lg">📷 Images</h5>
            <div id="picture-mcq-images-container" class="space-y-4">
                @foreach($picture_mcq_image_uploads as $idx => $imagePath)
                    <div class="picture-mcq-image-item" wire:key="picture_mcq_image_{{ $idx }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-700">Image {{ $idx + 1 }}</span>
                            @if($idx > 0)
                                <button type="button" wire:click="removePictureMcqImage({{ $idx }})" class="remove-btn-small">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Current Image Display -->
                        @if(isset($picture_mcq_images[$idx]) && !empty($picture_mcq_images[$idx]))
                            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-blue-800 font-medium">Current Image:</span>
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($picture_mcq_images[$idx]) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                                <div class="image-preview-container">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($picture_mcq_images[$idx]) }}" 
                                         alt="Current Image {{ $idx + 1 }}" 
                                         class="image-preview-thumb">
                                </div>
                            </div>
                        @endif
                        
                        <!-- Loading indicator for image upload -->
                        <div wire:loading wire:target="audio_image_text_multiple_pairs.{{ $idx }}.image" class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="loading-spinner">
                                    <svg class="w-5 h-5 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium" style="color: #92400e !important;">Uploading image...</span>
                            </div>
                        </div>

                        <div class="upload-section mt-2">
                            <label class="upload-label flex items-center cursor-pointer">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                {{ (isset($picture_mcq_images[$idx]) && !empty($picture_mcq_images[$idx])) || (isset($picture_mcq_image_uploads[$idx]) && $picture_mcq_image_uploads[$idx]) ? 'Replace Image' : 'Upload Image' }}
                                <input type="file" wire:model="picture_mcq_image_uploads.{{ $idx }}" class="custom-upload-input" accept="image/*">
                            </label>
                            @error("picture_mcq_image_uploads.{$idx}") <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        
                        @if(isset($picture_mcq_image_uploads[$idx]) && $picture_mcq_image_uploads[$idx])
                            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800 font-medium mb-2">New Image Preview - will replace current image:</p>
                                <div class="image-preview-container">
                                    <img src="{{ $picture_mcq_image_uploads[$idx]->temporaryUrl() }}" 
                                         alt="New Preview {{ $idx + 1 }}" 
                                         class="image-preview-thumb">
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="button" wire:click="addPictureMcqImage" class="add-btn mt-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Image
            </button>
        </div>
        
        <!-- Right Side - Text Options -->
        <div>
            <h5 class="font-semibold mb-4 text-lg">📝 Text Options</h5>
            <div id="picture-mcq-right-options-container">
                @foreach($picture_mcq_right_options as $idx => $option)
                    <div class="option-item flex items-center mb-2" wire:key="picture_mcq_right_option_{{ $idx }}">
                        <input type="text" wire:model.live="picture_mcq_right_options.{{ $idx }}" class="option-input flex-1 mr-2" placeholder="Enter text option {{ $idx + 1 }}">
                        @if($idx === 0)
                            <button type="button" wire:click="addPictureMcqRightOption" class="add-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        @else
                            <button type="button" wire:click="removePictureMcqRightOption({{ $idx }})" class="remove-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                    @error("picture_mcq_right_options.{$idx}") <p class="error-text">{{ $message }}</p> @enderror
                @endforeach
            </div>
        </div>
    </div>

    <!-- Correct Answer Pairs for Picture MCQ -->
    <div class="mt-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="sub-question-title">Correct Answer Pairs</h4>
            <div class="flex space-x-2">
                <button type="button" wire:click="addPictureMcqPair" class="add-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Pair
                </button>
                <button type="button" onclick="@this.set('picture_mcq_correct_pairs', Array(2).fill({left: '', right: ''}))" class="clear-all-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear All Pairs
                </button>
            </div>
        </div>
        <div class="info-banner-small">
            <span class="text-sm">Select pairs to match images with text options. Image indices: 0 = first image, 1 = second image, etc. Text indices: 0 = first text option, 1 = second text option, etc.</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4" wire:key="picture-mcq-correct-pairs-section">
            @foreach($picture_mcq_correct_pairs as $pairIdx => $pair)
                <div class="option-item" wire:key="picture-mcq-pair-{{ $pairIdx }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-semibold" style="color: #000 !important;">Correct Pair {{ $pairIdx+1 }}</div>
                        <button type="button" wire:click="removePictureMcqPair({{ $pairIdx }})" class="clear-pair-btn" title="Remove this pair">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="modern-label">Image</label>
                            <select class="option-input" wire:model.live="picture_mcq_correct_pairs.{{ $pairIdx }}.left" wire:key="picture-mcq-left-select-{{ $pairIdx }}-{{ count($picture_mcq_image_uploads) }}-{{ json_encode($picture_mcq_correct_pairs) }}">
                                <option value="">Select Image</option>
                                @php
                                    $totalPicImages = max(count($picture_mcq_images), count($picture_mcq_image_uploads));
                                @endphp
                                @for($idx = 0; $idx < $totalPicImages; $idx++)
                                    @php
                                        $hasExisting = isset($picture_mcq_images[$idx]) && !empty($picture_mcq_images[$idx]);
                                        $hasUpload = isset($picture_mcq_image_uploads[$idx]) && $picture_mcq_image_uploads[$idx];
                                        $alreadySelected = false;
                                        foreach ($picture_mcq_correct_pairs as $otherIdx => $otherPair) {
                                            if ($otherIdx !== $pairIdx && isset($otherPair['left']) && $otherPair['left'] !== '' && $otherPair['left'] !== null && $otherPair['left'] == $idx) {
                                                $alreadySelected = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if(($hasExisting || $hasUpload) && !$alreadySelected)
                                        <option value="{{ $idx }}">{{ $idx }}. Image {{ $idx + 1 }}</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="modern-label">Text Option</label>
                            <select class="option-input" wire:model.live="picture_mcq_correct_pairs.{{ $pairIdx }}.right" wire:key="picture-mcq-right-select-{{ $pairIdx }}-{{ count($picture_mcq_right_options) }}-{{ json_encode($picture_mcq_correct_pairs) }}">
                                <option value="">Select Text Option</option>
                                @foreach($picture_mcq_right_options as $idx => $option)
                                    @php
                                        $alreadySelected = false;
                                        foreach ($picture_mcq_correct_pairs as $otherIdx => $otherPair) {
                                            if ($otherIdx !== $pairIdx && isset($otherPair['right']) && $otherPair['right'] !== '' && $otherPair['right'] !== null && $otherPair['right'] == $idx) {
                                                $alreadySelected = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if(!$alreadySelected && trim($option ?? '') !== '')
                                        <option value="{{ $idx }}">{{ $idx }}. {{ $option }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>                        <!-- Simple True/False Section -->
                        <div class="section-block" id="true-false-section" x-show="type === 'true_false'">
                            <h3 class="section-title">True/False Question</h3>
                            <div class="info-banner">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Create a single True/False statement. Students will choose between True or False. Simply click the correct answer.
                            </div>
                            
                            <!-- True/False Statement -->
                            <div class="mb-6">
                                <label class="modern-label">Statement Text *</label>
                                <textarea wire:model="true_false_statement" rows="3" 
                                        placeholder="Enter the true/false statement..." class="modern-textarea"></textarea>
                                @error('true_false_statement') <p class="error-text">{{ $message }}</p> @enderror
                            </div>

                            <!-- True/False Answer Selection -->
                            <div class="mb-6">
                                <label class="modern-label">Correct Answer</label>
                                <div class="true-false-options">
                                    <div class="flex space-x-4">
                                        <label class="true-false-option true-option {{ ($true_false_answer ?? '') === 'true' ? 'selected' : '' }}"
                                               wire:click="$set('true_false_answer', 'true')">
                                            <div class="option-circle">
                                                <svg class="w-5 h-5 checkmark {{ ($true_false_answer ?? '') === 'true' ? 'show' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <span class="option-text">TRUE</span>
                                        </label>
                                        
                                        <label class="true-false-option false-option {{ ($true_false_answer ?? '') === 'false' ? 'selected' : '' }}"
                                               wire:click="$set('true_false_answer', 'false')">
                                            <div class="option-circle">
                                                <svg class="w-5 h-5 checkmark {{ ($true_false_answer ?? '') === 'false' ? 'show' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <span class="option-text">FALSE</span>
                                        </label>
                                    </div>
                                </div>
                                @error('true_false_answer') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                        </div>

                       <!-- Form Fill Section -->
<div class="section-block" id="form-fill-section" x-show="type === 'form_fill'">
    <h3 class="section-title">Form Fill (Fill in the Blanks)</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Create a paragraph with blanks marked as ___ (three underscores). Add options that students can choose from to fill in the blanks. Then provide the answer key for each blank.
    </div>
    
    <!-- Paragraph with Blanks -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Paragraph with Blanks</h4>
        <div class="form-fill-paragraph-section">
            <label class="modern-label">Paragraph Text (use ___ for blanks) *</label>
            <textarea wire:model.lazy="form_fill_paragraph" rows="6" 
                      placeholder="Enter your paragraph here. Use ___ (three underscores) to mark blanks where students should fill in answers. For example: The capital of France is ___. It is located in the ___ of the country."
                      class="modern-textarea"></textarea>
            @error('form_fill_paragraph') <p class="error-text">{{ $message }}</p> @enderror
            
            @if(trim($form_fill_paragraph ?? ''))
                <div class="paragraph-info">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Detected {{ substr_count($form_fill_paragraph, '___') }} blank(s) in the paragraph.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Options -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Answer Options</h4>
        <div class="space-y-4">
            @foreach($form_fill_options as $index => $option)
                <div class="form-fill-option-item" wire:key="form_fill_option_{{ $index }}">
                    <div class="flex items-center space-x-3">
                        <div class="option-number">{{ $index + 1 }}</div>
                        <div class="flex-1">
                            <input type="text" wire:model.live="form_fill_options.{{ $index }}" 
                                   placeholder="Enter answer option..." class="option-input">
                            @error("form_fill_options.{$index}") <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($index === 0)
                                <button type="button" wire:click="addFormFillOption" class="add-btn-small">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            @endif
                            @if($index > 1)
                                <button type="button" wire:click="removeFormFillOption({{ $index }})" class="remove-btn-small">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Answer Keys -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Answer Keys</h4>
        <div class="answer-key-section">
            <div class="answer-key-info mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Provide the correct answer for each blank in order. The answer must match exactly one of the options above.</span>
            </div>
            
            <div class="space-y-4">
                @foreach($form_fill_answer_key as $index => $answerKey)
                    <div class="answer-key-item" wire:key="form_fill_answer_{{ $index }}">
                        <div class="flex items-center space-x-3">
                            <div class="answer-number">Blank {{ $index + 1 }}</div>
                            <div class="flex-1">
                                <input type="text" wire:model.live="form_fill_answer_key.{{ $index }}" 
                                       placeholder="Enter the correct answer for blank {{ $index + 1 }}..." class="option-input">
                                @error("form_fill_answer_key.{$index}") <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($index === 0)
                                    <button type="button" wire:click="addFormFillAnswerKey" class="add-btn-small">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" wire:click="removeFormFillAnswerKey({{ $index }})" class="remove-btn-small">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="mb-6" wire:key="form-fill-preview-{{ count($form_fill_options) }}-{{ count($form_fill_answer_key) }}">
        <h4 class="sub-question-title mb-4">Preview</h4>
        <div class="preview-section">
            @php
                $filteredAnswerKeys = array_filter($form_fill_answer_key, fn($a) => trim($a ?? '') !== '');
                $previewParagraph = trim($form_fill_paragraph ?? '');
                $hasValidPreview = $previewParagraph && count($filteredAnswerKeys) > 0;
                
                // Create preview with filled answers
                if ($hasValidPreview) {
                    $answerIndex = 0;
                    $filledParagraph = preg_replace_callback('/___/', function($matches) use ($filteredAnswerKeys, &$answerIndex) {
                        if ($answerIndex < count($filteredAnswerKeys)) {
                            $answer = trim($filteredAnswerKeys[$answerIndex]);
                            $answerIndex++;
                            if (!empty($answer)) {
                                return '<span class="filled-answer">' . $answer . '</span>';
                            }
                        }
                        return '<span class="empty-blank">___</span>';
                    }, $previewParagraph);
                }
            @endphp
            
            @if($hasValidPreview)
                <div class="preview-filled-main" wire:key="main-preview-{{ md5($previewParagraph . implode('', $filteredAnswerKeys)) }}">
                    <p class="preview-label-main">✅ Final Sentence with Answers:</p>
                    <div class="filled-paragraph-main">{!! $filledParagraph !!}</div>
                </div>
            @endif
            
            @if(trim($form_fill_paragraph ?? ''))
                <div class="preview-paragraph" wire:key="paragraph-preview-{{ md5($form_fill_paragraph) }}">
                    <p class="preview-label">Original paragraph with blanks:</p>
                    <div class="paragraph-preview">{{ trim($form_fill_paragraph) }}</div>
                </div>
            @endif
            
            <div class="preview-options" wire:key="options-preview-{{ count($form_fill_options) }}">
                <p class="preview-label">Available options for students:</p>
                <div class="options-preview">
                    @php
                        $filteredOptions = array_filter($form_fill_options, fn($o) => trim($o ?? '') !== '');
                    @endphp
                    @if(count($filteredOptions) > 0)
                        @foreach($form_fill_options as $index => $option)
                            @if(trim($option ?? '') !== '')
                                <span class="option-preview" wire:key="option-preview-{{ $index }}-{{ md5($option) }}">{{ trim($option) }}</span>
                            @endif
                        @endforeach
                    @else
                        <span class="no-options-message">No options added yet...</span>
                    @endif
                </div>
            </div>
            
            @if(count($filteredAnswerKeys) > 0)
                <div class="preview-answers" wire:key="answers-preview-{{ count($form_fill_answer_key) }}">
                    <p class="preview-label">Answer key summary:</p>
                    <div class="answers-preview">
                        @foreach($form_fill_answer_key as $index => $answerKey)
                            @if(trim($answerKey ?? '') !== '')
                                <div class="answer-key-preview" wire:key="answer-key-preview-{{ $index }}-{{ md5($answerKey) }}">
                                    <strong>Blank {{ $index + 1 }}:</strong> {{ trim($answerKey) }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Reorder Section -->
<div class="section-block" id="reorder-section" x-show="type === 'reorder'">
    <h3 class="section-title">Sentence Reordering</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Create sentence fragments that students will drag and drop to form the correct sentence. Add the answer key to validate the correct order.
    </div>
    
    <!-- Sentence Fragments -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Sentence Fragments</h4>
        <div class="space-y-4">
            @foreach($reorder_fragments as $index => $fragment)
                <div class="reorder-fragment-item" wire:key="fragment_{{ $index }}">
                    <div class="flex items-center space-x-3">
                        <div class="fragment-number">{{ $index + 1 }}</div>
                        <div class="flex-1">
                            <input type="text" wire:model.live.debounce.300ms="reorder_fragments.{{ $index }}" 
                                   placeholder="Enter sentence fragment..." class="option-input">
                            @error("reorder_fragments.{$index}") <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($index === 0)
                                <button type="button" wire:click="addReorderFragment" class="add-btn-small">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            @endif
                            @if($index > 1)
                                <button type="button" wire:click="removeReorderFragment({{ $index }})" class="remove-btn-small">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Answer Key -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Answer Key</h4>
        <div class="answer-key-section">
            <label class="modern-label">Correct Sentence (Answer Key) *</label>
            <textarea wire:model.lazy="reorder_answer_key" rows="3"
                      placeholder="Enter the complete correct sentence that should be formed when fragments are arranged properly..."
                      class="modern-textarea"></textarea>
            @error('reorder_answer_key') <p class="error-text">{{ $message }}</p> @enderror
            
            <div class="answer-key-info">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>This is the target sentence that students should create by reordering the fragments above.</span>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="mb-6" wire:key="preview-section-{{ count($reorder_fragments) }}">
        <h4 class="sub-question-title mb-4">Preview</h4>
        <div class="preview-section">
            <div class="preview-fragments" wire:key="fragments-preview-{{ count($reorder_fragments) }}">
                <p class="preview-label">Fragments to be reordered:</p>
                <div class="fragments-preview">
                    @php
                        $filteredFragments = array_filter($reorder_fragments, fn($f) => trim($f ?? '') !== '');
                    @endphp
                    @if(count($filteredFragments) > 0)
                        @foreach($reorder_fragments as $index => $fragment)
                            @if(trim($fragment ?? '') !== '')
                                <span class="fragment-preview" wire:key="fragment-preview-{{ $index }}-{{ md5($fragment) }}">{{ trim($fragment) }}</span>
                            @endif
                        @endforeach
                    @else
                        <span class="no-fragments-message">No fragments added yet...</span>
                    @endif
                </div>
            </div>
            
            @if(trim($reorder_answer_key ?? ''))
                <div class="preview-answer" wire:key="answer-preview-{{ md5($reorder_answer_key) }}">
                    <p class="preview-label">Expected result:</p>
                    <div class="answer-preview">{{ trim($reorder_answer_key) }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

                       <!-- True/False Multiple Section -->
<div class="section-block" id="true-false-multiple-section" x-show="type === 'true_false_multiple'">
    <h3 class="section-title">True/False Multiple Questions</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Create multiple True/False statements (a, b, c, etc.). Each statement will have True and False options. Simply click the correct answer for each statement.
    </div>
    
    <!-- True/False Sub Questions Container -->
    <div class="space-y-6">
        @foreach($true_false_questions as $tfIndex => $tfQuestion)
            <div class="true-false-item" wire:key="tf_question_{{ $tfIndex }}">
                <!-- True/False Question Header -->
                <div class="flex items-center justify-between mb-4">
                    <h4 class="sub-question-title">Statement {{ chr(97 + $tfIndex) }})</h4>
                    <div class="button-group">
                        @if($tfIndex === 0)
                            <button type="button" wire:click="addTrueFalseQuestion" class="add-btn">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Statement
                            </button>
                        @else
                            <button type="button" wire:click="removeTrueFalseQuestion({{ $tfIndex }})" class="remove-btn">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Remove Statement
                            </button>
                        @endif
                    </div>
                </div>

                <!-- True/False Statement Text -->
                <div class="mb-4">
                    <label class="modern-label">Statement {{ chr(97 + $tfIndex) }}) Text *</label>
                    <textarea wire:model="true_false_questions.{{ $tfIndex }}.statement" rows="2" 
                            placeholder="Enter the true/false statement..." class="modern-textarea"></textarea>
                    @error("true_false_questions.{$tfIndex}.statement") <p class="error-text">{{ $message }}</p> @enderror
                </div>

                <!-- True/False Answer Selection -->
                <div class="mb-4">
                    <label class="modern-label">Correct Answer for Statement {{ chr(97 + $tfIndex) }})</label>
                    <div class="true-false-options">
                        <div class="flex space-x-4">
                            <label class="true-false-option true-option {{ ($tfQuestion['correct_answer'] ?? '') === 'true' ? 'selected' : '' }}"
                                   wire:click="setTrueFalseAnswer({{ $tfIndex }}, 'true')">
                                <div class="option-circle">
                                    <svg class="w-5 h-5 checkmark {{ ($tfQuestion['correct_answer'] ?? '') === 'true' ? 'show' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="option-text">TRUE</span>
                            </label>
                            
                            <label class="true-false-option false-option {{ ($tfQuestion['correct_answer'] ?? '') === 'false' ? 'selected' : '' }}"
                                   wire:click="setTrueFalseAnswer({{ $tfIndex }}, 'false')">
                                <div class="option-circle">
                                    <svg class="w-5 h-5 checkmark {{ ($tfQuestion['correct_answer'] ?? '') === 'false' ? 'show' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="option-text">FALSE</span>
                            </label>
                        </div>
                    </div>
                    @error("true_false_questions.{$tfIndex}.correct_answer") <p class="error-text">{{ $message }}</p> @enderror
                </div>
            </div>
        @endforeach
    </div>
</div>                       <!-- MCQ Multiple Section -->
<div class="section-block" id="mcq-multiple-section" x-show="type === 'mcq_multiple'">
    <h3 class="section-title">MCQ Multiple Questions</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Create multiple sub-questions (a, b, c, etc.) each with their own options and correct answers. Each sub-question can have multiple correct answers.
    </div>
    
    <!-- Sub Questions Container -->
    <div class="space-y-6">
        @foreach($sub_questions as $subIndex => $subQuestion)
            <div class="sub-question-item" wire:key="sub_question_{{ $subIndex }}">
                <!-- Sub Question Header -->
                <div class="flex items-center justify-between mb-4">
                    <h4 class="sub-question-title">Sub-question {{ chr(97 + $subIndex) }})</h4>
                    <div class="button-group">
                        @if($subIndex === 0)
                            <button type="button" wire:click="addSubQuestion" class="add-btn">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Sub-question
                            </button>
                        @else
                            <button type="button" wire:click="removeSubQuestion({{ $subIndex }})" class="remove-btn">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Remove Sub-question
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Sub Question Text -->
                <div class="mb-4">
                    <label class="modern-label">Sub-question {{ chr(97 + $subIndex) }}) Text *</label>
                    <textarea wire:model="sub_questions.{{ $subIndex }}.question" rows="2" 
                            placeholder="Enter the sub-question text..." class="modern-textarea"></textarea>
                    @error("sub_questions.{$subIndex}.question") <p class="error-text">{{ $message }}</p> @enderror
                </div>

                <!-- Sub Question Options -->
                <div class="mb-4">
                    <label class="modern-label">Options for Sub-question {{ chr(97 + $subIndex) }})</label>
                    <div class="space-y-3">
                        @foreach($subQuestion['options'] ?? [] as $optIndex => $option)
                            <div class="flex items-center space-x-3" wire:key="sub_opt_{{ $subIndex }}_{{ $optIndex }}">
                                <div class="flex-1">
                                    <input type="text" wire:model="sub_questions.{{ $subIndex }}.options.{{ $optIndex }}" 
                                           placeholder="Option {{ $optIndex + 1 }}" class="option-input">
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($optIndex === 0 && count($subQuestion['options']) < 6)
                                        <button type="button" wire:click="addSubQuestionOption({{ $subIndex }})" class="add-btn-small">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    @if($optIndex > 1)
                                        <button type="button" wire:click="removeSubQuestionOption({{ $subIndex }}, {{ $optIndex }})" class="remove-btn-small">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Correct Answer Indices for this sub-question -->
                <div class="mb-4">
                    <label class="modern-label">Correct Answer Indices for Sub-question {{ chr(97 + $subIndex) }})</label>
                    <div class="info-banner-small">
                        <span class="text-sm">Use 0 for first option, 1 for second option, etc. You can select multiple correct answers.</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($subQuestion['correct_indices'] ?? [0] as $ansIndex => $correctIndex)
                            <div class="flex items-center space-x-3" wire:key="sub_ans_{{ $subIndex }}_{{ $ansIndex }}">
                                <div class="flex-1">
                                    <input type="number" wire:model="sub_questions.{{ $subIndex }}.correct_indices.{{ $ansIndex }}" 
                                           min="0" max="{{ max(0, count($subQuestion['options']) - 1) }}" placeholder="0" class="index-input">
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($ansIndex === 0)
                                        <button type="button" wire:click="addSubQuestionAnswerIndex({{ $subIndex }})" class="add-btn-small">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="button" wire:click="removeSubQuestionAnswerIndex({{ $subIndex }}, {{ $ansIndex }})" class="remove-btn-small">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
                        <!-- Question Options Section (for regular MCQ) -->
                        <div class="section-block" id="question-options-section" x-show="type !== 'statement_match' && type !== 'opinion' && type !== 'mcq_multiple' && type !== 'true_false_multiple' && type !== 'true_false' && type !== 'reorder' && type !== 'form_fill' && type !== 'picture_mcq' && type !== 'audio_mcq_single' && type !== 'audio_image_text_single' && type !== 'audio_image_text_multiple' && type !== 'audio_fill_blank' && type !== 'picture_fill_blank' && type !== 'video_fill_blank' && type !== 'audio_picture_match'">
                            <h3 class="section-title">Question Options</h3>
                            <div class="options-wrapper">
                                <div id="options-container" class="space-y-4">
                                    @foreach($options as $index => $option)
                                        <div class="option-item">
                                            <div class="flex items-center justify-between mb-3">
                                                <label class="option-label">Option {{ $index + 1 }}</label>
                                                <div class="button-group">
                                                    @if($index === 0)
                                                        <button type="button" wire:click="addOption" class="add-btn">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                            Add Option
                                                        </button>
                                                    @else
                                                        <button type="button" wire:click="removeOption({{ $index }})" class="remove-btn">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Remove
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="text" wire:model="options.{{ $index }}" placeholder="Enter option text..." class="option-input">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Correct Answer Selection -->
                            <div class="mt-6">
                                <h4 class="sub-question-title mb-4">Correct Answer Selection</h4>
                                <div class="info-banner-small">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm">Select the correct answer(s) from the options above. Use 0 for first option, 1 for second option, etc.</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($answer_indices as $index => $answerIndex)
                                        <div class="flex items-center space-x-3" wire:key="answer_index_{{ $index }}">
                                            <div class="flex-1">
                                                <label class="modern-label">Correct Answer {{ $index + 1 }}</label>
                                                <select wire:model="answer_indices.{{ $index }}" class="modern-select">
                                                    @for($optIndex = 0; $optIndex < count($options); $optIndex++)
                                                        <option value="{{ $optIndex }}" {{ $answerIndex == $optIndex ? 'selected' : '' }}>
                                                            Option {{ $optIndex + 1 }}: {{ $options[$optIndex] ?? '' }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @if($index === 0)
                                                    <button type="button" wire:click="addAnswerIndex" class="add-btn-small">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button type="button" wire:click="removeAnswerIndex({{ $index }})" class="remove-btn-small">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Opinion Type Section -->
                        <div class="section-block" id="opinion-section" x-show="type === 'opinion'">
                            <h3 class="section-title">Opinion Question</h3>
                            <div class="info-banner">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Opinion questions are open-ended. Students will provide their own text response. You can optionally provide a sample answer for reference.
                            </div>
                            <div>
                                <label class="modern-label">Expected/Sample Answer (Optional)</label>
                                <textarea wire:model="opinion_answer" rows="4" class="modern-textarea" placeholder="Enter a sample or expected opinion answer (this is optional and for reference only)..."></textarea>
                                @error('opinion_answer') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        
<!-- Audio Fill in the Blanks Section -->
<div class="section-block" id="audio-fill-blank-section" x-show="type === 'audio_fill_blank'">
    <h3 class="section-title">Audio Fill in the Blanks</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Upload an audio file, enter a paragraph with blanks (use ___ for blanks), and provide the correct answers for each blank. Students will listen and fill in the blanks.
    </div>
    <!-- Audio File Upload/Player -->
    <div class="mb-6">
        <label class="modern-label">Audio File *</label>
        @php
            $currentAudioFile = $record->question_data ? (json_decode($record->question_data, true)['audio_file'] ?? null) : null;
            if (!$currentAudioFile && isset($record->audio_fill_audio_file)) {
                $currentAudioFile = $record->audio_fill_audio_file;
            }
        @endphp
        @if($currentAudioFile)
            <div class="mb-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-green-800">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                        Current: {{ basename($currentAudioFile) }}
                    </span>
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($currentAudioFile) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
                <div class="mt-3 modern-audio-player">
                    <audio controls style="width: 100%;" class="rounded-lg">
                        <source src="{{ \Illuminate\Support\Facades\Storage::url($currentAudioFile) }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        @endif
        <input type="file" wire:model="audio_fill_audio_file" class="modern-input" accept="audio/*">
        @error('audio_fill_audio_file') <p class="error-text">{{ $message }}</p> @enderror
    </div>
    <!-- Paragraph with Blanks -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Paragraph with Blanks</h4>
        <div class="form-fill-paragraph-section">
            <label class="modern-label">Paragraph Text (use ___ for blanks) *</label>
            <textarea wire:model.lazy="audio_fill_paragraph" rows="6"
                      placeholder="Enter your paragraph here. Use ___ (three underscores) to mark blanks where students should fill in answers."
                      class="modern-textarea"></textarea>
            @error('audio_fill_paragraph') <p class="error-text">{{ $message }}</p> @enderror
            @if(trim($audio_fill_paragraph ?? ''))
                <div class="paragraph-info">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Contains {{ substr_count($audio_fill_paragraph, '___') }} blank(s) to fill.</span>
                </div>
            @endif
        </div>
    </div>
    <!-- Answer Keys -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Correct Answers for Each Blank</h4>
        <div class="space-y-4">
            @foreach($audio_fill_answer_key as $index => $answerKey)
                <div class="answer-key-item" wire:key="audio-fill-answer-{{ $index }}">
                    <div class="flex items-center space-x-3">
                        <div class="answer-number">Blank {{ $index + 1 }}</div>
                        <div class="flex-1">
                            <input type="text" wire:model.live="audio_fill_answer_key.{{ $index }}"
                                   placeholder="Enter the correct answer for blank {{ $index + 1 }}..." class="option-input">
                            @error("audio_fill_answer_key.{$index}") <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        @if(count($audio_fill_answer_key) > 1)
                            <button type="button" wire:click="removeAudioFillAnswerKey({{ $index }})" class="remove-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addAudioFillAnswerKey" class="add-btn mt-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Blank/Answer
        </button>
    </div>
    <!-- Preview and Answers for Audio Fill in the Blanks -->
    @php
        $filteredAnswerKeys = array_filter($audio_fill_answer_key ?? [], fn($a) => trim($a ?? '') !== '');
        $previewParagraph = trim($audio_fill_paragraph ?? '');
        $hasValidPreview = $previewParagraph && count($filteredAnswerKeys) > 0;
        if ($hasValidPreview) {
            $answerIndex = 0;
            $filledParagraph = preg_replace_callback('/___/', function($matches) use ($filteredAnswerKeys, &$answerIndex) {
                if ($answerIndex < count($filteredAnswerKeys)) {
                    $answer = trim($filteredAnswerKeys[$answerIndex]);
                    $answerIndex++;
                    if (!empty($answer)) {
                        return '<span class="filled-answer">' . $answer . '</span>';
                    }
                }
                return '<span class="empty-blank">___</span>';
            }, $previewParagraph);
        }
    @endphp
    <div class="mb-6" wire:key="audio-fill-preview-{{ count($audio_fill_answer_key) }}">
        <h4 class="sub-question-title mb-4">Preview - Complete Sentence</h4>
        <div class="preview-section">
            @if($hasValidPreview)
                <div class="preview-filled-main" wire:key="audio-fill-main-preview-{{ md5($previewParagraph . implode('', $filteredAnswerKeys)) }}">
                    <p class="preview-label-main">✅ COMPLETE SENTENCE WITH ANSWERS:</p>
                    <div class="filled-paragraph-main">{!! $filledParagraph !!}</div>
                </div>
            @endif
            @if(trim($audio_fill_paragraph ?? ''))
                <div class="preview-paragraph" wire:key="audio-fill-paragraph-preview-{{ md5($audio_fill_paragraph) }}">
                    <p class="preview-label">Original paragraph with blanks:</p>
                    <div class="paragraph-preview">{{ trim($audio_fill_paragraph) }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Picture Fill in the Blanks Section -->
<div class="section-block" id="picture-fill-blank-section" x-show="type === 'picture_fill_blank'">
    <h3 class="section-title">Picture Fill in the Blanks</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Upload an image, enter a paragraph with blanks (use ___ for blanks), and provide the correct answers for each blank. Students will look at the image and fill in the blanks.
    </div>
    <!-- Image File Upload/Preview -->
    <div class="mb-6">
        <label class="modern-label">Image File *</label>
        @php
            $currentImageFile = $record->question_data ? (json_decode($record->question_data, true)['image_file'] ?? null) : null;
            if (!$currentImageFile && isset($record->picture_fill_image)) {
                $currentImageFile = $record->picture_fill_image;
            }
        @endphp
        @if($currentImageFile)
            <div class="mb-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-green-800">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Current: {{ basename($currentImageFile) }}
                    </span>
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($currentImageFile) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
                <div class="mt-3">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($currentImageFile) }}" alt="Current Image" class="max-w-xs rounded-lg border-2 border-blue-300">
                </div>
            </div>
        @endif
        <input type="file" wire:model="picture_fill_image" class="modern-input" accept="image/*">
        @error('picture_fill_image') <p class="error-text">{{ $message }}</p> @enderror
    </div>
    <!-- Paragraph with Blanks -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Paragraph with Blanks</h4>
        <div class="form-fill-paragraph-section">
            <label class="modern-label">Paragraph Text (use ___ for blanks) *</label>
            <textarea wire:model.lazy="picture_fill_paragraph" rows="6"
                      placeholder="Enter your paragraph here. Use ___ (three underscores) to mark blanks where students should fill in answers."
                      class="modern-textarea"></textarea>
            @error('picture_fill_paragraph') <p class="error-text">{{ $message }}</p> @enderror
            @if(trim($picture_fill_paragraph ?? ''))
                <div class="paragraph-info">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Contains {{ substr_count($picture_fill_paragraph, '___') }} blank(s) to fill.</span>
                </div>
            @endif
        </div>
    </div>
    <!-- Answer Keys -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Correct Answers for Each Blank</h4>
        <div class="space-y-4">
            @foreach($picture_fill_answer_key as $index => $answerKey)
                <div class="answer-key-item" wire:key="picture-fill-answer-{{ $index }}">
                    <div class="flex items-center space-x-3">
                        <div class="answer-number">Blank {{ $index + 1 }}</div>
                        <div class="flex-1">
                            <input type="text" wire:model.live="picture_fill_answer_key.{{ $index }}"
                                   placeholder="Enter the correct answer for blank {{ $index + 1 }}..." class="option-input">
                            @error("picture_fill_answer_key.{$index}") <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        @if(count($picture_fill_answer_key) > 1)
                            <button type="button" wire:click="removePictureFillAnswerKey({{ $index }})" class="remove-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addPictureFillAnswerKey" class="add-btn mt-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Blank/Answer
        </button>
    </div>
    <!-- Preview and Answers for Picture Fill in the Blanks -->
    @php
        $filteredAnswerKeys = array_filter($picture_fill_answer_key ?? [], fn($a) => trim($a ?? '') !== '');
        $previewParagraph = trim($picture_fill_paragraph ?? '');
        $hasValidPreview = $previewParagraph && count($filteredAnswerKeys) > 0;
        if ($hasValidPreview) {
            $answerIndex = 0;
            $filledParagraph = preg_replace_callback('/___/', function($matches) use ($filteredAnswerKeys, &$answerIndex) {
                if ($answerIndex < count($filteredAnswerKeys)) {
                    $answer = trim($filteredAnswerKeys[$answerIndex]);
                    $answerIndex++;
                    if (!empty($answer)) {
                        return '<span class="filled-answer">' . $answer . '</span>';
                    }
                }
                return '<span class="empty-blank">___</span>';
            }, $previewParagraph);
        }
    @endphp
    <div class="mb-6" wire:key="picture-fill-preview-{{ count($picture_fill_answer_key) }}">
        <h4 class="sub-question-title mb-4">Preview - Complete Sentence</h4>
        <div class="preview-section">
            @if($hasValidPreview)
                <div class="preview-filled-main" wire:key="picture-fill-main-preview-{{ md5($previewParagraph . implode('', $filteredAnswerKeys)) }}">
                    <p class="preview-label-main">✅ COMPLETE SENTENCE WITH ANSWERS:</p>
                    <div class="filled-paragraph-main">{!! $filledParagraph !!}</div>
                </div>
            @endif
            @if(trim($picture_fill_paragraph ?? ''))
                <div class="preview-paragraph" wire:key="picture-fill-paragraph-preview-{{ md5($picture_fill_paragraph) }}">
                    <p class="preview-label">Original paragraph with blanks:</p>
                    <div class="paragraph-preview">{{ trim($picture_fill_paragraph) }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Video Fill in the Blanks Section -->
<div class="section-block" id="video-fill-blank-section" x-show="type === 'video_fill_blank'">
    <h3 class="section-title">Video Fill in the Blanks</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Upload a video, enter a paragraph with blanks (use ___ for blanks), and provide the correct answers for each blank. Students will watch the video and fill in the blanks.
    </div>
    <!-- Video File Upload/Preview -->
    <div class="mb-6">
        <label class="modern-label">Video File *</label>
        @php
            $currentVideoFile = $record->question_data ? (json_decode($record->question_data, true)['video_file'] ?? null) : null;
            if (!$currentVideoFile && isset($record->video_fill_video)) {
                $currentVideoFile = $record->video_fill_video;
            }
        @endphp
        @if($currentVideoFile)
            <div class="mb-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-green-800">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A2 2 0 0122 9.618v4.764a2 2 0 01-2.447 1.894L15 14M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2z"></path>
                        </svg>
                        Current: {{ basename($currentVideoFile) }}
                    </span>
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($currentVideoFile) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
                <div class="mt-3">
                    <video controls class="max-w-xs rounded-lg border-2 border-blue-300">
                        <source src="{{ \Illuminate\Support\Facades\Storage::url($currentVideoFile) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        @endif
        <input type="file" wire:model="video_fill_video" class="modern-input" accept="video/*">
        @error('video_fill_video') <p class="error-text">{{ $message }}</p> @enderror
    </div>
    <!-- Paragraph with Blanks -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Paragraph with Blanks</h4>
        <div class="form-fill-paragraph-section">
            <label class="modern-label">Paragraph Text (use ___ for blanks) *</label>
            <textarea wire:model.lazy="video_fill_paragraph" rows="6"
                      placeholder="Enter your paragraph here. Use ___ (three underscores) to mark blanks where students should fill in answers."
                      class="modern-textarea"></textarea>
            @error('video_fill_paragraph') <p class="error-text">{{ $message }}</p> @enderror
            @if(trim($video_fill_paragraph ?? ''))
                <div class="paragraph-info">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Contains {{ substr_count($video_fill_paragraph, '___') }} blank(s) to fill.</span>
                </div>
            @endif
        </div>
    </div>
    <!-- Answer Keys -->
    <div class="mb-6">
        <h4 class="sub-question-title mb-4">Correct Answers for Each Blank</h4>
        <div class="space-y-4">
            @foreach($video_fill_answer_key as $index => $answerKey)
                <div class="answer-key-item" wire:key="video-fill-answer-{{ $index }}">
                    <div class="flex items-center space-x-3">
                        <div class="answer-number">Blank {{ $index + 1 }}</div>
                        <div class="flex-1">
                            <input type="text" wire:model.live="video_fill_answer_key.{{ $index }}"
                                   placeholder="Enter the correct answer for blank {{ $index + 1 }}..." class="option-input">
                            @error("video_fill_answer_key.{$index}") <p class="error-text">{{ $message }}</p> @enderror
                        </div>
                        @if(count($video_fill_answer_key) > 1)
                            <button type="button" wire:click="removeVideoFillAnswerKey({{ $index }})" class="remove-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" wire:click="addVideoFillAnswerKey" class="add-btn mt-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Blank/Answer
        </button>
    </div>
    <!-- Preview and Answers for Video Fill in the Blanks -->
    @php
        $filteredAnswerKeys = array_filter($video_fill_answer_key ?? [], fn($a) => trim($a ?? '') !== '');
        $previewParagraph = trim($video_fill_paragraph ?? '');
        $hasValidPreview = $previewParagraph && count($filteredAnswerKeys) > 0;
        if ($hasValidPreview) {
            $answerIndex = 0;
            $filledParagraph = preg_replace_callback('/___/', function($matches) use ($filteredAnswerKeys, &$answerIndex) {
                if ($answerIndex < count($filteredAnswerKeys)) {
                    $answer = trim($filteredAnswerKeys[$answerIndex]);
                    $answerIndex++;
                    if (!empty($answer)) {
                        return '<span class=\"filled-answer\">' . $answer . '</span>';
                    }
                }
                return '<span class=\"empty-blank\">___</span>';
            }, $previewParagraph);
        }
    @endphp
    <div class="mb-6" wire:key="video-fill-preview-{{ count($video_fill_answer_key) }}">
        <h4 class="sub-question-title mb-4">Preview - Complete Sentence</h4>
        <div class="preview-section">
            @if($hasValidPreview)
                <div class="preview-filled-main" wire:key="video-fill-main-preview-{{ md5($previewParagraph . implode('', $filteredAnswerKeys)) }}">
                    <p class="preview-label-main">✅ COMPLETE SENTENCE WITH ANSWERS:</p>
                    <div class="filled-paragraph-main">{!! $filledParagraph !!}</div>
                </div>
            @endif
            @if(trim($video_fill_paragraph ?? ''))
                <div class="preview-paragraph" wire:key="video-fill-paragraph-preview-{{ md5($video_fill_paragraph) }}">
                    <p class="preview-label">Original paragraph with blanks:</p>
                    <div class="paragraph-preview">{{ trim($video_fill_paragraph) }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Audio + Picture Matching Section -->
<div class="section-block" id="audio-picture-match-section" x-show="type === 'audio_picture_match' || question_type_id === 'audio_picture_match'">
    <h3 class="section-title">Audio + Picture Matching</h3>
    <div class="info-banner">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Upload audio files on the left and image files on the right. Students will match each audio to the correct image.
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left Side - Audio Files -->
        <div>
            <h5 class="font-semibold mb-4 text-lg">🎵 Audio Files</h5>
            <div id="audio-picture-match-audios-container">
                @foreach($audio_picture_audios as $idx => $audioUpload)
                    <div class="audio-picture-audio-item flex flex-col mb-4 p-4 border-2 border-dashed border-blue-300 rounded-lg" wire:key="audio_picture_audio_{{ $idx }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-700">Audio {{ $idx + 1 }}</span>
                            @if($idx > 0)
                                <button type="button" wire:click="removeAudioPictureAudio({{ $idx }})" class="remove-btn-small">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        </div>
                        <input type="file" wire:model="audio_picture_audios.{{ $idx }}" class="modern-input" accept="audio/*" placeholder="Upload audio file">
                        @error("audio_picture_audios.{$idx}") <p class="error-text">{{ $message }}</p> @enderror
                        @if($audioUpload)
                            <div class="mt-2">
                                @if($audioUpload instanceof \Illuminate\Http\UploadedFile)
                                    <audio controls class="w-full">
                                        <source src="{{ $audioUpload->temporaryUrl() }}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                @else
                                    <audio controls class="w-full">
                                        <source src="{{ \Illuminate\Support\Facades\Storage::url($audioUpload) }}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="button" wire:click="addAudioPictureAudio" class="add-btn mt-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Audio
            </button>
        </div>
        <!-- Right Side - Image Files -->
        <div>
            <h5 class="font-semibold mb-4 text-lg">🖼️ Image Files</h5>
            <div id="audio-picture-match-images-container">
                @foreach($audio_picture_images as $idx => $imageUpload)
                    <div class="audio-picture-image-item flex flex-col mb-4 p-4 border-2 border-dashed border-purple-300 rounded-lg" wire:key="audio_picture_image_{{ $idx }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-700">Image {{ $idx + 1 }}</span>
                            @if($idx > 0)
                                <button type="button" wire:click="removeAudioPictureImage({{ $idx }})" class="remove-btn-small">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        </div>
                        <input type="file" wire:model="audio_picture_images.{{ $idx }}" class="modern-input" accept="image/*" placeholder="Upload image file">
                        @error("audio_picture_images.{$idx}") <p class="error-text">{{ $message }}</p> @enderror
                        @if($imageUpload)
                            <div class="mt-2">
                                @if($imageUpload instanceof \Illuminate\Http\UploadedFile)
                                    <img src="{{ $imageUpload->temporaryUrl() }}" alt="Preview" class="preview-image object-cover rounded border" style="width: 120px; height: 120px; max-width: 100%; max-height: 100%;">
                                @else
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($imageUpload) }}" alt="Preview" class="preview-image object-cover rounded border" style="width: 120px; height: 120px; max-width: 100%; max-height: 100%;">
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="button" wire:click="addAudioPictureImage" class="add-btn mt-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Image
            </button>
        </div>
    </div>

    <!-- Matching Pairs Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="sub-question-title">Correct Answer Pairs</h4>
            <div class="flex space-x-2">
                <button type="button" wire:click="addAudioPicturePair" class="add-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Pair
                </button>
                <button type="button" wire:click="clearAllAudioPicturePairs" class="clear-all-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear All Pairs
                </button>
            </div>
        </div>
        <div class="info-banner-small mb-4">
            <span class="text-sm">Audio indices: 0 = first audio, 1 = second audio, etc. Image indices: 0 = first image, 1 = second image, etc.</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" wire:key="audio-picture-correct-pairs-section">
            @foreach($audio_picture_pairs as $pairIdx => $pair)
                <div class="option-item bg-white p-4 rounded-lg border border-gray-200" wire:key="audio-picture-pair-{{ $pairIdx }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-semibold text-gray-900">Correct Pair {{ $pairIdx+1 }}</div>
                        <button type="button" wire:click="removeAudioPicturePair({{ $pairIdx }})" class="clear-pair-btn" title="Remove this pair">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="modern-label">Audio</label>
                            <select class="option-input" wire:model.live="audio_picture_pairs.{{ $pairIdx }}.left" wire:key="audio-picture-left-select-{{ $pairIdx }}-{{ count($audio_picture_audios ?? []) }}">
                                <option value="">Select Audio</option>
                                @foreach($audio_picture_audios as $idx => $audio)
                                    <option value="{{ $idx }}">{{ $idx }}. Audio {{ $idx + 1 }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="modern-label">Image</label>
                            <select class="option-input" wire:model.live="audio_picture_pairs.{{ $pairIdx }}.right" wire:key="audio-picture-right-select-{{ $pairIdx }}-{{ count($audio_picture_images ?? []) }}">
                                <option value="">Select Image</option>
                                @foreach($audio_picture_images as $idx => $image)
                                    <option value="{{ $idx }}">{{ $idx }}. Image {{ $idx + 1 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Preview Section -->
    <div class="mb-6" wire:key="audio-picture-preview-{{ count($audio_picture_audios) }}-{{ count($audio_picture_images) }}-{{ count($audio_picture_pairs) }}">
        <h4 class="sub-question-title mb-4">Preview</h4>
        <div class="preview-section">
            @php
                $validPairs = array_filter($audio_picture_pairs ?? [], function($pair) {
                    return isset($pair['left'], $pair['right']) && $pair['left'] !== '' && $pair['right'] !== '' && $pair['left'] !== null && $pair['right'] !== null;
                });
            @endphp
            @if(count($validPairs) > 0)
                <p class="preview-label mb-4">💡 Audio to Picture Matching Preview:</p>
                <div class="space-y-6">
                    @foreach($validPairs as $index => $pair)
                        @php
                            $audioIndex = (int)$pair['left'];
                            $imageIndex = (int)$pair['right'];
                            $audioUpload = $audio_picture_audios[$audioIndex] ?? null;
                            $imageUpload = $audio_picture_images[$imageIndex] ?? null;
                        @endphp
                        @if($audioUpload && $imageUpload)
                            <div class="audio-picture-match-item">
                                <div class="flex items-center justify-center space-x-8 p-6 bg-white border-2 border-green-200 rounded-xl">
                                    <!-- Audio Section -->
                                    <div class="flex flex-col items-center space-y-2">
                                        @if($audioUpload instanceof \Illuminate\Http\UploadedFile)
                                            <audio controls class="w-40">
                                                <source src="{{ $audioUpload->temporaryUrl() }}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        @else
                                            <audio controls class="w-40">
                                                <source src="{{ \Illuminate\Support\Facades\Storage::url($audioUpload) }}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        @endif
                                        <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">Audio {{ $audioIndex + 1 }}</span>
                                    </div>
                                    <!-- Arrow -->
                                    <div class="flex items-center px-4">
                                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </div>
                                    <!-- Image Section -->
                                    <div class="flex flex-col items-center space-y-2">
                                        @if($imageUpload instanceof \Illuminate\Http\UploadedFile)
                                            <img src="{{ $imageUpload->temporaryUrl() }}" alt="Image {{ $imageIndex + 1 }}" class="preview-image object-cover rounded-lg border-2 border-blue-300" style="width: 120px; height: 120px;">
                                        @else
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($imageUpload) }}" alt="Image {{ $imageIndex + 1 }}" class="preview-image object-cover rounded-lg border-2 border-blue-300" style="width: 120px; height: 120px;">
                                        @endif
                                        <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">Image {{ $imageIndex + 1 }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500 mb-2">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">Upload audio and image files, then set answer pairs to see preview</p>
                </div>
            @endif
        </div>
    </div>
</div>

                       <!-- Statement Match Section -->
<div class="section-block" id="statement-match-section" x-show="type === 'statement_match'">
    <h3 class="section-title">Statement Match</h3>
    <div class="grid grid-cols-2 gap-6">
        <!-- Left Side Options -->
        <div>
            <h5 class="font-semibold mb-2">Left Side Options</h5>
            <div id="left-options-container">
                @foreach($left_options as $idx => $option)
                    <div class="option-item flex items-center mb-2" wire:key="left_option_{{ $idx }}">
                        <input type="text" wire:model.live="left_options.{{ $idx }}" class="option-input flex-1 mr-2" placeholder="Enter left option">
                        @if($idx === 0)
                            <button type="button" wire:click="addLeftOption" class="add-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        @else
                            <button type="button" wire:click="removeLeftOption({{ $idx }})" class="remove-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Right Side Options -->
        <div>
            <h5 class="font-semibold mb-2">Right Side Options</h5>
            <div id="right-options-container">
                @foreach($right_options as $idx => $option)
                    <div class="option-item flex items-center mb-2" wire:key="right_option_{{ $idx }}">
                        <input type="text" wire:model.live="right_options.{{ $idx }}" class="option-input flex-1 mr-2" placeholder="Enter right option">
                        @if($idx === 0)
                            <button type="button" wire:click="addRightOption" class="add-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        @else
                            <button type="button" wire:click="removeRightOption({{ $idx }})" class="remove-btn-small">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Correct Answer Pairs Section for Statement Match -->
    <div class="mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title">Correct Answer Pairs</h3>
            <div class="flex space-x-2">
                <button type="button" wire:click="addStatementMatchPair" class="add-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Pair
                </button>
                <button type="button" wire:click="clearAllStatementMatchPairs" class="clear-all-btn">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear All Pairs
                </button>
            </div>
        </div>
        <div class="info-banner">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Select at least 1 pair. Use indices starting from 0. Left side: 0 = first option, 1 = second option, etc. Right side: 0 = first option, 1 = second option, etc.
                </div>
        <div class="grid grid-cols-2 gap-6 mt-4" wire:key="correct-pairs-section">
            @foreach($correct_pairs as $pairIdx => $pair)
                <div class="option-item" wire:key="pair-{{ $pairIdx }}">
                    <div class="flex items-center justify-between mb-3">
                        <div class="mb-2 font-semibold" style="color: #000 !important;">Correct Pair {{ $pairIdx+1 }}</div>
                        <button type="button" wire:click="removeStatementMatchPair({{ $pairIdx }})" class="clear-pair-btn" title="Remove this pair">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="modern-label">Left Option</label>
                            <select class="option-input" wire:model.live="correct_pairs.{{ $pairIdx }}.left" wire:key="left-select-{{ $pairIdx }}-{{ count($left_options) }}">
                                <option value="">Select Left Option</option>
                                @foreach($this->getFilteredLeftOptions() as $idx => $option)
                                    @php
                                        $alreadySelected = false;
                                        foreach ($correct_pairs as $otherIdx => $otherPair) {
                                            if ($otherIdx !== $pairIdx && isset($otherPair['left']) && $otherPair['left'] !== '' && $otherPair['left'] == $idx) {
                                                $alreadySelected = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if(!$alreadySelected)
                                        <option value="{{ $idx }}">{{ $idx }}. {{ $option }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="modern-label">Right Option</label>
                            <select class="option-input" wire:model.live="correct_pairs.{{ $pairIdx }}.right" wire:key="right-select-{{ $pairIdx }}-{{ count($right_options) }}">
                                <option value="">Select Right Option</option>
                                @foreach($this->getFilteredRightOptions() as $idx => $option)
                                    @php
                                        $alreadySelected = false;
                                        foreach ($correct_pairs as $otherIdx => $otherPair) {
                                            if ($otherIdx !== $pairIdx && isset($otherPair['right']) && $otherPair['right'] !== '' && $otherPair['right'] == $idx) {
                                                $alreadySelected = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if(!$alreadySelected)
                                        <option value="{{ $idx }}">{{ $idx }}. {{ $option }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>                    </div>

                    <!-- Submit Buttons -->
                    <div class="section-block">
                        <div class="flex justify-end space-x-4">
                            <a href="/admin/questions" class="cancel-btn">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </a>
                            <button type="submit" class="submit-btn">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Question
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
    <style>
    /* Copy all the CSS styles from the create page with same styling */
    
    /* Custom styling for Filament header */
    .fi-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
        border-radius: 12px !important;
        margin-bottom: 1.5rem !important;
        padding: 1.25rem 1.5rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        border: 1px solid #e5e7eb !important;
        transition: all 0.3s ease !important;
    }

    .fi-header:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        transform: translateY(-1px) !important;
    }

    /* Style breadcrumbs */
    .fi-breadcrumbs {
        font-size: 0.875rem !important;
        color: #6b7280 !important;
        margin-bottom: 0.5rem !important;
    }

    .fi-breadcrumbs a {
        color: #3b82f6 !important;
        text-decoration: none !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
    }

    .fi-breadcrumbs a:hover {
        color: #1d4ed8 !important;
        text-decoration: underline !important;
    }

    .fi-breadcrumbs-separator {
        color: #9ca3af !important;
        margin: 0 0.5rem !important;
    }

    /* Style page heading */
    .fi-page-heading h1, .fi-heading {
        color: #1f2937 !important;
        font-size: 1.75rem !important;
        font-weight: 700 !important;
        margin: 0.5rem 0 0.25rem 0 !important;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6) !important;
        -webkit-background-clip: text !important;
        background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
    }

    /* Style subheading */
    .fi-page-heading p, .fi-subheading {
        color: #6b7280 !important;
        font-size: 0.9rem !important;
        margin: 0 !important;
        font-weight: 500 !important;
        line-height: 1.5 !important;
    }

    /* Override Filament default spacing */
    .fi-page {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .fi-page-content {
        padding: 1rem !important;
        margin: 0 !important;
        max-width: none !important;
    }

    .fi-main {
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Loading Spinner Styles */
    .loading-spinner {
        display: inline-block;
    }

    .loading-spinner svg {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* File Preservation Notice */
    .file-preservation-notice {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #3b82f6;
        border-radius: 8px;
        padding: 0.75rem;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #1e40af;
        font-weight: 500;
    }

    /* Modern Single Card Form Styling */
    .modern-question-form {
        max-width: 98vw;
        width: 100%;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Single Modern Card Design */
    .modern-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border: 1px solid #e5e7eb;
        position: relative;
        margin: 0;
        width: 100%;
    }

    .modern-card:hover {
        transform: translateY(-8px) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border-color: #3b82f6;
    }

    .card-content {
        padding: 2rem;
        position: relative;
    }

    /* Section Blocks */
    .section-block {
        padding: 1.5rem 0;
        border-bottom: 1px solid #f3f4f6;
        position: relative;
    }

    .section-block:first-child {
        padding-top: 0.5rem;
    }
    .section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #000000;  /* Change from #1f2937 to #000000 */
    margin-bottom: 1.5rem;
    position: relative;
    padding-left: 1rem;
    }

    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 100%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border-radius: 2px;
    }

    /* Modern Audio Player Styling */
    .modern-audio-player audio {
        width: 100%;
        height: 40px;
        border-radius: 8px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        outline: none;
        transition: all 0.3s ease;
    }

    .modern-audio-player audio:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Enhanced Audio Player */
    .modern-audio-player {
        padding: 0.75rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .modern-audio-player:hover {
        border-color: #3b82f6;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Image Preview Container */
    .image-preview-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0.5rem 0;
    }

    .image-preview-thumb {
        width: 100%;
        max-width: 140px;
        height: 120px;
        object-fit: cover;
        object-position: center;
        border-radius: 0.5rem;
        border: 2px solid #a78bfa; /* purple-400 */
        background: #f3f4f6;
        display: block;
        margin-left: auto;
        margin-right: auto;
        box-shadow: 0 2px 8px rgba(168,139,250,0.08);
        transition: all 0.3s ease;
    }

    .image-preview-thumb:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(168,139,250,0.2);
    }

    @media (max-width: 640px) {
        .image-preview-thumb {
            max-width: 100px;
            height: 80px;
        }
    }

    /* Audio Image Text Multiple Section Styling */
    .audio-image-pair-item {
        background: linear-gradient(145deg, #fef7ff 0%, #f3e8ff 100%);
        border: 2px dashed #a855f7;
        transition: all 0.3s ease;
    }

    .audio-image-pair-item:hover {
        border-color: #7c3aed;
        background: linear-gradient(145deg, #faf5ff 0%, #f3e8ff 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(168, 85, 247, 0.2);
    }

    .audio-image-multiple-match-item {
        animation: slideInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .audio-indicator {
        transition: all 0.3s ease;
    }

    .audio-indicator:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
    }

    /* Picture MCQ Section Styling */
    .picture-mcq-image-item {
        background: linear-gradient(145deg, #fef7ff 0%, #f3e8ff 100%);
        border: 2px dashed #a855f7;
        transition: all 0.3s ease;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
    }

    .picture-mcq-image-item:hover {
        border-color: #7c3aed;
        background: linear-gradient(145deg, #faf5ff 0%, #f3e8ff 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(168, 85, 247, 0.2);
    }

    /* Form Fill Section Styling */
    .form-fill-paragraph-section {
        background: linear-gradient(145deg, #fefce8 0%, #fef3c7 100%);
        border: 2px solid #f59e0b;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .form-fill-option-item, .answer-key-item {
        background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .form-fill-option-item:hover, .answer-key-item:hover {
        border-color: #3b82f6;
        background: white;
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .option-number, .answer-number {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .answer-number {
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        width: auto;
        height: auto;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .paragraph-info {
        display: flex;
        align-items: center;
        margin-top: 0.75rem;
        padding: 0.75rem;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid #10b981;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #065f46;
        font-weight: 500;
    }

    .filled-paragraph-main {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 3px solid #10b981;
        border-radius: 16px;
        padding: 2rem;
        font-size: 1.25rem;
        line-height: 1.8;
        color: #1f2937;
        margin-bottom: 1.5rem;
        font-weight: 600;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2), 0 4px 6px -2px rgba(16, 185, 129, 0.1);
        position: relative;
    }

    .filled-paragraph-main::before {
        content: '🎯';
        position: absolute;
        top: -10px;
        left: -10px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .preview-label-main {
        font-weight: 700;
        color: #065f46;
        margin-bottom: 1rem;
        font-size: 1rem;
        display: flex;
        align-items: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .empty-blank {
        background: linear-gradient(135deg, #fee2e2, #fca5a5);
        color: #dc2626;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
        display: inline-block;
        margin: 0 2px;
        text-decoration: line-through;
    }

    .filled-paragraph-preview {
        background: white;
        border: 2px solid #10b981;
        border-radius: 12px;
        padding: 1.5rem;
        font-size: 1rem;
        line-height: 1.6;
        color: #1f2937;
        margin-bottom: 1rem;
        font-weight: 500;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);
    }

    .filled-answer {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        display: inline-block;
        margin: 0 2px;
    }

    .paragraph-preview {
        background: white;
        border: 2px solid #f59e0b;
        border-radius: 12px;
        padding: 1.5rem;
        font-size: 1rem;
        line-height: 1.6;
        color: #1f2937;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .options-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .option-preview {
        background: white;
        border: 1px solid #3b82f6;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #1e40af;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin: 0.25rem;
        display: inline-block;
    }

    .no-options-message {
        color: #6b7280;
        font-style: italic;
        font-size: 0.875rem;
        padding: 1rem;
        text-align: center;
        border: 1px dashed #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
    }

    .answers-preview {
        space-y: 0.5rem;
    }

    .answer-key-preview {
        background: white;
        border: 1px solid #10b981;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #065f46;
        margin-bottom: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Reorder Section Styling */
    .reorder-fragment-item {
        background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .reorder-fragment-item:hover {
        border-color: #3b82f6;
        background: white;
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .fragment-number {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .answer-key-section {
        background: linear-gradient(145deg, #fefce8 0%, #fef3c7 100%);
        border: 2px solid #f59e0b;
        border-radius: 16px;
        padding: 1.5rem;
        position: relative;
    }

    .answer-key-info {
        display: flex;
        align-items: center;
        margin-top: 0.75rem;
        padding: 0.75rem;
        background: rgba(245, 158, 11, 0.1);
        border-radius: 8px;
        font-size: 0.875rem;
        color: #92400e;
        font-weight: 500;
    }

    .preview-section {
        background: linear-gradient(145deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid #0ea5e9;
        border-radius: 16px;
        padding: 1.5rem;
    }

    .preview-label {
        font-weight: 600;
        color: #0c4a6e;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
    }

    .fragments-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .fragment-preview {
        background: white;
        border: 1px solid #0ea5e9;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #0c4a6e;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin: 0.25rem;
        display: inline-block;
    }

    .no-fragments-message {
        color: #6b7280;
        font-style: italic;
        font-size: 0.875rem;
        padding: 1rem;
        text-align: center;
        border: 1px dashed #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
    }

    .answer-preview {
        background: white;
        border: 2px solid #10b981;
        border-radius: 8px;
        padding: 1rem;
        font-weight: 600;
        color: #064e3b;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Sub Question Styling */
    .sub-question-item, .true-false-item {
        background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .sub-question-item:hover, .true-false-item:hover {
        border-color: #3b82f6;
        background: white;
        transform: translateY(-4px) !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .sub-question-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #000000;  /* Change from #374151 to #000000 */
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    }

    /* True/False Options Styling */
    .true-false-options {
        margin-top: 0.5rem;
    }

    .true-false-option {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        position: relative;
        overflow: hidden;
        flex: 1;
    }

    .true-false-option:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .true-false-option.selected {
        border-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
    }

    .true-false-option.true-option.selected {
        border-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .true-false-option.false-option.selected {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
    }

    .option-circle {
        width: 2rem;
        height: 2rem;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .true-false-option.selected .option-circle {
        border-color: #10b981;
        background: #10b981;
    }

    .true-false-option.false-option.selected .option-circle {
        border-color: #ef4444;
        background: #ef4444;
    }

    .checkmark {
        color: white;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease;
    }

    .checkmark.show {
        opacity: 1;
        transform: scale(1);
    }

    .option-text {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        transition: color 0.3s ease;
    }

    .true-false-option.selected .option-text {
        color: #1f2937;
    }

    /* Modern Form Elements */
    .modern-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color:  #000000 !important;
        margin-bottom: 0.75rem;
        position: relative;
    }

    .modern-input, .modern-select, .modern-textarea {
    width: 100% !important;
    padding: 1rem 1.25rem !important;
    border: 2px solid #e5e7eb !important;
    border-radius: 12px !important;
    font-size: 0.875rem !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background: white !important;
    color: #000000 !important;  /* Change from #1f2937 to #000000 */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    }

    .modern-input:focus, .modern-select:focus, .modern-textarea:focus {
        outline: none !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-2px) !important;
    }

    .modern-input:hover, .modern-select:hover, .modern-textarea:hover {
        border-color: #6366f1 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-1px) !important;
    }

    .modern-checkbox-label {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .modern-checkbox-label:hover {
        background: #f3f4f6;
    }

    .modern-checkbox {
        width: 1.25rem !important;
        height: 1.25rem !important;
        border-radius: 0.375rem !important;
        border: 2px solid #d1d5db !important;
        background: white !important;
        transition: all 0.3s ease !important;
    }

    .modern-checkbox:checked {
        background: #3b82f6 !important;
        border-color: #3b82f6 !important;
    }

    /* Option and Index Items */
    .option-item, .index-item {
        background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .option-item::before, .index-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .option-item:hover, .index-item:hover {
        border-color: #3b82f6;
        background: white;
        transform: translateY(-4px) !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .option-item:hover::before, .index-item:hover::before {
        left: 100%;
    }

    .option-label {
    font-size: 0.875rem;
    font-weight: 700;
    color: #000000;  /* Change from #374151 to #000000 */
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    }

    .option-input, .index-input {
        width: 100% !important;
        padding: 0.875rem 1.125rem !important;
        border: 2px solid #e2e8f0 !important;
        border-radius: 10px !important;
        font-size: 0.875rem !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        background: white !important;
        margin-top: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    }

    .option-input:focus, .index-input:focus {
        outline: none !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-1px) !important;
    }

    /* Enhanced Button Styling */
    .button-group {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .add-btn, .add-index-btn {
        display: inline-flex !important;
        align-items: center !important;
        padding: 0.625rem 1rem !important;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4) !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .add-btn:hover, .add-index-btn:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: white !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4) !important;
    }

    .add-btn-small, .remove-btn-small {
        display: inline-flex !important;
        align-items: center !important;
        padding: 0.5rem !important;
        border: none !important;
        border-radius: 6px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .add-btn-small {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
        box-shadow: 0 2px 4px -1px rgba(16, 185, 129, 0.4) !important;
    }

    .add-btn-small:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4) !important;
    }

    .remove-btn-small {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
        box-shadow: 0 2px 4px -1px rgba(239, 68, 68, 0.4) !important;
    }

    .remove-btn-small:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.4) !important;
    }

    .remove-btn {
        display: inline-flex !important;
        align-items: center !important;
        padding: 0.625rem 1rem !important;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.4) !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .remove-btn:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        color: white !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.4) !important;
    }

    /* Clear Buttons for Picture MCQ */
    .clear-pair-btn {
        display: inline-flex !important;
        align-items: center !important;
        padding: 0.5rem !important;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 6px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        box-shadow: 0 2px 4px -1px rgba(245, 158, 11, 0.4) !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .clear-pair-btn:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.4) !important;
    }

    .clear-all-btn {
        display: inline-flex !important;
        align-items: center !important;
        padding: 0.625rem 1rem !important;
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.4) !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .clear-all-btn:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%) !important;
        color: white !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 15px -3px rgba(220, 38, 38, 0.4) !important;
    }

    /* Main Action Buttons */
    .submit-btn {
        display: inline-flex !important;
        align-items: center !important;
        padding: 1rem 2rem !important;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 12px !important;
        font-size: 1rem !important;
        font-weight: 700 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.4) !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .submit-btn:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
        color: white !important;
        transform: translateY(-3px) !important;
        box-shadow: 0 20px 25px -5px rgba(245, 158, 11, 0.4) !important;
    }

    .cancel-btn {
        display: inline-flex !important;
        align-items: center !important;
        padding: 1rem 2rem !important;
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 12px !important;
        font-size: 1rem !important;
        font-weight: 600 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        box-shadow: 0 4px 6px -1px rgba(107, 114, 128, 0.4) !important;
    }

    .cancel-btn:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%) !important;
        color: white !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 15px -3px rgba(107, 114, 128, 0.4) !important;
    }

    /* Info Banner */
    .info-banner {
        display: flex;
        align-items: center;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #f59e0b;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #92400e;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.1);
    }

    .info-banner-small {
        padding: 0.5rem 0.75rem;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 1px solid #3b82f6;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 500;
        color: #1e40af;
        margin-bottom: 0.75rem;
    }

    /* Error Text */
    .error-text {
        color: #ef4444 !important;
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        margin-top: 0.5rem !important;
        padding: 0.25rem 0.5rem !important;
        background: #fef2f2 !important;
        border-radius: 6px !important;
    }

    /* Audio Upload Section Styling */
    .audio-upload-section {
        background: linear-gradient(145deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid #0ea5e9;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .audio-upload-section:hover {
        border-color: #0284c7;
        background: linear-gradient(145deg, #e0f2fe 0%, #bae6fd 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -3px rgba(14, 165, 233, 0.2);
    }

    .audio-upload-section .modern-input {
        border-color: #0ea5e9 !important;
        background: white !important;
    }

    .audio-upload-section .modern-input:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1) !important;
    }

    /* Upload Label Styling */
    .upload-label {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    .upload-label:hover {
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.4);
    }

    .custom-upload-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .upload-section {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    /* Picture MCQ Match Item Styles */
    .picture-mcq-match-item {
        animation: slideInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Fixed responsive image sizing for preview */
    .preview-image {
        width: 120px !important;
        height: 120px !important;
        object-fit: cover !important;
        object-position: center !important;
    }

    /* Responsive Design */
    @media (min-width: 1200px) {
        .modern-question-form {
            max-width: 95vw;
            padding: 0 2rem;
        }
        
        .card-content {
            padding: 2.5rem;
        }
    }

    @media (max-width: 1024px) {
        .modern-question-form {
            max-width: 98vw;
            padding: 0 1rem;
        }
    }

    @media (max-width: 768px) {
        .modern-question-form {
            max-width: 100vw;
            padding: 0 0.5rem;
        }
        
        .card-content {
            padding: 1.5rem;
        }
        
        .grid-cols-1.md\\:grid-cols-2 {
            grid-template-columns: 1fr;
        }
        
        .button-group {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .section-block {
            padding: 1rem 0;
        }

        .true-false-option {
            padding: 0.75rem 1rem;
        }

        .option-text {
            font-size: 0.875rem;
        }

        .fragments-preview, .options-preview {
            flex-direction: column;
        }

        .true-false-options .flex {
            flex-direction: column;
            gap: 0.75rem;
        }

        .picture-mcq-image-item {
            margin-bottom: 1rem;
        }

        .picture-mcq-image-item img {
            width: 100%;
            max-width: 200px;
            height: auto;
        }

        .preview-image {
            width: 100px !important;
            height: 100px !important;
        }
    }

    @media (max-width: 480px) {
        .preview-image {
            width: 70px !important;
            height: 70px !important;
        }

        .clear-pair-btn, .clear-all-btn {
            font-size: 0.625rem !important;
            padding: 0.5rem 0.75rem !important;
        }

        .audio-upload-section {
            padding: 1rem;
        }

        .picture-mcq-image-item {
            margin-bottom: 1rem;
        }

        .picture-mcq-image-item img {
            width: 100%;
            max-width: 200px;
            height: auto;
        }

        .audio-image-pair-item {
            margin-bottom: 1rem;
            padding: 1rem;
        }
    }

    /* Animation for new items */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .option-item, .index-item, .sub-question-item, .true-false-item, .reorder-fragment-item, .form-fill-option-item, .answer-key-item, .picture-mcq-image-item, .audio-image-pair-item {
        animation: slideInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Enhanced Hover Effects for Interactive Elements */
    .modern-card .section-block:hover {
        background: rgba(59, 130, 246, 0.02);
        border-radius: 16px;
        transition: background 0.3s ease;
    }

    /* Focus States for Better Accessibility */
    .add-btn:focus, .remove-btn:focus, .clear-pair-btn:focus, .clear-all-btn:focus {
        outline: 2px solid #3b82f6 !important;
        outline-offset: 2px !important;
    }

    /* Success State Styles */
    .option-item.success {
        border-color: #10b981 !important;
        background: linear-gradient(145deg, #f0fdf4 0%, #dcfce7 100%) !important;
    }

    /* Error State Styles */
    .option-item.error {
        border-color: #ef4444 !important;
        background: linear-gradient(145deg, #fef2f2 0%, #fee2e2 100%) !important;
    }

    /* Enhanced Typography for Better Readability */
    .section-title, .sub-question-title {
        letter-spacing: 0.025em;
        line-height: 1.2;
    }

    /* Improved Form Validation Styling */
    .modern-input.error, .modern-select.error, .modern-textarea.error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
    }

    .modern-input.success, .modern-select.success, .modern-textarea.success {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
    }

    /* Enhanced Container Styling */
    .modern-question-form {
        position: relative;
    }

    .modern-question-form::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        z-index: -1;
        opacity: 0.5;
    }

    /* Print Styles */
    @media print {
        .modern-card {
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }
        
        .button-group {
            display: none !important;
        }
        
        .submit-btn, .cancel-btn {
            display: none !important;
        }
    }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-save functionality
            const inputs = document.querySelectorAll('.modern-input, .modern-textarea, .modern-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Add visual feedback for unsaved changes
                    this.classList.add('modified');
                    
                    // Remove modified class after a delay
                    setTimeout(() => {
                        this.classList.remove('modified');
                    }, 2000);
                });
            });

            // Enhanced form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let hasErrors = false;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('error');
                            hasErrors = true;
                        } else {
                            field.classList.remove('error');
                            field.classList.add('success');
                        }
                    });
                    
                    if (hasErrors) {
                        e.preventDefault();
                        // Scroll to first error
                        const firstError = form.querySelector('.error');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }
                    }
                });
            }

            // Dynamic preview updates
            const previewElements = document.querySelectorAll('[wire\\:model]');
            previewElements.forEach(element => {
                element.addEventListener('input', function() {
                    // Add loading state
                    const section = this.closest('.section-block');
                    if (section) {
                        section.classList.add('loading');
                        setTimeout(() => {
                            section.classList.remove('loading');
                        }, 500);
                    }
                });
            });

            // Smooth scrolling for section navigation
            const sectionTitles = document.querySelectorAll('.section-title');
            sectionTitles.forEach(title => {
                title.style.cursor = 'pointer';
                title.addEventListener('click', function() {
                    this.closest('.section-block').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+S to save (prevent default and submit form)
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const submitBtn = document.querySelector('.submit-btn');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
                
                // Escape to cancel
                if (e.key === 'Escape') {
                    const cancelBtn = document.querySelector('.cancel-btn');
                    if (cancelBtn) {
                        cancelBtn.click();
                    }
                }
            });

            // Add visual feedback for button interactions
            const buttons = document.querySelectorAll('.add-btn, .remove-btn, .clear-pair-btn, .clear-all-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Auto-resize textareas
            const textareas = document.querySelectorAll('.modern-textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
                
                // Initialize height on load
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });

            // File upload progress indication
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        // Remove existing progress indicators
                        const existingProgress = this.parentNode.querySelector('.upload-progress');
                        if (existingProgress) {
                            existingProgress.remove();
                        }
                        
                        // Create progress indicator
                        const progressDiv = document.createElement('div');
                        progressDiv.className = 'upload-progress';
                        progressDiv.innerHTML = `<div class="progress-bar"></div><span>${fileName}</span>`;
                        
                        // Insert after the input
                        this.parentNode.insertBefore(progressDiv, this.nextSibling);
                        
                        // Simulate upload progress
                        const progressBar = progressDiv.querySelector('.progress-bar');
                        let width = 0;
                        const interval = setInterval(() => {
                            width += 10;
                            progressBar.style.width = width + '%';
                            if (width >= 100) {
                                clearInterval(interval);
                                setTimeout(() => {
                                    progressDiv.remove();
                                }, 1000);
                            }
                        }, 100);
                    }
                });
            });

            // Enhanced audio player controls
            const audioPlayers = document.querySelectorAll('audio');
            audioPlayers.forEach(audio => {
                audio.addEventListener('loadstart', function() {
                    this.style.opacity = '0.7';
                });
                
                audio.addEventListener('canplay', function() {
                    this.style.opacity = '1';
                });
                
                audio.addEventListener('error', function() {
                    this.style.borderColor = '#ef4444';
                    this.style.background = '#fef2f2';
                });
            });

            // Form auto-save warning
            window.addEventListener('beforeunload', function(e) {
                const modifiedFields = document.querySelectorAll('.modified');
                if (modifiedFields.length > 0) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });
        });

        // Livewire event listeners
        document.addEventListener('livewire:load', function() {
            // Show loading state during Livewire requests
            Livewire.hook('message.sent', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '0.8';
                    form.style.pointerEvents = 'none';
                }
            });

            Livewire.hook('message.processed', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '1';
                    form.style.pointerEvents = 'auto';
                }
            });

            // Handle validation errors
            Livewire.on('validationError', function(message) {
                console.log('Validation Error:', message);
                
                // Scroll to first error field
                setTimeout(() => {
                    const firstError = document.querySelector('.error-text');
                    if (firstError) {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }
                }, 100);
            });

            // Handle successful updates
            Livewire.on('questionUpdated', function() {
                const card = document.querySelector('.modern-card');
                if (card) {
                    card.style.transform = 'scale(1.02)';
                    card.style.borderColor = '#10b981';
                    card.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
                    
                    setTimeout(() => {
                        card.style.transform = '';
                        card.style.borderColor = '';
                        card.style.boxShadow = '';
                    }, 1500);
                }
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        /* Upload Progress Styling */
        .upload-progress {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .progress-bar {
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            border-radius: 2px;
            transition: width 0.3s ease;
            margin-bottom: 0.25rem;
            width: 0%;
        }

        .upload-progress span {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Modified State Styling */
        .modern-input.modified, .modern-textarea.modified, .modern-select.modified {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2) !important;
        }

        /* Loading State */
        .modern-card.loading {
            position: relative;
        }

        .modern-card.loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            z-index: 1000;
        }

        /* Enhanced Accessibility */
        .add-btn:focus-visible, .remove-btn:focus-visible, .clear-pair-btn:focus-visible, .clear-all-btn:focus-visible {
            outline: 2px solid #3b82f6 !important;
            outline-offset: 2px !important;
        }

        /* High Contrast Mode Support */
        @media (prefers-contrast: high) {
            .modern-card {
                border: 2px solid #000 !important;
            }
            
            .section-title::before {
                background: #000 !important;
            }
            
            .add-btn, .remove-btn {
                border: 1px solid #000 !important;
            }
        }

        /* Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

       
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-save functionality
            const inputs = document.querySelectorAll('.modern-input, .modern-textarea, .modern-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Add visual feedback for unsaved changes
                    this.classList.add('modified');
                    
                    // Remove modified class after a delay
                    setTimeout(() => {
                        this.classList.remove('modified');
                    }, 2000);
                });
            });

            // Enhanced form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let hasErrors = false;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('error');
                            hasErrors = true;
                        } else {
                            field.classList.remove('error');
                            field.classList.add('success');
                        }
                    });
                    
                    if (hasErrors) {
                        e.preventDefault();
                        // Scroll to first error
                        const firstError = form.querySelector('.error');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }
                    }
                });
            }

            // Dynamic preview updates
            const previewElements = document.querySelectorAll('[wire\\:model]');
            previewElements.forEach(element => {
                element.addEventListener('input', function() {
                    // Add loading state
                    const section = this.closest('.section-block');
                    if (section) {
                        section.classList.add('loading');
                        setTimeout(() => {
                            section.classList.remove('loading');
                        }, 500);
                    }
                });
            });

            // Smooth scrolling for section navigation
            const sectionTitles = document.querySelectorAll('.section-title');
            sectionTitles.forEach(title => {
                title.style.cursor = 'pointer';
                title.addEventListener('click', function() {
                    this.closest('.section-block').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+S to save (prevent default and submit form)
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const submitBtn = document.querySelector('.submit-btn');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
                
                // Escape to cancel
                if (e.key === 'Escape') {
                    const cancelBtn = document.querySelector('.cancel-btn');
                    if (cancelBtn) {
                        cancelBtn.click();
                    }
                }
            });

            // Add visual feedback for button interactions
            const buttons = document.querySelectorAll('.add-btn, .remove-btn, .clear-pair-btn, .clear-all-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Auto-resize textareas
            const textareas = document.querySelectorAll('.modern-textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
                
                // Initialize height on load
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });

            // File upload progress indication
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        // Remove existing progress indicators
                        const existingProgress = this.parentNode.querySelector('.upload-progress');
                        if (existingProgress) {
                            existingProgress.remove();
                        }
                        
                        // Create progress indicator
                        const progressDiv = document.createElement('div');
                        progressDiv.className = 'upload-progress';
                        progressDiv.innerHTML = `<div class="progress-bar"></div><span>${fileName}</span>`;
                        
                        // Insert after the input
                        this.parentNode.insertBefore(progressDiv, this.nextSibling);
                        
                        // Simulate upload progress
                        const progressBar = progressDiv.querySelector('.progress-bar');
                        let width = 0;
                        const interval = setInterval(() => {
                            width += 10;
                            progressBar.style.width = width + '%';
                            if (width >= 100) {
                                clearInterval(interval);
                                setTimeout(() => {
                                    progressDiv.remove();
                                }, 1000);
                            }
                        }, 100);
                    }
                });
            });

            // Enhanced audio player controls
            const audioPlayers = document.querySelectorAll('audio');
            audioPlayers.forEach(audio => {
                audio.addEventListener('loadstart', function() {
                    this.style.opacity = '0.7';
                });
                
                audio.addEventListener('canplay', function() {
                    this.style.opacity = '1';
                });
                
                audio.addEventListener('error', function() {
                    this.style.borderColor = '#ef4444';
                    this.style.background = '#fef2f2';
                });
            });

            // Form auto-save warning
            window.addEventListener('beforeunload', function(e) {
                const modifiedFields = document.querySelectorAll('.modified');
                if (modifiedFields.length > 0) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });
        });

        // Livewire event listeners
        document.addEventListener('livewire:load', function() {
            // Show loading state during Livewire requests
            Livewire.hook('message.sent', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '0.8';
                    form.style.pointerEvents = 'none';
                }
            });

            Livewire.hook('message.processed', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '1';
                    form.style.pointerEvents = 'auto';
                }
            });

            // Handle validation errors
            Livewire.on('validationError', function(message) {
                console.log('Validation Error:', message);
                
                // Scroll to first error field
                setTimeout(() => {
                    const firstError = document.querySelector('.error-text');
                    if (firstError) {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }
                }, 100);
            });

            // Handle successful updates
            Livewire.on('questionUpdated', function() {
                const card = document.querySelector('.modern-card');
                if (card) {
                    card.style.transform = 'scale(1.02)';
                    card.style.borderColor = '#10b981';
                    card.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
                    
                    setTimeout(() => {
                        card.style.transform = '';
                        card.style.borderColor = '';
                        card.style.boxShadow = '';
                    }, 1500);
                }
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        /* Upload Progress Styling */
        .upload-progress {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .progress-bar {
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
            border-radius: 2px;
            transition: width 0.3s ease;
            margin-bottom: 0.25rem;
            width: 0%;
        }

        .upload-progress span {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Modified State Styling */
        .modern-input.modified, .modern-textarea.modified, .modern-select.modified {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2) !important;
        }

        /* Loading State */
        .modern-card.loading {
            position: relative;
        }

        .modern-card.loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            z-index: 1000;
        }

        /* Enhanced Accessibility */
        .add-btn:focus-visible, .remove-btn:focus-visible, .clear-pair-btn:focus-visible, .clear-all-btn:focus-visible {
            outline: 2px solid #3b82f6 !important;
            outline-offset: 2px !important;
        }

        /* High Contrast Mode Support */
        @media (prefers-contrast: high) {
            .modern-card {
                border: 2px solid #000 !important;
            }
            
            .section-title::before {
                background: #000 !important;
            }
            
            .add-btn, .remove-btn {
                border: 1px solid #000 !important;
            }
        }

        /* Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

       
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-save functionality
            const inputs = document.querySelectorAll('.modern-input, .modern-textarea, .modern-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Add visual feedback for unsaved changes
                    this.classList.add('modified');
                    
                    // Remove modified class after a delay
                    setTimeout(() => {
                        this.classList.remove('modified');
                    }, 2000);
                });
            });

            // Enhanced form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let hasErrors = false;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('error');
                            hasErrors = true;
                        } else {
                            field.classList.remove('error');
                            field.classList.add('success');
                        }
                    });
                    
                    if (hasErrors) {
                        e.preventDefault();
                        // Scroll to first error
                        const firstError = form.querySelector('.error');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }
                    }
                });
            }

            // Dynamic preview updates
            const previewElements = document.querySelectorAll('[wire\\:model]');
            previewElements.forEach(element => {
                element.addEventListener('input', function() {
                    // Add loading state
                    const section = this.closest('.section-block');
                    if (section) {
                        section.classList.add('loading');
                        setTimeout(() => {
                            section.classList.remove('loading');
                        }, 500);
                    }
                });
            });

            // Smooth scrolling for section navigation
            const sectionTitles = document.querySelectorAll('.section-title');
            sectionTitles.forEach(title => {
                title.style.cursor = 'pointer';
                title.addEventListener('click', function() {
                    this.closest('.section-block').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+S to save (prevent default and submit form)
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const submitBtn = document.querySelector('.submit-btn');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
                
                // Escape to cancel
                if (e.key === 'Escape') {
                    const cancelBtn = document.querySelector('.cancel-btn');
                    if (cancelBtn) {
                        cancelBtn.click();
                    }
                }
            });

            // Add visual feedback for button interactions
            const buttons = document.querySelectorAll('.add-btn, .remove-btn, .clear-pair-btn, .clear-all-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Auto-resize textareas
            const textareas = document.querySelectorAll('.modern-textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
                
                // Initialize height on load
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });

            // File upload progress indication
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        // Remove existing progress indicators
                        const existingProgress = this.parentNode.querySelector('.upload-progress');
                        if (existingProgress) {
                            existingProgress.remove();
                        }
                        
                        // Create progress indicator
                        const progressDiv = document.createElement('div');
                        progressDiv.className = 'upload-progress';
                        progressDiv.innerHTML = `<div class="progress-bar"></div><span>${fileName}</span>`;
                        
                        // Insert after the input
                        this.parentNode.insertBefore(progressDiv, this.nextSibling);
                        
                        // Simulate upload progress
                        const progressBar = progressDiv.querySelector('.progress-bar');
                        let width = 0;
                        const interval = setInterval(() => {
                            width += 10;
                            progressBar.style.width = width + '%';
                            if (width >= 100) {
                                clearInterval(interval);
                                setTimeout(() => {
                                    progressDiv.remove();
                                }, 1000);
                            }
                        }, 100);
                    }
                });
            });

            // Enhanced audio player controls
            const audioPlayers = document.querySelectorAll('audio');
            audioPlayers.forEach(audio => {
                audio.addEventListener('loadstart', function() {
                    this.style.opacity = '0.7';
                });
                
                audio.addEventListener('canplay', function() {
                    this.style.opacity = '1';
                });
                
                audio.addEventListener('error', function() {
                    this.style.borderColor = '#ef4444';
                    this.style.background = '#fef2f2';
                });
            });

            // Form auto-save warning
            window.addEventListener('beforeunload', function(e) {
                const modifiedFields = document.querySelectorAll('.modified');
                if (modifiedFields.length > 0) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });
        });

        // Livewire event listeners
        document.addEventListener('livewire:load', function() {
            // Show loading state during Livewire requests
            Livewire.hook('message.sent', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '0.8';
                    form.style.pointerEvents = 'none';
                }
            });

            Livewire.hook('message.processed', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '1';
                    form.style.pointerEvents = 'auto';
                }
            });

            // Handle validation errors
            Livewire.on('validationError', function(message) {
                console.log('Validation Error:', message);
                
                // Scroll to first error field
                setTimeout(() => {
                    const firstError = document.querySelector('.error-text');
                    if (firstError) {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }
                }, 100);
            });

            // Handle successful updates
            Livewire.on('questionUpdated', function() {
                const card = document.querySelector('.modern-card');
                if (card) {
                    card.style.transform = 'scale(1.02)';
                    card.style.borderColor = '#10b981';
                    card.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
                    
                    setTimeout(() => {
                        card.style.transform = '';
                        card.style.borderColor = '';
                        card.style.boxShadow = '';
                    }, 1500);
                }
            });
        });
    </script>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-save functionality
            const inputs = document.querySelectorAll('.modern-input, .modern-textarea, .modern-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Add visual feedback for unsaved changes
                    this.classList.add('modified');
                    
                    // Remove modified class after a delay
                    setTimeout(() => {
                        this.classList.remove('modified');
                    }, 2000);
                });
            });

            // Enhanced form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let hasErrors = false;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('error');
                            hasErrors = true;
                        } else {
                            field.classList.remove('error');
                            field.classList.add('success');
                        }
                    });
                    
                    if (hasErrors) {
                        e.preventDefault();
                        // Scroll to first error
                        const firstError = form.querySelector('.error');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }
                    }
                });
            }

            // Dynamic preview updates
            const previewElements = document.querySelectorAll('[wire\\:model]');
            previewElements.forEach(element => {
                element.addEventListener('input', function() {
                    // Add loading state
                    const section = this.closest('.section-block');
                    if (section) {
                        section.classList.add('loading');
                        setTimeout(() => {
                            section.classList.remove('loading');
                        }, 500);
                    }
                });
            });

            // Smooth scrolling for section navigation
            const sectionTitles = document.querySelectorAll('.section-title');
            sectionTitles.forEach(title => {
                title.style.cursor = 'pointer';
                title.addEventListener('click', function() {
                    this.closest('.section-block').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+S to save (prevent default and submit form)
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const submitBtn = document.querySelector('.submit-btn');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
                
                // Escape to cancel
                if (e.key === 'Escape') {
                    const cancelBtn = document.querySelector('.cancel-btn');
                    if (cancelBtn) {
                        cancelBtn.click();
                    }
                }
            });

            // Add visual feedback for button interactions
            const buttons = document.querySelectorAll('.add-btn, .remove-btn, .clear-pair-btn, .clear-all-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Auto-resize textareas
            const textareas = document.querySelectorAll('.modern-textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
                
                // Initialize height on load
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });

            // File upload progress indication
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        // Remove existing progress indicators
                        const existingProgress = this.parentNode.querySelector('.upload-progress');
                        if (existingProgress) {
                            existingProgress.remove();
                        }
                        
                        // Create progress indicator
                        const progressDiv = document.createElement('div');
                        progressDiv.className = 'upload-progress';
                        progressDiv.innerHTML = `<div class="progress-bar"></div><span>${fileName}</span>`;
                        
                        // Insert after the input
                        this.parentNode.insertBefore(progressDiv, this.nextSibling);
                        
                        // Simulate upload progress
                        const progressBar = progressDiv.querySelector('.progress-bar');
                        let width = 0;
                        const interval = setInterval(() => {
                            width += 10;
                            progressBar.style.width = width + '%';
                            if (width >= 100) {
                                clearInterval(interval);
                                setTimeout(() => {
                                    progressDiv.remove();
                                }, 1000);
                            }
                        }, 100);
                    }
                });
            });

            // Enhanced audio player controls
            const audioPlayers = document.querySelectorAll('audio');
            audioPlayers.forEach(audio => {
                audio.addEventListener('loadstart', function() {
                    this.style.opacity = '0.7';
                });
                
                audio.addEventListener('canplay', function() {
                    this.style.opacity = '1';
                });
                
                audio.addEventListener('error', function() {
                    this.style.borderColor = '#ef4444';
                    this.style.background = '#fef2f2';
                });
            });

            // Form auto-save warning
            window.addEventListener('beforeunload', function(e) {
                const modifiedFields = document.querySelectorAll('.modified');
                if (modifiedFields.length > 0) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });
        });

        // Livewire event listeners
        document.addEventListener('livewire:load', function() {
            // Show loading state during Livewire requests
            Livewire.hook('message.sent', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '0.8';
                    form.style.pointerEvents = 'none';
                }
            });

            Livewire.hook('message.processed', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '1';
                    form.style.pointerEvents = 'auto';
                }
            });

            // Handle validation errors
            Livewire.on('validationError', function(message) {
                console.log('Validation Error:', message);
                
                // Scroll to first error field
                setTimeout(() => {
                    const firstError = document.querySelector('.error-text');
                    if (firstError) {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }
                }, 100);
            });

            // Handle successful updates
            Livewire.on('questionUpdated', function() {
                const card = document.querySelector('.modern-card');
                if (card) {
                    card.style.transform = 'scale(1.02)';
                    card.style.borderColor = '#10b981';
                    card.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
                    
                    setTimeout(() => {
                        card.style.transform = '';
                        card.style.borderColor = '';
                        card.style.boxShadow = '';
                    }, 1500);
                }
            });
        });
    </script>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-save functionality
            const inputs = document.querySelectorAll('.modern-input, .modern-textarea, .modern-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Add visual feedback for unsaved changes
                    this.classList.add('modified');
                    
                    // Remove modified class after a delay
                    setTimeout(() => {
                        this.classList.remove('modified');
                    }, 2000);
                });
            });

            // Enhanced form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let hasErrors = false;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('error');
                            hasErrors = true;
                        } else {
                            field.classList.remove('error');
                            field.classList.add('success');
                        }
                    });
                    
                    if (hasErrors) {
                        e.preventDefault();
                        // Scroll to first error
                        const firstError = form.querySelector('.error');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }
                    }
                });
            }

            // Dynamic preview updates
            const previewElements = document.querySelectorAll('[wire\\:model]');
            previewElements.forEach(element => {
                element.addEventListener('input', function() {
                    // Add loading state
                    const section = this.closest('.section-block');
                    if (section) {
                        section.classList.add('loading');
                        setTimeout(() => {
                            section.classList.remove('loading');
                        }, 500);
                    }
                });
            });

            // Smooth scrolling for section navigation
            const sectionTitles = document.querySelectorAll('.section-title');
            sectionTitles.forEach(title => {
                title.style.cursor = 'pointer';
                title.addEventListener('click', function() {
                    this.closest('.section-block').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+S to save (prevent default and submit form)
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const submitBtn = document.querySelector('.submit-btn');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
                
                // Escape to cancel
                if (e.key === 'Escape') {
                    const cancelBtn = document.querySelector('.cancel-btn');
                    if (cancelBtn) {
                        cancelBtn.click();
                    }
                }
            });

            // Add visual feedback for button interactions
            const buttons = document.querySelectorAll('.add-btn, .remove-btn, .clear-pair-btn, .clear-all-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Auto-resize textareas
            const textareas = document.querySelectorAll('.modern-textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
                
                // Initialize height on load
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });

            // File upload progress indication
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        // Remove existing progress indicators
                        const existingProgress = this.parentNode.querySelector('.upload-progress');
                        if (existingProgress) {
                            existingProgress.remove();
                        }
                        
                        // Create progress indicator
                        const progressDiv = document.createElement('div');
                        progressDiv.className = 'upload-progress';
                        progressDiv.innerHTML = `<div class="progress-bar"></div><span>${fileName}</span>`;
                        
                        // Insert after the input
                        this.parentNode.insertBefore(progressDiv, this.nextSibling);
                        
                        // Simulate upload progress
                        const progressBar = progressDiv.querySelector('.progress-bar');
                        let width = 0;
                        const interval = setInterval(() => {
                            width += 10;
                            progressBar.style.width = width + '%';
                            if (width >= 100) {
                                clearInterval(interval);
                                setTimeout(() => {
                                    progressDiv.remove();
                                }, 1000);
                            }
                        }, 100);
                    }
                });
            });

            // Enhanced audio player controls
            const audioPlayers = document.querySelectorAll('audio');
            audioPlayers.forEach(audio => {
                audio.addEventListener('loadstart', function() {
                    this.style.opacity = '0.7';
                });
                
                audio.addEventListener('canplay', function() {
                    this.style.opacity = '1';
                });
                
                audio.addEventListener('error', function() {
                    this.style.borderColor = '#ef4444';
                    this.style.background = '#fef2f2';
                });
            });

            // Form auto-save warning
            window.addEventListener('beforeunload', function(e) {
                const modifiedFields = document.querySelectorAll('.modified');
                if (modifiedFields.length > 0) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });
        });

        // Livewire event listeners
        document.addEventListener('livewire:load', function() {
            // Show loading state during Livewire requests
            Livewire.hook('message.sent', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '0.8';
                    form.style.pointerEvents = 'none';
                }
            });

            Livewire.hook('message.processed', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '1';
                    form.style.pointerEvents = 'auto';
                }
            });

            // Handle validation errors
            Livewire.on('validationError', function(message) {
                console.log('Validation Error:', message);
                
                // Scroll to first error field
                setTimeout(() => {
                    const firstError = document.querySelector('.error-text');
                    if (firstError) {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }
                }, 100);
            });

            // Handle successful updates
            Livewire.on('questionUpdated', function() {
                const card = document.querySelector('.modern-card');
                if (card) {
                    card.style.transform = 'scale(1.02)';
                    card.style.borderColor = '#10b981';
                    card.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
                    
                    setTimeout(() => {
                        card.style.transform = '';
                        card.style.borderColor = '';
                        card.style.boxShadow = '';
                    }, 1500);
                }
            });
        });
    </script>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-save functionality
            const inputs = document.querySelectorAll('.modern-input, .modern-textarea, .modern-select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Add visual feedback for unsaved changes
                    this.classList.add('modified');
                    
                    // Remove modified class after a delay
                    setTimeout(() => {
                        this.classList.remove('modified');
                    }, 2000);
                });
            });

            // Enhanced form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let hasErrors = false;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('error');
                            hasErrors = true;
                        } else {
                            field.classList.remove('error');
                            field.classList.add('success');
                        }
                    });
                    
                    if (hasErrors) {
                        e.preventDefault();
                        // Scroll to first error
                        const firstError = form.querySelector('.error');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }
                    }
                });
            }

            // Dynamic preview updates
            const previewElements = document.querySelectorAll('[wire\\:model]');
            previewElements.forEach(element => {
                element.addEventListener('input', function() {
                    // Add loading state
                    const section = this.closest('.section-block');
                    if (section) {
                        section.classList.add('loading');
                        setTimeout(() => {
                            section.classList.remove('loading');
                        }, 500);
                    }
                });
            });

            // Smooth scrolling for section navigation
            const sectionTitles = document.querySelectorAll('.section-title');
            sectionTitles.forEach(title => {
                title.style.cursor = 'pointer';
                title.addEventListener('click', function() {
                    this.closest('.section-block').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+S to save (prevent default and submit form)
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    const submitBtn = document.querySelector('.submit-btn');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
                
                // Escape to cancel
                if (e.key === 'Escape') {
                    const cancelBtn = document.querySelector('.cancel-btn');
                    if (cancelBtn) {
                        cancelBtn.click();
                    }
                }
            });

            // Add visual feedback for button interactions
            const buttons = document.querySelectorAll('.add-btn, .remove-btn, .clear-pair-btn, .clear-all-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Auto-resize textareas
            const textareas = document.querySelectorAll('.modern-textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
                
                // Initialize height on load
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });

            // File upload progress indication
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        // Remove existing progress indicators
                        const existingProgress = this.parentNode.querySelector('.upload-progress');
                        if (existingProgress) {
                            existingProgress.remove();
                        }
                        
                        // Create progress indicator
                        const progressDiv = document.createElement('div');
                        progressDiv.className = 'upload-progress';
                        progressDiv.innerHTML = `<div class="progress-bar"></div><span>${fileName}</span>`;
                        
                        // Insert after the input
                        this.parentNode.insertBefore(progressDiv, this.nextSibling);
                        
                        // Simulate upload progress
                        const progressBar = progressDiv.querySelector('.progress-bar');
                        let width = 0;
                        const interval = setInterval(() => {
                            width += 10;
                            progressBar.style.width = width + '%';
                            if (width >= 100) {
                                clearInterval(interval);
                                setTimeout(() => {
                                    progressDiv.remove();
                                }, 1000);
                            }
                        }, 100);
                    }
                });
            });

            // Enhanced audio player controls
            const audioPlayers = document.querySelectorAll('audio');
            audioPlayers.forEach(audio => {
                audio.addEventListener('loadstart', function() {
                    this.style.opacity = '0.7';
                });
                
                audio.addEventListener('canplay', function() {
                    this.style.opacity = '1';
                });
                
                audio.addEventListener('error', function() {
                    this.style.borderColor = '#ef4444';
                    this.style.background = '#fef2f2';
                });
            });

            // Form auto-save warning
            window.addEventListener('beforeunload', function(e) {
                const modifiedFields = document.querySelectorAll('.modified');
                if (modifiedFields.length > 0) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });
        });

        // Livewire event listeners
        document.addEventListener('livewire:load', function() {
            // Show loading state during Livewire requests
            Livewire.hook('message.sent', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '0.8';
                    form.style.pointerEvents = 'none';
                }
            });

            Livewire.hook('message.processed', (message, component) => {
                const form = document.querySelector('.modern-card');
                if (form) {
                    form.style.opacity = '1';
                    form.style.pointerEvents = 'auto';
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>