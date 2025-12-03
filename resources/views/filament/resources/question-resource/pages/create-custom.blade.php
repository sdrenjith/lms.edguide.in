<x-filament-panels::page>
    <div class="modern-question-form">
        <form wire:submit="create">
            @csrf
            
            <!-- Single Full-Width Card -->
            <div class="modern-card">
                <div class="card-content">
                    <!-- Question Details Section -->
                    <div class="section-block">
                        <h3 class="section-title">Question Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="modern-label">Day Number *</label>
                                <input type="number" wire:model="number_input" min="1" placeholder="1" class="modern-input">
                                @error('number_input') <p class="error-text">{{ $message }}</p> @enderror
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
                                    <option value="mcq_single">MCQ single answer</option>
                                    <option value="mcq_multiple">MCQ multiple answer</option>
                                    <option value="reorder">Rearrange options</option>
                                    <option value="opinion">Essay/para writing</option>
                                    <option value="statement_match">Match the following</option>
                                    <option value="true_false">True or false- single question</option>
                                    <option value="true_false_multiple">True or false multiple questions</option>
                                    <option value="form_fill">Fill in the blanks</option>
                                    <option value="audio_mcq_single">Audio with MCQ</option>
                                    <option value="audio_image_text_single">Audio with image matching</option>
                                    <option value="audio_image_text_multiple">Multiple audio text matching</option>
                                    <option value="picture_mcq">Image to text matching</option>
                                    <option value="audio_fill_blank">Audio fill in the blanks</option>
                                    <option value="picture_fill_blank">Picture fill in the blanks</option>
                                    <option value="video_fill_blank">Video fill in the blanks</option>
                                    <option value="audio_picture_match">Audio + image matching</option>
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
                                <input type="file" wire:model="explanation_file" class="modern-input" accept="*/*">
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
                                Upload one audio file as context/hint, add images on the left, text options on the right, then match images to correct text options. Students will listen to the audio and match accordingly.
                            </div>

                            <!-- Audio Upload Section -->
                            <div class="mb-6">
                                <h4 class="sub-question-title mb-4">🎵 Context Audio File</h4>
                                <div class="audio-upload-section">
                                    <label class="modern-label">Upload Audio File (Context/Hint) *</label>
                                    <input type="file" wire:model="audio_image_text_audio_file" class="modern-input" accept="audio/*" placeholder="Upload audio file">
                                    @error('audio_image_text_audio_file') <p class="error-text">{{ $message }}</p> @enderror
                                    
                                    @if($audio_image_text_audio_file ?? null)
                                        <div class="mt-3 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                </svg>
                                                <div>
                                                    <p class="font-semibold text-green-800">{{ $audio_image_text_audio_file->getClientOriginalName() }}</p>
                                                    <p class="text-sm text-green-600">Audio file uploaded successfully</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-2 text-sm text-gray-500">
                                        <strong>Supported formats:</strong> MP3, WAV, OGG, M4A (Max size: 10MB)<br>
                                        <strong>Purpose:</strong> This audio provides context or hints to help students match images to text options.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Left Side - Images -->
                                <div>
                                    <h5 class="font-semibold mb-4 text-lg">📷 Images to Match</h5>
                                    <div id="audio-image-text-images-container">
                                        @if(is_array($audio_image_text_image_uploads ?? []) && count($audio_image_text_image_uploads) > 0)
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
                                                    <input type="file" wire:model="audio_image_text_image_uploads.{{ $idx }}" class="modern-input" accept="image/*" placeholder="Upload image">
                                                    @error("audio_image_text_image_uploads.{$idx}") <p class="error-text">{{ $message }}</p> @enderror
                                                    
                                                    @if(isset($audio_image_text_image_uploads[$idx]) && $audio_image_text_image_uploads[$idx])
                                                        <div class="mt-2">
                                                            <img src="{{ $audio_image_text_image_uploads[$idx]->temporaryUrl() }}" alt="Preview" class="preview-image object-cover rounded border" style="width: 120px; height: 120px; max-width: 100%; max-height: 100%;">
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="picture-mcq-image-item flex flex-col mb-4 p-4 border-2 border-dashed border-purple-300 rounded-lg">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="font-medium text-gray-700">Image 1</span>
                                                </div>
                                                <input type="file" wire:model="audio_image_text_image_uploads.0" class="modern-input" accept="image/*" placeholder="Upload image">
                                                @error("audio_image_text_image_uploads.0") <p class="error-text">{{ $message }}</p> @enderror
                                            </div>
                                        @endif
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
                                        @if(is_array($audio_image_text_right_options ?? []) && count($audio_image_text_right_options) > 0)
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
                                        @else
                                            <div class="option-item flex items-center mb-2">
                                                <input type="text" wire:model.live="audio_image_text_right_options.0" class="option-input flex-1 mr-2" placeholder="Enter text option 1">
                                                <button type="button" wire:click="addAudioImageTextRightOption" class="add-btn-small">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                            </div>
                                            @error("audio_image_text_right_options.0") <p class="error-text">{{ $message }}</p> @enderror
                                        @endif
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
                                        <button type="button" wire:click="clearAllAudioImageTextPairs" class="clear-all-btn">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Clear All Pairs
                                        </button>
                                    </div>
                                </div>
                                <div class="info-banner-small">
                                    <span class="text-sm">Image indices: 0 = first image, 1 = second image, etc. Text indices: 0 = first text option, 1 = second text option, etc.</span>
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
                                                    <select class="option-input" wire:model.live="audio_image_text_correct_pairs.{{ $pairIdx }}.left" wire:key="audio-image-text-left-select-{{ $pairIdx }}-{{ count($audio_image_text_image_uploads ?? []) }}">
                                                        <option value="">Select Image</option>
                                                        @foreach($this->getFilteredAudioImageTextImages() as $idx => $image)
                                                            @php
                                                                $alreadySelected = false;
                                                                $pairs = $audio_image_text_correct_pairs ?? [];
                                                                foreach ($pairs as $otherIdx => $otherPair) {
                                                                    if ($otherIdx !== $pairIdx && isset($otherPair['left']) && $otherPair['left'] !== '' && $otherPair['left'] == $idx) {
                                                                        $alreadySelected = true;
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp
                                                            @if(!$alreadySelected)
                                                                <option value="{{ $idx }}">{{ $idx }}. Image {{ $idx + 1 }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex-1">
                                                    <label class="modern-label">Text Option</label>
                                                    <select class="option-input" wire:model.live="audio_image_text_correct_pairs.{{ $pairIdx }}.right" wire:key="audio-image-text-right-select-{{ $pairIdx }}-{{ count($audio_image_text_right_options ?? []) }}">
                                                        <option value="">Select Text Option</option>
                                                        @foreach($this->getFilteredAudioImageTextRightOptions() as $idx => $option)
                                                            @php
                                                                $alreadySelected = false;
                                                                $pairs = $audio_image_text_correct_pairs ?? [];
                                                                foreach ($pairs as $otherIdx => $otherPair) {
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

                            <!-- Preview Section for Audio Image Text -->
                            <div class="mb-6" wire:key="audio-image-text-preview-{{ count($audio_image_text_image_uploads) }}-{{ count($audio_image_text_right_options) }}">
                                <h4 class="sub-question-title mb-4">Preview</h4>
                                <div class="preview-section">
                                    @if($audio_image_text_audio_file ?? null)
                                        <div class="mb-4 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                </svg>
                                                <div>
                                                    <p class="font-semibold text-blue-800">🎵 Context Audio: {{ $audio_image_text_audio_file->getClientOriginalName() }}</p>
                                                    <p class="text-sm text-blue-600">Students will listen to this audio before matching</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @php
                                        $validPairs = array_filter($audio_image_text_correct_pairs ?? [], function($pair) {
                                            return isset($pair['left'], $pair['right']) && 
                                                   $pair['left'] !== '' && $pair['right'] !== '' &&
                                                   $pair['left'] !== null && $pair['right'] !== null;
                                        });
                                        $imageUploads = $audio_image_text_image_uploads ?? [];
                                        $rightOptions = $audio_image_text_right_options ?? [];
                                    @endphp
                                    
                                    @if(count($validPairs) > 0)
                                        <p class="preview-label mb-4">💡 Image-Text Matching Preview:</p>
                                        <div class="space-y-6">
                                            @foreach($validPairs as $index => $pair)
                                                @php
                                                    $imageIndex = (int)$pair['left'];
                                                    $textIndex = (int)$pair['right'];
                                                    $imageUpload = $imageUploads[$imageIndex] ?? null;
                                                    $textOption = $rightOptions[$textIndex] ?? '';
                                                @endphp
                                                
                                                @if($imageUpload && trim($textOption) !== '')
                                                    <div class="picture-mcq-match-item">
                                                        <div class="flex items-center justify-center space-x-8 p-6 bg-white border-2 border-green-200 rounded-xl">
                                                            <!-- Image Section -->
                                                            <div class="flex flex-col items-center space-y-2">
                                                                <div class="image-container">
                                                                    <img src="{{ $imageUpload->temporaryUrl() }}" 
                                                                         alt="Image {{ $imageIndex + 1 }}" 
                                                                         class="preview-image object-cover rounded-lg border-2 border-blue-300">
                                                                </div>
                                                                <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">Image {{ $imageIndex + 1 }}</span>
                                                            </div>
                                                            
                                                            <!-- Arrow -->
                                                            <div class="flex items-center px-4">
                                                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                                </svg>
                                                            </div>
                                                            
                                                            <!-- Text Section -->
                                                            <div class="flex flex-col items-center space-y-2">
                                                                <div class="text-container bg-blue-50 border-2 border-blue-300 px-6 py-3 rounded-lg">
                                                                    <span class="font-bold text-blue-900 text-lg">{{ trim($textOption) }}</span>
                                                                </div>
                                                                <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">Text Option</span>
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 font-medium">Upload audio, add images & text options, and set answer pairs to see preview</p>
                                        </div>
                                    @endif
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
                                Create multiple audio pairs (optionally with images) on the left side and text options on the right. Students will listen to audio files (with optional images) and match them to the correct text descriptions. Images are optional - you can upload just audio files.
                            </div>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Left Side - Audio + Optional Image Pairs -->
                                <div>
                                    <h5 class="font-semibold mb-4 text-lg">🎭 Audio + Optional Image Pairs</h5>
                                    <div id="audio-image-text-multiple-pairs-container">
                                        @php
                                            $maxPairs = max(count($audio_files ?? []), count($image_files ?? []), 1);
                                        @endphp
                                        
                                        @for($idx = 0; $idx < $maxPairs; $idx++)
                                            <div class="audio-image-pair-item flex flex-col mb-6 p-4 border-2 border-dashed border-indigo-300 rounded-lg bg-gradient-to-br from-indigo-50 to-purple-50" wire:key="audio_image_pair_{{ $idx }}">
                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="font-bold text-indigo-700">📱 Pair {{ $idx + 1 }}</span>
                                                    @if($idx > 0)
                                                        <button type="button" wire:click="removeAudioImageTextMultiplePair({{ $idx }})" class="remove-btn-small">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    @endif
                                                </div>
                                                
                                                <!-- Audio Upload (Simplified with separate arrays) -->
                                                <div class="mb-3">
                                                    <label class="modern-label text-sm">🎵 Audio File *</label>
                                                    <input type="file" 
                                                           wire:model.defer="audio_files.{{ $idx }}" 
                                                           class="modern-input" 
                                                           accept="audio/*">
                                                    
                                                    @if(isset($audio_files[$idx]) && $audio_files[$idx])
                                                        <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded">
                                                            <div class="flex items-center space-x-2">
                                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                                </svg>
                                                                <span class="text-sm font-medium text-green-700">
                                                                    @if(is_string($audio_files[$idx]))
                                                                        Audio uploaded
                                                                    @else
                                                                        {{ $audio_files[$idx]->getClientOriginalName() }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @error("audio_files.{$idx}") 
                                                        <p class="error-text">{{ $message }}</p> 
                                                    @enderror
                                                </div>
                                                
                                                <!-- Image Upload (Simplified with separate arrays) -->
                                                <div class="mb-2">
                                                    <label class="modern-label text-sm">🖼️ Image File (Optional)</label>
                                                    <input type="file" 
                                                           wire:model.defer="image_files.{{ $idx }}" 
                                                           class="modern-input" 
                                                           accept="image/*">
                                                    
                                                    @if(isset($image_files[$idx]) && $image_files[$idx])
                                                        <div class="mt-2">
                                                            @if(is_string($image_files[$idx]))
                                                                <p class="text-sm text-green-600">Image uploaded</p>
                                                            @else
                                                                <img src="{{ $image_files[$idx]->temporaryUrl() }}" alt="Preview" class="w-20 h-20 object-cover rounded border-2 border-indigo-200">
                                                            @endif
                                                        </div>
                                                    @endif
                                                    
                                                    @error("image_files.{$idx}") 
                                                        <p class="error-text">{{ $message }}</p> 
                                                    @enderror
                                                </div>
                                                
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Audio: MP3, WAV, OGG, M4A, AAC (Max: 25MB)<br>
                                                    Image: JPG, PNG, GIF (Max: 2MB, Optional)
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                    <button type="button" wire:click="addAudioImageTextMultiplePair" class="add-btn mt-2">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Audio+Image Pair
                                    </button>
                                </div>
                                
                                <!-- Right Side - Text Options -->
                                <div>
                                    <h5 class="font-semibold mb-4 text-lg">📝 Text Options</h5>
                                    <div id="audio-image-text-multiple-right-options-container">
                                        @if(is_array($audio_image_text_multiple_right_options ?? []) && count($audio_image_text_multiple_right_options) > 0)
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
                                        @else
                                            <div class="option-item flex items-center mb-2">
                                                <input type="text" wire:model.live="audio_image_text_multiple_right_options.0" class="option-input flex-1 mr-2" placeholder="Enter text option 1">
                                                <button type="button" wire:click="addAudioImageTextMultipleRightOption" class="add-btn-small">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                            </div>
                                            @error("audio_image_text_multiple_right_options.0") <p class="error-text">{{ $message }}</p> @enderror
                                        @endif
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
                                        <button type="button" wire:click="clearAllAudioImageTextMultiplePairs" class="clear-all-btn">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Clear All Pairs
                                        </button>
                                    </div>
                                </div>
                                <div class="info-banner-small">
                                    <span class="text-sm">Pair indices: 0 = first audio+image pair, 1 = second pair, etc. Text indices: 0 = first text option, 1 = second text option, etc.</span>
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
                                                    <label class="modern-label">Audio+Image Pair</label>
                                                    <select class="option-input" wire:model.live="audio_image_text_multiple_correct_pairs.{{ $pairIdx }}.left" wire:key="audio-image-text-multiple-left-select-{{ $pairIdx }}-{{ count($audio_image_text_multiple_pairs ?? []) }}">
                                                        <option value="">Select Pair</option>
                                                        @foreach($this->getFilteredAudioImageTextMultiplePairs() as $idx => $pair)
                                                            @php
                                                                $alreadySelected = false;
                                                                $pairs = $audio_image_text_multiple_correct_pairs ?? [];
                                                                foreach ($pairs as $otherIdx => $correctPair) {
                                                                    if ($otherIdx !== $pairIdx && isset($correctPair['left']) && $correctPair['left'] !== '' && $correctPair['left'] == $idx) {
                                                                        $alreadySelected = true;
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp
                                                            @if(!$alreadySelected)
                                                                <option value="{{ $idx }}">{{ $idx }}. Pair {{ $idx + 1 }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex-1">
                                                    <label class="modern-label">Text Option</label>
                                                    <select class="option-input" wire:model.live="audio_image_text_multiple_correct_pairs.{{ $pairIdx }}.right" wire:key="audio-image-text-multiple-right-select-{{ $pairIdx }}-{{ count($audio_image_text_multiple_right_options ?? []) }}">
                                                        <option value="">Select Text Option</option>
                                                        @foreach($this->getFilteredAudioImageTextMultipleRightOptions() as $idx => $option)
                                                            @php
                                                                $alreadySelected = false;
                                                                $pairs = $audio_image_text_multiple_correct_pairs ?? [];
                                                                foreach ($pairs as $otherIdx => $correctPair) {
                                                                    if ($otherIdx !== $pairIdx && isset($correctPair['right']) && $correctPair['right'] !== '' && $correctPair['right'] == $idx) {
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

                            <!-- Preview Section for Audio Image Text Multiple -->
                            <div class="mb-6" wire:key="audio-image-text-multiple-preview-{{ count($audio_files ?? []) }}-{{ count($audio_image_text_multiple_right_options ?? []) }}">
                                <h4 class="sub-question-title mb-4">Preview</h4>
                                <div class="preview-section">
                                    @php
                                        $validPairs = array_filter($audio_image_text_multiple_correct_pairs ?? [], function($pair) {
                                            return isset($pair['left'], $pair['right']) && 
                                                   $pair['left'] !== '' && $pair['right'] !== '' &&
                                                   $pair['left'] !== null && $pair['right'] !== null;
                                        });
                                        $audioFiles = $audio_files ?? [];
                                        $imageFiles = $image_files ?? [];
                                        $multipleRightOptions = $audio_image_text_multiple_right_options ?? [];
                                    @endphp
                                    
                                    @if(count($validPairs) > 0)
                                        <p class="preview-label mb-4">💡 Audio+Image to Text Matching Preview:</p>
                                        <div class="space-y-6">
                                            @foreach($validPairs as $index => $pair)
                                                @php
                                                    $pairIndex = (int)$pair['left'];
                                                    $textIndex = (int)$pair['right'];
                                                    $audioFile = $audioFiles[$pairIndex] ?? null;
                                                    $imageFile = $imageFiles[$pairIndex] ?? null;
                                                    $textOption = $multipleRightOptions[$textIndex] ?? '';
                                                @endphp
                                                
                                                @if($audioFile && trim($textOption) !== '')
                                                    <div class="audio-image-multiple-match-item">
                                                        <div class="flex items-center justify-center space-x-8 p-6 bg-white border-2 border-green-200 rounded-xl">
                                                            <!-- Audio + Optional Image Section -->
                                                            <div class="flex flex-col items-center space-y-3">
                                                                @if($imageFile)
                                                                    <div class="image-container">
                                                                        <img src="{{ $imageFile->temporaryUrl() }}" 
                                                                             alt="Image {{ $pairIndex + 1 }}" 
                                                                             class="preview-image object-cover rounded-lg border-2 border-indigo-300">
                                                                    </div>
                                                                @endif
                                                                @if($audioFile)
                                                                    <div class="audio-indicator bg-indigo-100 border-2 border-indigo-300 px-3 py-2 rounded-lg">
                                                                        <div class="flex items-center space-x-2">
                                                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                                            </svg>
                                                                            <span class="text-sm font-medium text-indigo-700">🎵 {{ $audioFile->getClientOriginalName() }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">Pair {{ $pairIndex + 1 }}</span>
                                                            </div>
                                                            
                                                            <!-- Arrow -->
                                                            <div class="flex items-center px-4">
                                                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                                </svg>
                                                            </div>
                                                            
                                                            <!-- Text Section -->
                                                            <div class="flex flex-col items-center space-y-2">
                                                                <div class="text-container bg-blue-50 border-2 border-blue-300 px-6 py-3 rounded-lg">
                                                                    <span class="font-bold text-blue-900 text-lg">{{ trim($textOption) }}</span>
                                                                </div>
                                                                <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">Text Option</span>
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 font-medium">Upload audio files (with optional images), add text options, and set answer pairs to see preview</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

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
                                    <label class="modern-label">Upload Audio File *</label>
                                    <input type="file" wire:model="audio_mcq_file" class="modern-input" accept="audio/*" placeholder="Upload audio file">
                                    @error('audio_mcq_file') <p class="error-text">{{ $message }}</p> @enderror
                                    
                                    @if($audio_mcq_file ?? null)
                                        <div class="mt-3 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                </svg>
                                                <div>
                                                    <p class="font-semibold text-green-800">{{ $audio_mcq_file->getClientOriginalName() }}</p>
                                                    <p class="text-sm text-green-600">Audio file uploaded successfully</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-2 text-sm text-gray-500">
                                        <strong>Supported formats:</strong> MP3, WAV, OGG, M4A (Max size: 10MB)<br>
                                        <strong>Tip:</strong> Upload a clear audio recording that students can listen to while answering the questions below.
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
                                    <div id="picture-mcq-images-container">
                                        @foreach($picture_mcq_image_uploads as $idx => $imageUpload)
                                            <div class="picture-mcq-image-item flex flex-col mb-4 p-4 border-2 border-dashed border-gray-300 rounded-lg" wire:key="picture_mcq_image_{{ $idx }}">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="font-medium text-gray-700">Image {{ $idx + 1 }}</span>
                                                    @if($idx > 0)
                                                        <button type="button" wire:click="removePictureMcqImage({{ $idx }})" class="remove-btn-small">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    @endif
                                                </div>
                                                <input type="file" wire:model="picture_mcq_image_uploads.{{ $idx }}" class="modern-input" accept="image/*" placeholder="Upload image">
                                                @error("picture_mcq_image_uploads.{$idx}") <p class="error-text">{{ $message }}</p> @enderror
                                                
                                                @if(isset($picture_mcq_image_uploads[$idx]) && $picture_mcq_image_uploads[$idx])
                                                    <div class="mt-2">
                                                        <img src="{{ $picture_mcq_image_uploads[$idx]->temporaryUrl() }}" alt="Preview" class="w-20 h-20 object-cover rounded border">
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
                                        <button type="button" wire:click="clearAllPictureMcqPairs" class="clear-all-btn">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Clear All Pairs
                                        </button>
                                    </div>
                                </div>
                                <div class="info-banner-small">
                                    <span class="text-sm">Image indices: 0 = first image, 1 = second image, etc. Text indices: 0 = first text option, 1 = second text option, etc.</span>
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
                                                    <select class="option-input" wire:model.live="picture_mcq_correct_pairs.{{ $pairIdx }}.left" wire:key="picture-mcq-left-select-{{ $pairIdx }}-{{ count($picture_mcq_image_uploads) }}">
                                                        <option value="">Select Image</option>
                                                        @foreach($this->getFilteredPictureMcqImages() as $idx => $image)
                                                            @php
                                                                $alreadySelected = false;
                                                                foreach ($picture_mcq_correct_pairs as $otherIdx => $otherPair) {
                                                                    if ($otherIdx !== $pairIdx && isset($otherPair['left']) && $otherPair['left'] !== '' && $otherPair['left'] == $idx) {
                                                                        $alreadySelected = true;
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp
                                                            @if(!$alreadySelected)
                                                                <option value="{{ $idx }}">{{ $idx }}. Image {{ $idx + 1 }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex-1">
                                                    <label class="modern-label">Text Option</label>
                                                    <select class="option-input" wire:model.live="picture_mcq_correct_pairs.{{ $pairIdx }}.right" wire:key="picture-mcq-right-select-{{ $pairIdx }}-{{ count($picture_mcq_right_options) }}">
                                                        <option value="">Select Text Option</option>
                                                        @foreach($this->getFilteredPictureMcqRightOptions() as $idx => $option)
                                                            @php
                                                                $alreadySelected = false;
                                                                foreach ($picture_mcq_correct_pairs as $otherIdx => $otherPair) {
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

                            <!-- Preview Section for Picture MCQ -->
                            <div class="mb-6" wire:key="picture-mcq-preview-{{ count($picture_mcq_image_uploads) }}-{{ count($picture_mcq_right_options) }}">
                                <h4 class="sub-question-title mb-4">Preview</h4>
                                <div class="preview-section">
                                    @php
                                        $validPairs = array_filter($picture_mcq_correct_pairs, function($pair) {
                                            return isset($pair['left'], $pair['right']) && 
                                                   $pair['left'] !== '' && $pair['right'] !== '' &&
                                                   $pair['left'] !== null && $pair['right'] !== null;
                                        });
                                    @endphp
                                    
                                    @if(count($validPairs) > 0)
                                        <p class="preview-label mb-4">💡 Image-Text Matching Preview:</p>
                                        <div class="space-y-6">
                                            @foreach($validPairs as $index => $pair)
                                                @php
                                                    $imageIndex = (int)$pair['left'];
                                                    $textIndex = (int)$pair['right'];
                                                    $imageUpload = $picture_mcq_image_uploads[$imageIndex] ?? null;
                                                    $textOption = $picture_mcq_right_options[$textIndex] ?? '';
                                                @endphp
                                                
                                                @if($imageUpload && trim($textOption) !== '')
                                                    <div class="picture-mcq-match-item">
                                                        <div class="flex items-center justify-center space-x-8 p-6 bg-white border-2 border-green-200 rounded-xl">
                                                            <!-- Image Section -->
                                                            <div class="flex flex-col items-center space-y-2">
                                                                <div class="image-container">
                                                                    <img src="{{ $imageUpload->temporaryUrl() }}" 
                                                                         alt="Image {{ $imageIndex + 1 }}" 
                                                                         class="w-24 h-24 object-cover rounded-lg border-2 border-blue-300">
                                                                </div>
                                                                <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">Image {{ $imageIndex + 1 }}</span>
                                                            </div>
                                                            
                                                            <!-- Arrow -->
                                                            <div class="flex items-center px-4">
                                                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                                </svg>
                                                            </div>
                                                            
                                                            <!-- Text Section -->
                                                            <div class="flex flex-col items-center space-y-2">
                                                                <div class="text-container bg-blue-50 border-2 border-blue-300 px-6 py-3 rounded-lg">
                                                                    <span class="font-bold text-blue-900 text-lg">{{ trim($textOption) }}</span>
                                                                </div>
                                                                <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">Text Option</span>
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 font-medium">Upload images, add text options, and set answer pairs to see preview</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Simple True/False Section -->
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
                                        @if(is_array($form_fill_answer_key) && count($form_fill_answer_key) > 0)
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
                                        @else
                                            <!-- Show at least one answer key field if array is empty -->
                                            <div class="answer-key-item" wire:key="form_fill_answer_0">
                                                <div class="flex items-center space-x-3">
                                                    <div class="answer-number">Blank 1</div>
                                                    <div class="flex-1">
                                                        <input type="text" wire:model.live="form_fill_answer_key.0" 
                                                               placeholder="Enter the correct answer for blank 1..." class="option-input">
                                                        @error("form_fill_answer_key.0") <p class="error-text">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <button type="button" wire:click="addFormFillAnswerKey" class="add-btn-small">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
                                    
                                    @if(is_array($form_fill_answer_key) && count($filteredAnswerKeys) > 0)
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
                        </div>

                        <!-- MCQ Multiple Section -->
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
                                                <label class="option-label">Option {{ $index }}</label>
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
                                                <button type="button" wire:click="removeLeftOption({{ $idx }})" class="remove-btn w-8 h-8 flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" wire:click="addLeftOption" class="add-btn mt-2 w-32">+ Add Left Option</button>
                                </div>
                                <!-- Right Side Options -->
                                <div>
                                    <h5 class="font-semibold mb-2">Right Side Options</h5>
                                    <div id="right-options-container">
                                        @foreach($right_options as $idx => $option)
                                            <div class="option-item flex items-center mb-2" wire:key="right_option_{{ $idx }}">
                                                <input type="text" wire:model.live="right_options.{{ $idx }}" class="option-input flex-1 mr-2" placeholder="Enter right option">
                                                <button type="button" wire:click="removeRightOption({{ $idx }})" class="remove-btn w-8 h-8 flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" wire:click="addRightOption" class="add-btn mt-2 w-32">+ Add Right Option</button>
                                </div>
                            </div>
                        </div>

                        <!-- Correct Answer Indices Section -->
                        <!-- Show for statement_match -->
                        <div class="section-block" x-show="type === 'statement_match'">
                            <h3 class="section-title">Correct Answer Pairs</h3>
                            <div class="info-banner">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Use indices starting from 0. Left side: 0 = first option, 1 = second option, etc. Right side: 0 = first option, 1 = second option, etc.
                            </div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="sub-question-title">Answer Pairs</h4>
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
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4" wire:key="correct-pairs-section">
                                @foreach($correct_pairs as $pairIdx => $pair)
                                    <div class="option-item" wire:key="pair-{{ $pairIdx }}">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="font-semibold" style="color: #000 !important;">Correct Pair {{ $pairIdx+1 }}</div>
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
                        
                        <!-- Show for other types (NOT statement_match, NOT opinion, NOT mcq_multiple, NOT true_false_multiple, NOT true_false, NOT reorder, NOT form_fill, NOT picture_mcq, NOT audio_mcq_single, NOT audio_image_text_single, NOT audio_image_text_multiple, NOT audio_fill_blank, NOT picture_fill_blank, NOT video_fill_blank, NOT audio_picture_match) -->
                        <div class="section-block" x-show="type !== 'statement_match' && type !== 'opinion' && type !== 'mcq_multiple' && type !== 'true_false_multiple' && type !== 'true_false' && type !== 'reorder' && type !== 'form_fill' && type !== 'picture_mcq' && type !== 'audio_mcq_single' && type !== 'audio_image_text_single' && type !== 'audio_image_text_multiple' && type !== 'audio_fill_blank' && type !== 'picture_fill_blank' && type !== 'video_fill_blank' && type !== 'audio_picture_match'">
                            <h3 class="section-title">Correct Answer Indices</h3>
                            <div class="info-banner">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Use 0 for first option, 1 for second option, 2 for third option, etc.
                            </div>
                            <div class="indices-wrapper">
                                <div class="space-y-4">
                                    @foreach($answer_indices as $index => $answer_index)
                                        <div class="index-item">
                                            <div class="flex items-center justify-between mb-3">
                                                <label class="option-label">Answer Index {{ $index }}</label>
                                                <div class="button-group">
                                                    @if($index === 0)
                                                        <button type="button" wire:click="addAnswerIndex" class="add-index-btn">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                            Add Index
                                                        </button>
                                                    @else
                                                        <button type="button" wire:click="removeAnswerIndex({{ $index }})" class="remove-btn">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Remove
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="number" wire:model="answer_indices.{{ $index }}" min="0" placeholder="0" class="index-input">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Audio Fill in the Blanks Section -->
                        <div class="section-block" x-show="type === 'audio_fill_blank'">
                            <h3 class="section-title">Audio Fill in the Blanks</h3>
                            <!-- Audio File Upload -->
                            <div class="mb-6">
                                <label class="modern-label">Audio File *</label>
                                <input type="file" wire:model="audio_file" accept="audio/*" class="modern-input">
                                @error('audio_file') <p class="error-text">{{ $message }}</p> @enderror
                            </div>
                            <!-- Paragraph with Blanks -->
                            <div class="mb-6">
                                <h4 class="sub-question-title mb-4">Paragraph with Blanks</h4>
                                <div class="form-fill-paragraph-section">
                                    <label class="modern-label">Paragraph Text (use ___ for blanks) *</label>
                                    <textarea wire:model.lazy="audio_fill_paragraph" rows="6"
                                              placeholder="Enter your paragraph here. Use ___ (three underscores) to mark blanks where students should fill in answers. For example: The capital of France is ___. It is located in the ___ of the country."
                                              class="modern-textarea"></textarea>
                                    @error('audio_fill_paragraph') <p class="error-text">{{ $message }}</p> @enderror
                                    @if(trim($audio_fill_paragraph ?? ''))
                                        <div class="paragraph-info">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Detected {{ substr_count($audio_fill_paragraph, '___') }} blank(s) in the paragraph.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- Answer Keys -->
                            <div class="mb-6">
                                <h4 class="sub-question-title mb-4">Answer Keys</h4>
                                <div class="answer-key-section">
                                    <div class="space-y-4">
                                        @if(is_array($audio_fill_answer_key) && count($audio_fill_answer_key) > 0)
                                            @foreach($audio_fill_answer_key as $index => $answerKey)
                                                <div class="answer-key-item" wire:key="audio-fill-answer-{{ $index }}">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="answer-number">Blank {{ $index + 1 }}</div>
                                                        <div class="flex-1">
                                                            <input type="text" wire:model.live="audio_fill_answer_key.{{ $index }}"
                                                                   placeholder="Enter the correct answer for blank {{ $index + 1 }}..." class="option-input">
                                                            @error("audio_fill_answer_key.{$index}") <p class="error-text">{{ $message }}</p> @enderror
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($index === 0)
                                                                <button type="button" wire:click="addAudioFillAnswerKey" class="add-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                    </svg>
                                                                </button>
                                                            @else
                                                                <button type="button" wire:click="removeAudioFillAnswerKey({{ $index }})" class="remove-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <!-- Show at least one answer key field if array is empty -->
                                            <div class="answer-key-item" wire:key="audio-fill-answer-0">
                                                <div class="flex items-center space-x-3">
                                                    <div class="answer-number">Blank 1</div>
                                                    <div class="flex-1">
                                                        <input type="text" wire:model.live="audio_fill_answer_key.0"
                                                               placeholder="Enter the correct answer for blank 1..." class="option-input">
                                                        @error("audio_fill_answer_key.0") <p class="error-text">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <button type="button" wire:click="addAudioFillAnswerKey" class="add-btn-small">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Preview Section -->
                            <div class="mb-6" wire:key="audio-fill-preview-{{ count($audio_fill_answer_key) }}">
                                <h4 class="sub-question-title mb-4">Preview</h4>
                                <div class="preview-section">
                                    @php
                                        $filteredAnswerKeys = array_filter($audio_fill_answer_key, fn($a) => trim($a ?? '') !== '');
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
                                    @if($hasValidPreview)
                                        <div class="preview-filled-main" wire:key="audio-fill-main-preview-{{ md5($previewParagraph . implode('', $filteredAnswerKeys)) }}">
                                            <p class="preview-label-main">✅ Final Sentence with Answers:</p>
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
                        <div class="section-block" x-show="type === 'picture_fill_blank'">
                            <h3 class="section-title">Picture Fill in the Blanks</h3>
                            <!-- Image Upload -->
                            <div class="mb-6">
                                <label class="modern-label">Image File *</label>
                                <input type="file" wire:model="picture_fill_image" accept="image/*" class="modern-input">
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
                                            <span>Detected {{ substr_count($picture_fill_paragraph, '___') }} blank(s) in the paragraph.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- Answer Keys -->
                            <div class="mb-6">
                                <h4 class="sub-question-title mb-4">Answer Keys</h4>
                                <div class="answer-key-section">
                                    <div class="space-y-4">
                                        @if(is_array($picture_fill_answer_key) && count($picture_fill_answer_key) > 0)
                                            @foreach($picture_fill_answer_key as $index => $answerKey)
                                                <div class="answer-key-item" wire:key="picture-fill-answer-{{ $index }}">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="answer-number">Blank {{ $index + 1 }}</div>
                                                        <div class="flex-1">
                                                            <input type="text" wire:model.live="picture_fill_answer_key.{{ $index }}"
                                                                   placeholder="Enter the correct answer for blank {{ $index + 1 }}..." class="option-input">
                                                            @error("picture_fill_answer_key.{$index}") <p class="error-text">{{ $message }}</p> @enderror
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($index === 0)
                                                                <button type="button" wire:click="addPictureFillAnswerKey" class="add-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                    </svg>
                                                                </button>
                                                            @else
                                                                <button type="button" wire:click="removePictureFillAnswerKey({{ $index }})" class="remove-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <!-- Show at least one answer key field if array is empty -->
                                            <div class="answer-key-item" wire:key="picture-fill-answer-0">
                                                <div class="flex items-center space-x-3">
                                                    <div class="answer-number">Blank 1</div>
                                                    <div class="flex-1">
                                                        <input type="text" wire:model.live="picture_fill_answer_key.0"
                                                               placeholder="Enter the correct answer for blank 1..." class="option-input">
                                                        @error("picture_fill_answer_key.0") <p class="error-text">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <button type="button" wire:click="addPictureFillAnswerKey" class="add-btn-small">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Preview Section -->
                            <div class="mb-6" wire:key="picture-fill-preview-{{ count($picture_fill_answer_key) }}">
                                <h4 class="sub-question-title mb-4">Preview</h4>
                                <div class="preview-section">
                                    @if($picture_fill_image)
                                        <div class="mb-4">
                                            <img src="{{ $picture_fill_image instanceof Illuminate\Http\UploadedFile ? $picture_fill_image->temporaryUrl() : Storage::url($picture_fill_image) }}" alt="Uploaded Image" class="preview-image object-cover rounded border-2 border-blue-300" style="max-width: 300px; max-height: 200px;">
                                        </div>
                                    @endif
                                    @php
                                        $filteredAnswerKeys = array_filter($picture_fill_answer_key, fn($a) => trim($a ?? '') !== '');
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
                                    @if($hasValidPreview)
                                        <div class="preview-filled-main" wire:key="picture-fill-main-preview-{{ md5($previewParagraph . implode('', $filteredAnswerKeys)) }}">
                                            <p class="preview-label-main">✅ Final Sentence with Answers:</p>
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
                        <div class="section-block" x-show="type === 'video_fill_blank'">
                            <h3 class="section-title">Video Fill in the Blanks</h3>
                            <!-- Video Upload -->
                            <div class="mb-6">
                                <label class="modern-label">Video File *</label>
                                <input type="file" wire:model="video_fill_video" accept="video/*" class="modern-input">
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
                                            <span>Detected {{ substr_count($video_fill_paragraph, '___') }} blank(s) in the paragraph.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- Answer Keys -->
                            <div class="mb-6">
                                <h4 class="sub-question-title mb-4">Answer Keys</h4>
                                <div class="answer-key-section">
                                    <div class="space-y-4">
                                        @if(is_array($video_fill_answer_key) && count($video_fill_answer_key) > 0)
                                            @foreach($video_fill_answer_key as $index => $answerKey)
                                                <div class="answer-key-item" wire:key="video-fill-answer-{{ $index }}">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="answer-number">Blank {{ $index + 1 }}</div>
                                                        <div class="flex-1">
                                                            <input type="text" wire:model.live="video_fill_answer_key.{{ $index }}"
                                                                   placeholder="Enter the correct answer for blank {{ $index + 1 }}..." class="option-input">
                                                            @error("video_fill_answer_key.{$index}") <p class="error-text">{{ $message }}</p> @enderror
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($index === 0)
                                                                <button type="button" wire:click="addVideoFillAnswerKey" class="add-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                    </svg>
                                                                </button>
                                                            @else
                                                                <button type="button" wire:click="removeVideoFillAnswerKey({{ $index }})" class="remove-btn-small">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <!-- Show at least one answer key field if array is empty -->
                                            <div class="answer-key-item" wire:key="video-fill-answer-0">
                                                <div class="flex items-center space-x-3">
                                                    <div class="answer-number">Blank 1</div>
                                                    <div class="flex-1">
                                                        <input type="text" wire:model.live="video_fill_answer_key.0"
                                                               placeholder="Enter the correct answer for blank 1..." class="option-input">
                                                        @error("video_fill_answer_key.0") <p class="error-text">{{ $message }}</p> @enderror
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <button type="button" wire:click="addVideoFillAnswerKey" class="add-btn-small">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Preview Section -->
                            <div class="mb-6" wire:key="video-fill-preview-{{ count($video_fill_answer_key) }}">
                                <h4 class="sub-question-title mb-4">Preview</h4>
                                <div class="preview-section">
                                    @if($video_fill_video)
                                        <div class="mb-4">
                                            <video controls class="rounded border-2 border-blue-300" style="max-width: 400px; max-height: 250px;">
                                                <source src="{{ $video_fill_video instanceof Illuminate\Http\UploadedFile ? $video_fill_video->temporaryUrl() : Storage::url($video_fill_video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    @endif
                                    @php
                                        $filteredAnswerKeys = array_filter($video_fill_answer_key, fn($a) => trim($a ?? '') !== '');
                                        $previewParagraph = trim($video_fill_paragraph ?? '');
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
                                    @if($hasValidPreview)
                                        <div class="preview-filled-main" wire:key="video-fill-main-preview-{{ md5($previewParagraph . implode('', $filteredAnswerKeys)) }}">
                                            <p class="preview-label-main">✅ Final Sentence with Answers:</p>
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
                        <div class="section-block" x-show="type === 'audio_picture_match'">
                            <h3 class="section-title">Audio + Picture Matching</h3>
                            <div class="info-banner">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Upload audio files on the left and image files on the right. Students will match each audio to the correct image.
                            </div>
                            <div class="grid grid-cols-2 gap-6">
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
                                                @if(isset($audio_picture_audios[$idx]) && $audio_picture_audios[$idx])
                                                    <div class="mt-2">
                                                        <audio controls class="w-full">
                                                            <source src="{{ $audio_picture_audios[$idx]->temporaryUrl() }}" type="audio/mpeg">
                                                            Your browser does not support the audio element.
                                                        </audio>
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
                                                @if(isset($audio_picture_images[$idx]) && $audio_picture_images[$idx])
                                                    <div class="mt-2">
                                                        <img src="{{ $audio_picture_images[$idx]->temporaryUrl() }}" alt="Preview" class="preview-image object-cover rounded border" style="width: 120px; height: 120px; max-width: 100%; max-height: 100%;">
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
                            <div class="mt-6">
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
                                <div class="info-banner-small">
                                    <span class="text-sm">Audio indices: 0 = first audio, 1 = second audio, etc. Image indices: 0 = first image, 1 = second image, etc.</span>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4" wire:key="audio-picture-correct-pairs-section">
                                    @foreach($audio_picture_pairs as $pairIdx => $pair)
                                        <div class="option-item" wire:key="audio-picture-pair-{{ $pairIdx }}">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="font-semibold" style="color: #000 !important;">Correct Pair {{ $pairIdx+1 }}</div>
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
                                                                <audio controls class="w-40">
                                                                    <source src="{{ $audioUpload->temporaryUrl() }}" type="audio/mpeg">
                                                                    Your browser does not support the audio element.
                                                                </audio>
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
                                                                <img src="{{ $imageUpload->temporaryUrl() }}" alt="Image {{ $imageIndex + 1 }}" class="preview-image object-cover rounded-lg border-2 border-blue-300">
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
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 font-medium">Upload audio and image files, then set answer pairs to see preview</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Media Upload Section (hidden by default) -->
                    <div class="section-block" id="media-upload-section" style="display:none;">
                        <h3 class="section-title">Media Upload</h3>
                        <input type="file" name="media_file" id="media_file" class="modern-input" accept="audio/*,image/*">
                        <small class="text-gray-500">Upload an audio or image file as required by the question type.</small>
                    </div>

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
                                Create Question
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
    <style>
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

    .section-block:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
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
        color: #374151;
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
        color: #374151 !important;
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
        color: #1f2937 !important;
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
        color: #374151;
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
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 12px !important;
        font-size: 1rem !important;
        font-weight: 700 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        text-decoration: none !important;
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4) !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .submit-btn:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #7c3aed 100%) !important;
        color: white !important;
        transform: translateY(-3px) !important;
        box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4) !important;
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

    .picture-mcq-match-item .image-container {
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border-radius: 12px;
        overflow: hidden;
    }

    .picture-mcq-match-item .image-container img {
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
    }

    .picture-mcq-match-item .image-container img:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 15px -3px rgba(59, 130, 246, 0.4);
    }

    .picture-mcq-match-item .text-container {
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        transition: all 0.3s ease;
        min-width: 180px;
        text-align: center;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
        border: 2px solid #3b82f6 !important;
    }

    .picture-mcq-match-item .text-container span {
        color: #1e3a8a !important;
        font-weight: 700 !important;
        font-size: 1rem !important;
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
    }

    .picture-mcq-match-item .text-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 10px -1px rgba(59, 130, 246, 0.3);
        background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%) !important;
    }

    .picture-mcq-match-item svg {
        transition: all 0.3s ease;
        filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.1));
    }

    .picture-mcq-match-item:hover svg {
        transform: translateX(6px) scale(1.1);
        color: #059669;
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

        .picture-mcq-match-item .image-container {
            width: 100px;
            height: 100px;
        }

        .picture-mcq-match-item .flex {
            flex-direction: column !important;
            space-x: 0 !important;
        }

        .picture-mcq-match-item .flex > * + * {
            margin-left: 0 !important;
            margin-top: 1.5rem !important;
        }

        .picture-mcq-match-item svg {
            transform: rotate(90deg);
        }

        .picture-mcq-match-item:hover svg {
            transform: rotate(90deg) translateX(6px) scale(1.1);
        }

        .picture-mcq-match-item .text-container {
            min-width: 200px;
        }
    }

    @media (max-width: 480px) {
        .preview-image {
            width: 70px !important;
            height: 70px !important;
        }

        .picture-mcq-match-item .image-container {
            width: 70px;
            height: 70px;
        }

        .picture-mcq-match-item .text-container {
            min-width: 160px;
            font-size: 0.875rem !important;
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

    /* Additional Button Group Styling for Better Alignment */
    .flex.space-x-2 .add-btn,
    .flex.space-x-2 .clear-all-btn {
        display: inline-flex !important;
        align-items: center !important;
        white-space: nowrap !important;
    }

    /* Enhanced Pair Management Buttons */
    .add-btn svg, .clear-all-btn svg, .clear-pair-btn svg {
        flex-shrink: 0 !important;
    }

    /* Mobile Responsive Button Groups */
    @media (max-width: 640px) {
        .flex.space-x-2 {
            flex-direction: column !important;
            space-x: 0 !important;
            gap: 0.5rem !important;
        }
        
        .flex.space-x-2 > * {
            margin-left: 0 !important;
        }
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

    /* Loading State Styles */
    .option-item.loading, .index-item.loading {
        opacity: 0.7;
        pointer-events: none;
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

    /* Improved Button Spacing in Grid Layouts */
    .grid .button-group {
        justify-content: flex-end;
        margin-top: 0.5rem;
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

    /* Smooth Transitions for Dynamic Content */
    .section-block {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            });

            // Enhanced error handling
            window.addEventListener('livewire:load', function() {
                Livewire.on('validationError', function(message) {
                    // Show toast notification for validation errors
                    console.log('Validation Error:', message);
                });
                
                Livewire.on('questionCreated', function() {
                    // Show success animation
                    const card = document.querySelector('.modern-card');
                    if (card) {
                        card.style.transform = 'scale(1.02)';
                        card.style.borderColor = '#10b981';
                        setTimeout(() => {
                            card.style.transform = '';
                            card.style.borderColor = '';
                        }, 1000);
                    }
                });
            });

            // File upload progress indication
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
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
</x-filament-panels::page>