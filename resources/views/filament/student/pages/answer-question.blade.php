<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Answer Question - Edguide Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        @keyframes fade-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fade-in 0.3s ease; }
        
        /* View Mode Styles */
        .view-mode-banner {
            animation: slideInDown 0.3s ease;
        }
        
        .view-mode .mcq-option-item.submitted-answer {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }
        
        .view-mode .mcq-option-item.submitted-answer .option-indicator {
            background: #10b981;
            color: white;
        }
        
        .view-mode .mcq-option-item.submitted-answer .selection-mark {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }
        
        .view-mode .checkbox-option-item.submitted-answer {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border-color: #3b82f6;
        }
        
        .view-mode .tf-option-item.submitted-answer.true-option .tf-card {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border-color: #10b981;
        }
        
        .view-mode .tf-option-item.submitted-answer.false-option .tf-card {
            background: linear-gradient(135deg, #fee2e2, #fca5a5);
            border-color: #ef4444;
        }
        
        .view-mode input[type="radio"]:checked,
        .view-mode input[type="checkbox"]:checked {
            background-color: #10b981;
            border-color: #10b981;
        }
        
        .view-mode input[disabled],
        .view-mode select[disabled] {
            background-color: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .submitted-mark {
            color: #10b981;
            font-weight: bold;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .view-mode-section {
            background: linear-gradient(135deg, #f0f9ff, #e6f3ff);
            border: 2px solid #3b82f6;
            box-shadow: 0 4px 12px -4px rgba(59, 130, 246, 0.2);
        }
        
        .submitted-answer {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0) !important;
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
        }
        
        .submitted-answer .option-indicator,
        .submitted-answer .checkbox-indicator,
        .submitted-answer .tf-indicator {
            background: #10b981 !important;
            color: white !important;
        }
        
        .submitted-answer .selection-mark,
        .submitted-answer .checkmark {
            opacity: 1 !important;
            color: white !important;
        }
        
        /* Success Modal Styles */
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .modal-header.bg-success {
            border-bottom: none;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border: 1px solid #10b981;
            color: #065f46;
        }
        
        .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            border-radius: 8px;
        }
        
        /* Options Reference Styles */
        .options-reference-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
            box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.1);
        }
        
        .options-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .options-title::before {
            content: "📝";
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }
        
        .options-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .option-tag {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            border: 1px solid #3b82f6;
            transition: all 0.2s ease;
        }
        
        .option-tag:hover {
            background: linear-gradient(135deg, #bfdbfe, #93c5fd);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px -2px rgba(59, 130, 246, 0.3);
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
@php
    $user = auth()->user();
    $type = $question->question_type_id ? ($question->questionType->name ?? null) : null;
    $qdata = is_array($question->question_data) ? $question->question_data : (json_decode($question->question_data, true) ?? []);
    
    // Set a default value for $isReAttempt if not already set
    $isReAttempt = $isReAttempt ?? false;
    
    // Check if we have a result from the session (means this is a submission result page)
    $hasResult = session('answer_result') !== null;
    
    // CRITICAL FIX: Only show detailed result modal on re-attempts
    // Initial submissions should never show detailed comparison
    $showDetailedResult = $hasResult && $isReAttempt && session('show_result_modal');
    
    // For initial submissions, just show simple success/failure message and redirect
    $showSimpleResult = $hasResult && !$isReAttempt && session('show_result_modal');
    
    // For re-attempt mode, always allow new submission (don't show view mode)
    $isReadOnly = $isReAttempt ? false : (isset($studentAnswer) && $studentAnswer->answer_data !== null && $studentAnswer->answer_data !== '');
    $isViewMode = $isReAttempt ? false : (isset($submittedAnswer) && !empty($submittedAnswer));
    
    // For re-attempt mode, don't show previous answers (allow fresh submission)
    $viewModeAnswerData = $isReAttempt ? null : (
        $isViewMode && isset($submittedAnswer) && !empty($submittedAnswer) ? 
        (is_string($submittedAnswer->answer_data ?? $submittedAnswer['answer_data'] ?? null) 
            ? (json_decode($submittedAnswer->answer_data ?? $submittedAnswer['answer_data'] ?? '', true) ?: ($submittedAnswer->answer_data ?? $submittedAnswer['answer_data'] ?? null)) 
            : ($submittedAnswer->answer_data ?? $submittedAnswer['answer_data'] ?? null)) 
        : null
    );
    
    // Convert string answer to index for MCQ Single
    if ($type === 'mcq_single' && is_string($viewModeAnswerData)) {
        $viewModeAnswerData = $isReAttempt ? null : intval($viewModeAnswerData);
    }
    
    // Initialize $answer if not already set
    if (!isset($answer)) {
        $answer = null;
    }
    
    $answer = $isReAttempt ? null : $answer;
    
    // Additional debugging
    \Log::info('Submission Flow Debug', [
        'hasResult' => $hasResult,
        'isReAttempt' => $isReAttempt,
        'showDetailedResult' => $showDetailedResult,
        'showSimpleResult' => $showSimpleResult,
        'session_show_modal' => session('show_result_modal')
    ]);
    
    // Debugging: Print out all relevant variables
    \Log::info('Answer Question Debug', [
        'type' => $type,
        'submittedAnswer' => $submittedAnswer ?? 'Not set',
        'studentAnswer' => $studentAnswer ?? 'Not set',
        'question_data' => $qdata,
        'isReadOnly' => $isReadOnly ?? 'Not set',
        'hasResult' => $hasResult,
        'isReAttempt' => $isReAttempt
    ]);
    
    // Additional debugging
    \Log::info('View Mode Debug', [
        'isViewMode' => $isViewMode,
        'viewModeAnswerData' => $viewModeAnswerData
    ]);
    
    if (!isset($answer)) {
        $answer = null; // Only set to null if not already set
    }
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Back Button -->
                <div class="flex items-center min-w-0 flex-1">
                    <button onclick="history.back()" class="mr-3 sm:mr-4 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200 flex-shrink-0">
                        <i class="fas fa-arrow-left text-lg sm:text-xl"></i>
                    </button>
                    <div class="flex items-center min-w-0">
                        <img src="{{ asset('/edguide-logo.png') }}" alt="EdGuide" class="header-logo h-8 sm:h-10 md:h-12 flex-shrink-0" style="width: auto; max-width: 120px; sm:max-width: 140px;" />
                    </div>
                </div>

                <!-- Question Info -->
                <div class="hidden md:flex items-center space-x-4">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Question Type:</span>
                        <span class="ml-1 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {{ ucfirst(str_replace('_', ' ', $type ?? 'Question')) }}
                        </span>
                    </div>
                    @if($question->points)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Points:</span>
                            <span class="ml-1 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                {{ $question->points }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- User Info -->
                <div class="flex items-center flex-shrink-0 ml-3">
                    <div class="hidden sm:block mr-3">
                        <span class="text-xs sm:text-sm text-gray-600 truncate max-w-24 sm:max-w-32">{{ $user->name }}</span>
                    </div>
                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" 
                         alt="{{ $user->name }}" 
                         class="h-7 w-7 sm:h-8 sm:w-8 rounded-full border-2 border-gray-200 flex-shrink-0" />
                </div>
            </div>
        </div>

        <!-- Mobile Question Info -->
        <div class="md:hidden px-3 py-2 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 text-sm">
                <span class="text-gray-600 flex items-center">
                    <span class="font-medium text-xs">Type:</span>
                    <span class="ml-1 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                        {{ ucfirst(str_replace('_', ' ', $type ?? 'Question')) }}
                    </span>
                </span>
                @if($question->points)
                    <span class="text-gray-600 flex items-center">
                        <span class="font-medium text-xs">Points:</span>
                        <span class="ml-1 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                            {{ $question->points }}
                        </span>
                    </span>
                @endif
            </div>
        </div>
    </nav>

    <!-- Breadcrumb Navigation -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm overflow-x-auto scrollbar-hide">
                <a href="{{ route('filament.student.pages.dashboard') }}" class="text-blue-600 hover:text-blue-800 transition-colors duration-200 whitespace-nowrap">
                    <i class="fas fa-home mr-1"></i><span class="hidden sm:inline">Dashboard</span>
                </a>
                <i class="fas fa-chevron-right text-gray-400 flex-shrink-0"></i>
                <a href="{{ route('filament.student.pages.courses') }}" class="text-blue-600 hover:text-blue-800 transition-colors duration-200 whitespace-nowrap">
                    <span class="hidden sm:inline">Courses</span><span class="sm:hidden">C</span>
                </a>
                <i class="fas fa-chevron-right text-gray-400 flex-shrink-0"></i>
                <a href="{{ route('filament.student.pages.questions') }}" class="text-blue-600 hover:text-blue-800 transition-colors duration-200 whitespace-nowrap">
                    <span class="hidden sm:inline">Questions</span><span class="sm:hidden">Q</span>
                </a>
                <i class="fas fa-chevron-right text-gray-400 flex-shrink-0"></i>
                <span class="text-gray-600 font-medium whitespace-nowrap">
                    <span class="hidden sm:inline">Answer Question</span><span class="sm:hidden">Answer</span>
                </span>
            </nav>
        </div>
    </div>

    <!-- Session Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-b border-green-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-green-800">Success!</h3>
                        <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border-b border-blue-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-blue-800">Information</h3>
                        <p class="text-sm text-blue-700 mt-1">{{ session('info') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-b border-red-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Error</h3>
                        <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-b border-red-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Validation Errors</h3>
                        <ul class="text-sm text-red-700 mt-1 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- JavaScript Validation Error Banner (hidden by default) -->
    <div id="js-validation-error" class="bg-red-50 border-b border-red-200 hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                <div>
                    <h3 class="text-sm font-medium text-red-800">Please Complete All Required Fields</h3>
                    <p class="text-sm text-red-700 mt-1">Make sure you have selected an answer for all questions before submitting.</p>
                </div>
            </div>
        </div>
    </div>

    @if($isViewMode && !$isReAttempt && isset($submittedAnswer) && !empty($submittedAnswer->answer_data ?? $submittedAnswer['answer_data'] ?? null))
        <div class="view-mode-banner bg-blue-50 border-b border-blue-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-800">Viewing Submitted Answer</h3>
                        <p class="text-sm text-blue-700 mt-1">
                            Submitted on {{ $submittedAnswer->submitted_at ?? $submittedAnswer['submitted_at'] ?? 'Unknown Date' }}
                            @if(null !== ($submittedAnswer->is_correct ?? $submittedAnswer['is_correct'] ?? null))
                                • 
                                <span class="{{ ($submittedAnswer->is_correct ?? $submittedAnswer['is_correct'] ?? false) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ($submittedAnswer->is_correct ?? $submittedAnswer['is_correct'] ?? false) ? 'Correct' : 'Incorrect' }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @php
        $isOpinionQuestion = $question->questionType && $question->questionType->name === 'opinion';
    @endphp

    @if($isOpinionQuestion && isset($existingAnswer) && $existingAnswer)
        <!-- Opinion Question Status Message -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                @if($existingAnswer->verification_status === 'pending')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-blue-800">Answer Submitted</h3>
                                <p class="text-sm text-blue-700 mt-1">Your opinion answer is awaiting verification by your teacher. You can update your response below if needed.</p>
                            </div>
                        </div>
                    </div>
                @elseif($existingAnswer->verification_status === 'verified_correct')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-green-800">Answer Verified as Correct</h3>
                                <p class="text-sm text-green-700 mt-1">Your opinion answer has been verified as correct! You earned 1 point. You can submit a new answer if you wish.</p>
                                @if($existingAnswer->verification_comment)
                                    <div class="mt-3 p-3 bg-white rounded-lg border border-green-200">
                                        <h4 class="text-xs font-medium text-green-800 mb-1">Teacher's Comment:</h4>
                                        <p class="text-sm text-gray-700">{{ $existingAnswer->verification_comment }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($existingAnswer->verification_status === 'verified_incorrect')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-yellow-800">Answer Needs Revision</h3>
                                <p class="text-sm text-yellow-700 mt-1">Your opinion answer has been reviewed and needs improvement. Please provide a new response below.</p>
                                @if($existingAnswer->verification_comment)
                                    <div class="mt-3 p-3 bg-white rounded-lg border border-yellow-200">
                                        <h4 class="text-xs font-medium text-yellow-800 mb-1">Teacher's Comment:</h4>
                                        <p class="text-sm text-gray-700">{{ $existingAnswer->verification_comment }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Simple Result for Initial Submissions -->
    @if($showSimpleResult)
        @php
            $result = session('answer_result');
        @endphp
        
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show simple success/failure message
            @if(isset($result['is_correct']) && $result['is_correct'])
                // Correct answer
                showToast('Correct Answer! You earned {{ $question->points ?? 1 }} point(s).', 'success');
            @else
                // Wrong answer or needs review
                showToast('Answer submitted! You can re-attempt this question if needed.', 'info');
            @endif
            
            // Clear session flags
            fetch('{{ route("filament.student.clear_result_modal") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            }).catch(error => console.log('Error clearing modal flag:', error));
            
            // Auto redirect after 2 seconds
            setTimeout(function() {
                goBackToQuestions();
            }, 2000);
        });
        
        function showToast(message, type) {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                    <div>
                        <strong>${type === 'success' ? 'Success!' : 'Information'}</strong>
                        <div>${message}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 5000);
        }
        
        function goBackToQuestions() {
            const courseId = {{ $question->course_id ?? 1 }};
            const subjectId = {{ $question->subject_id ?? 1 }};
            const dayId = {{ $question->day_id ?? 1 }};
            
            const questionsUrl = `{{ route('filament.student.pages.questions') }}?course=${courseId}&subject=${subjectId}&day=${dayId}`;
            window.location.href = questionsUrl;
        }
    </script>
    @endif

    <!-- Detailed Result Modal (Re-attempt Submissions Only) -->
    @if($showDetailedResult)
        @php
            $result = session('answer_result');
            $questionId = session('question_id');
            $courseId = session('course_id');
            $subjectId = session('subject_id');
            $dayId = session('day_id');
        @endphp
        
        <!-- Result Modal -->
        <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header {{ $result['is_correct'] ? 'bg-success' : 'bg-danger' }} text-white">
                        <h5 class="modal-title" id="resultModalLabel">
                            @if($result['is_correct'])
                                <i class="fas fa-check-circle me-2"></i>{{ $result['message'] }}
                            @else
                                <i class="fas fa-times-circle me-2"></i>{{ $result['message'] }}
                            @endif
                        </h5>
            </div>
                    <div class="modal-body">
                        @if(!$result['is_correct'])
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-danger"><i class="fas fa-times me-2"></i>Your Answer:</h6>
                                    <div class="p-3 bg-light rounded border-start border-danger border-3">
                                        @if(is_array($result['student_answer_text']))
                                            @php
                                                $questionType = $question->questionType->name ?? '';
                                            @endphp
                                            @if($questionType === 'audio_picture_match')
                                                @foreach($result['student_answer_text'] as $audioIndex => $answerText)
                                                    <div class="mb-2">
                                                        <strong>Audio {{ intval($audioIndex) + 1 }}:</strong> 
                                                        <span class="text-dark">{{ $answerText }}</span>
                                                    </div>
                                                @endforeach
                                            @elseif($questionType === 'picture_mcq' || $questionType === 'audio_image_text_single' || $questionType === 'audio_image_text_multiple')
                                                @if(is_array($result['student_answer_text']))
                                                    @foreach($result['student_answer_text'] as $imageIndex => $answerText)
                                                        <div class="mb-2">
                                                            <span class="text-dark">{{ $answerText ?: '(No answer provided)' }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <span class="text-dark">{{ $result['student_answer_text'] ?: '(No answer provided)' }}</span>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'audio_mcq_single')
                                                @php
                                                    $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                                                    $subQuestions = $questionData['sub_questions'] ?? [];
                                                @endphp
                                                @if(is_array($result['student_answer_text']))
                                                    @foreach($result['student_answer_text'] as $index => $answer)
                                                        @php
                                                            $subQuestion = $subQuestions[$index] ?? [];
                                                            $options = $subQuestion['options'] ?? [];
                                                            $answerIndex = is_numeric($answer) ? intval($answer) : null;
                                                            $answerText = $answerIndex !== null && isset($options[$answerIndex]) ? $options[$answerIndex] : $answer;
                                                        @endphp
                                                        <div class="mb-2">
                                                            <strong>Question {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ $answerText ?: '(No answer provided)' }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <strong>Answer:</strong> 
                                                        <span class="text-dark">{{ $result['student_answer_text'] ?: '(No answer provided)' }}</span>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'true_false_multiple')
                                                @if(is_array($result['student_answer_text']))
                                                    @foreach($result['student_answer_text'] as $index => $answer)
                                                        <div class="mb-2">
                                                            <strong>Statement {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ ucfirst($answer ?: '(No answer provided)') }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <strong>Answer:</strong> 
                                                        <span class="text-dark">{{ ucfirst($result['student_answer_text'] ?: '(No answer provided)') }}</span>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'statement_match')
                                                @if(is_array($result['student_answer_text']))
                                                    @foreach($result['student_answer_text'] as $index => $matchAnswer)
                                                        <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-danger me-2">{{ intval($index) + 1 }}</span>
                                                                <span class="text-dark fw-bold">{{ $matchAnswer ?: '(No answer provided)' }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-danger me-2">1</span>
                                                            <span class="text-dark fw-bold">{{ $result['student_answer_text'] ?: '(No answer provided)' }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'opinion')
                                                <div class="p-3 bg-blue-50 rounded border">
                                                    <p class="text-dark">{{ is_array($result['student_answer_text']) ? implode(' ', $result['student_answer_text']) : ($result['student_answer_text'] ?: '(No response provided)') }}</p>
                                                </div>
                                            @elseif($questionType === 'reorder')
                                                <div class="p-3 bg-blue-50 rounded border">
                                                    <p class="text-dark">{{ is_array($result['student_answer_text']) ? implode(' ', $result['student_answer_text']) : ($result['student_answer_text'] ?: '(No response provided)') }}</p>
                                                </div>
                                            @elseif($questionType === 'form_fill' || $questionType === 'audio_fill_blank' || $questionType === 'picture_fill_blank' || $questionType === 'video_fill_blank')
                                                @if(is_array($result['student_answer_text']))
                                                    @foreach($result['student_answer_text'] as $index => $answer)
                                                        <div class="mb-2">
                                                            <strong>Blank {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ is_array($answer) ? implode(', ', $answer) : ($answer ?: '(No answer provided)') }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="p-3 bg-blue-50 rounded border">
                                                        <p class="text-dark">{{ $result['student_answer_text'] ?: '(No answer provided)' }}</p>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'mcq_single')
                                                @php
                                                    $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                                                    $options = $questionData['options'] ?? [];
                                                @endphp
                                                @if(is_array($result['student_answer_text']))
                                                    @foreach($result['student_answer_text'] as $index => $answer)
                                                        <div class="mb-2">
                                                            <strong>Option {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ $answer ?: '(No answer provided)' }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    @php
                                                        $studentAnswerIndex = is_numeric($result['student_answer_text']) ? intval($result['student_answer_text']) : null;
                                                        $studentAnswerText = $studentAnswerIndex !== null && isset($options[$studentAnswerIndex]) ? $options[$studentAnswerIndex] : $result['student_answer_text'];
                                                    @endphp
                                                    <div class="mb-2">
                                                        <strong>Selected Option:</strong> 
                                                        <span class="text-dark">{{ $studentAnswerText ?: '(No answer provided)' }}</span>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'mcq_multiple')
                                                @php
                                                    $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                                                    $subQuestions = $questionData['sub_questions'] ?? [];
                                                @endphp
                                                @if(is_array($result['student_answer_text']))
                                                    @foreach($result['student_answer_text'] as $index => $answer)
                                                        @php
                                                            $subQuestion = $subQuestions[$index] ?? [];
                                                            $options = $subQuestion['options'] ?? [];
                                                            $answerText = $answer;
                                                            if (is_array($answer)) {
                                                                $selectedOptions = [];
                                                                foreach ($answer as $optionIndex) {
                                                                    if (isset($options[$optionIndex])) {
                                                                        $selectedOptions[] = $options[$optionIndex];
                                                                    }
                                                                }
                                                                $answerText = !empty($selectedOptions) ? implode(', ', $selectedOptions) : '(No answer provided)';
                                                            } elseif (is_numeric($answer) && isset($options[$answer])) {
                                                                $answerText = $options[$answer];
                                                            }
                                                        @endphp
                                                        <div class="mb-2">
                                                                                                                    <strong>Sub-question {{ chr(97 + intval($index)) }}):</strong> 
                                                        <span class="text-dark">{{ $answerText ?: '(No answer provided)' }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <strong>Answer:</strong> 
                                                        <span class="text-dark">{{ $result['student_answer_text'] ?: '(No answer provided)' }}</span>
                                                    </div>
                                                @endif
                                                                                        @else
                                                @if(is_array($result['student_answer_text']))
                                                    @foreach($result['student_answer_text'] as $index => $answer)
                                                        <div class="mb-2">
                                                            <strong>Blank {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ $answer ?: '(No answer provided)' }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <strong>Answer:</strong> 
                                                        <span class="text-dark">{{ $result['student_answer_text'] ?: '(No answer provided)' }}</span>
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-dark">{{ $result['student_answer_text'] ?: '(No answer provided)' }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success"><i class="fas fa-check me-2"></i>Correct Answer:</h6>
                                    <div class="p-3 bg-light rounded border-start border-success border-3">
                                        @if(is_array($result['correct_answer_text']))
                                            @php
                                                $questionType = $question->questionType->name ?? '';
                                            @endphp
                                            @if($questionType === 'audio_picture_match')
                                                @foreach($result['correct_answer_text'] as $audioIndex => $answerText)
                                                    <div class="mb-2">
                                                        <strong>Audio {{ intval($audioIndex) + 1 }}:</strong> 
                                                        <span class="text-dark">{{ $answerText }}</span>
                                                    </div>
                                                @endforeach
                                            @elseif($questionType === 'picture_mcq' || $questionType === 'audio_image_text_single' || $questionType === 'audio_image_text_multiple')
                                                @if(is_array($result['correct_answer_text']))
                                                    @foreach($result['correct_answer_text'] as $imageIndex => $answerText)
                                                        <div class="mb-2">
                                                            <span class="text-dark">{{ $answerText }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <span class="text-dark">{{ $result['correct_answer_text'] }}</span>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'audio_mcq_single')
                                                @php
                                                    $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                                                    $subQuestions = $questionData['sub_questions'] ?? [];
                                                @endphp
                                                @if(is_array($result['correct_answer_text']))
                                                    @foreach($result['correct_answer_text'] as $index => $answer)
                                                        @php
                                                            $subQuestion = $subQuestions[$index] ?? [];
                                                            $options = $subQuestion['options'] ?? [];
                                                            $answerIndex = is_numeric($answer) ? intval($answer) : null;
                                                            $answerText = $answerIndex !== null && isset($options[$answerIndex]) ? $options[$answerIndex] : $answer;
                                                        @endphp
                                                        <div class="mb-2">
                                                            <strong>Question {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ $answerText }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <strong>Correct Answer:</strong> 
                                                        <span class="text-dark">{{ $result['correct_answer_text'] }}</span>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'true_false_multiple')
                                                @if(is_array($result['correct_answer_text']))
                                                    @foreach($result['correct_answer_text'] as $index => $answer)
                                                        <div class="mb-2">
                                                            <strong>Statement {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ ucfirst($answer) }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <strong>Correct Answer:</strong> 
                                                        <span class="text-dark">{{ ucfirst($result['correct_answer_text']) }}</span>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'statement_match')
                                                @if(is_array($result['correct_answer_text']))
                                                    @foreach($result['correct_answer_text'] as $index => $matchAnswer)
                                                        <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-success me-2">{{ intval($index) + 1 }}</span>
                                                                <span class="text-dark fw-bold">{{ $matchAnswer }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-success me-2">1</span>
                                                            <span class="text-dark fw-bold">{{ $result['correct_answer_text'] }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'opinion')
                                                <div class="p-3 bg-green-50 rounded border">
                                                    <p class="text-dark">{{ is_array($result['correct_answer_text']) ? implode(' ', $result['correct_answer_text']) : $result['correct_answer_text'] }}</p>
                                                </div>
                                            @elseif($questionType === 'reorder')
                                                <div class="p-3 bg-green-50 rounded border">
                                                    <p class="text-dark">{{ is_array($result['correct_answer_text']) ? implode(' ', $result['correct_answer_text']) : $result['correct_answer_text'] }}</p>
                                                </div>
                                            @elseif($questionType === 'form_fill' || $questionType === 'audio_fill_blank' || $questionType === 'picture_fill_blank' || $questionType === 'video_fill_blank')
                                                @if(is_array($result['correct_answer_text']))
                                                    @foreach($result['correct_answer_text'] as $index => $answer)
                                                        <div class="mb-2">
                                                            <strong>Blank {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ is_array($answer) ? implode(', ', $answer) : $answer }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="p-3 bg-green-50 rounded border">
                                                        <p class="text-dark">{{ $result['correct_answer_text'] }}</p>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'mcq_single')
                                                @php
                                                    $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                                                    $options = $questionData['options'] ?? [];
                                                @endphp
                                                @if(is_array($result['correct_answer_text']))
                                                    @foreach($result['correct_answer_text'] as $index => $answer)
                                                        <div class="mb-2">
                                                            <strong>Option {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ $answer }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    @php
                                                        $correctAnswerIndex = is_numeric($result['correct_answer_text']) ? intval($result['correct_answer_text']) : null;
                                                        $correctAnswerText = $correctAnswerIndex !== null && isset($options[$correctAnswerIndex]) ? $options[$correctAnswerIndex] : $result['correct_answer_text'];
                                                    @endphp
                                                    <div class="mb-2">
                                                        <strong>Correct Option:</strong> 
                                                        <span class="text-dark">{{ $correctAnswerText }}</span>
                                                    </div>
                                                @endif
                                            @elseif($questionType === 'mcq_multiple')
                                                @php
                                                    $questionData = is_string($question->question_data) ? json_decode($question->question_data, true) : ($question->question_data ?? []);
                                                    $subQuestions = $questionData['sub_questions'] ?? [];
                                                @endphp
                                                @if(is_array($result['correct_answer_text']))
                                                    @foreach($result['correct_answer_text'] as $index => $answer)
                                                        @php
                                                            $subQuestion = $subQuestions[$index] ?? [];
                                                            $options = $subQuestion['options'] ?? [];
                                                            $answerText = $answer;
                                                            if (is_array($answer)) {
                                                                $selectedOptions = [];
                                                                foreach ($answer as $optionIndex) {
                                                                    if (isset($options[$optionIndex])) {
                                                                        $selectedOptions[] = $options[$optionIndex];
                                                                    }
                                                                }
                                                                $answerText = !empty($selectedOptions) ? implode(', ', $selectedOptions) : '';
                                                            } elseif (is_numeric($answer) && isset($options[$answer])) {
                                                                $answerText = $options[$answer];
                                                            }
                                                        @endphp
                                                        <div class="mb-2">
                                                            <strong>Sub-question {{ chr(97 + intval($index)) }}):</strong> 
                                                            <span class="text-dark">{{ $answerText }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <strong>Correct Answer:</strong> 
                                                        <span class="text-dark">{{ $result['correct_answer_text'] }}</span>
                                                    </div>
                                                @endif
                                            @else
                                                @if(is_array($result['correct_answer_text']))
                                                    @foreach($result['correct_answer_text'] as $index => $answer)
                                                        <div class="mb-2">
                                                            <strong>Blank {{ intval($index) + 1 }}:</strong> 
                                                            <span class="text-dark">{{ is_array($answer) ? implode(', ', $answer) : $answer }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-2">
                                                        <strong>Correct Answer:</strong> 
                                                        <span class="text-dark">{{ $result['correct_answer_text'] }}</span>
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-dark">{{ $result['correct_answer_text'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-trophy text-warning" style="font-size: 4rem;"></i>
                                <h4 class="mt-3 text-success">Excellent Work!</h4>
                                <p class="text-muted">You've answered this question correctly.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="goBackToQuestions()">
                            <i class="fas fa-arrow-left me-2"></i>Back to Questions
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-show the modal
                const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
                resultModal.show();
                
                // Clear the show_result_modal flag after showing the modal
                // This prevents the modal from showing again on page refresh
                fetch('{{ route("filament.student.clear_result_modal") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                }).catch(error => console.log('Error clearing modal flag:', error));
            });
            
            function goBackToQuestions() {
                // Redirect to questions listing page with proper parameters
                const url = new URL(window.location.href);
                const questionId = url.pathname.split('/').pop();
                
                // Get the question details from the page or use default values
                const courseId = {{ $question->course_id ?? 1 }};
                const subjectId = {{ $question->subject_id ?? 1 }};
                const dayId = {{ $question->day_id ?? 1 }};
                
                const questionsUrl = `{{ route('filament.student.pages.questions') }}?course=${courseId}&subject=${subjectId}&day=${dayId}`;
                window.location.href = questionsUrl;
            }
        </script>
    @endif

    <!-- Image Preview Modal -->
    <div id="imagePreviewModal" class="image-preview-modal" style="display: none;">
        <div class="image-preview-overlay" onclick="closeImagePreview()"></div>
        <div class="image-preview-content">
            <button class="image-preview-close" onclick="closeImagePreview()">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="previewImage" src="" alt="Preview" class="preview-image">
            <div class="image-preview-info">
                <span id="previewImageTitle">Image Preview</span>
            </div>
        </div>
    </div>
    <div class="modern-answer-form">
        <!-- Modern Answer Card -->
        <div class="modern-answer-card">
            <div class="card-content">
                <!-- Quick Actions Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 p-3 sm:p-4 bg-gray-50 rounded-lg gap-3 sm:gap-0">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                        <button onclick="history.back()" class="flex items-center justify-center sm:justify-start px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Go Back
                        </button>
                        <a href="{{ route('filament.student.pages.questions') }}" class="flex items-center justify-center sm:justify-start px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-list mr-2"></i>
                            All Questions
                        </a>
                    </div>
                    <div class="flex items-center justify-center sm:justify-end space-x-2 text-xs sm:text-sm text-gray-600 bg-white sm:bg-transparent px-3 py-2 sm:px-0 sm:py-0 rounded-lg sm:rounded-none">
                        <i class="fas fa-clock text-sm sm:text-base"></i>
                        <span class="font-medium sm:font-normal">Take your time</span>

                    </div>
                </div>

                <!-- Question Header Section -->
                <div class="question-header-section">
                    <div class="question-meta">
                        <span class="question-type-badge">{{ ucfirst(str_replace('_', ' ', $type ?? 'Question')) }}</span>
                        @if($question->points)
                            <span class="points-badge">{{ $question->points }} {{ $question->points == 1 ? 'Point' : 'Points' }}</span>
                        @endif
                    </div>
                    <h1 class="question-title">{{ $question->instruction ?? 'No question text available' }}</h1>
                    
                    @if($question->explanation_file)
                        <div class="explanation-file-section">
                            <div class="file-download-card">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <p class="file-title">Additional Reference</p>
                                    <a href="{{ asset('storage/' . $question->explanation_file) }}" target="_blank" class="file-link">
                                        View Explanation File
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($question->explanation && !$question->explanation_file)
                        <div class="explanation-text-section">
                            <div class="explanation-card">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="explanation-title">Explanation</p>
                                    @php
                                        $explanationPath = $question->explanation;
                                        $isAudioFile = preg_match('/\.(mp3|wav|ogg|m4a)$/i', $explanationPath);
                                        $isVideoFile = preg_match('/\.(mp4|avi|mov|wmv)$/i', $explanationPath);
                                    @endphp
                                    
                                    @if($isAudioFile)
                                        <div class="audio-player-section">
                                            <audio controls class="audio-player">
                                                <source src="{{ asset('storage/' . $explanationPath) }}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                    @elseif($isVideoFile)
                                        <div class="video-player-section">
                                            <video controls class="video-player">
                                                <source src="{{ asset('storage/' . $explanationPath) }}" type="video/mp4">
                                                Your browser does not support the video element.
                                            </video>
                                        </div>
                                    @else
                                        <p class="explanation-text">{{ $question->explanation }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Answer Form -->
                <form method="POST" action="{{ 
                    $formAction ?? (
                        isset($test) 
                            ? route('filament.student.pages.tests.question.submit', [$test, $question]) 
                            : route('filament.student.submit_answer', ['id' => $question->id]) 
                    )
                }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Add hidden input for re-attempt flag -->
                    <input type="hidden" name="is_reattempt" value="{{ $isReAttempt ? '1' : '0' }}">
                    
                    @if($isReadOnly)
                        <div class="mt-8 mb-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-medium text-yellow-800">Answer Submitted</h3>
                                        <p class="text-sm text-yellow-700 mt-1">Your answer has been submitted.</p>
                                    </div>
                                </div>
                                
                                <!-- Re-attempt Button - Only show for non-test questions -->
                                @if(!isset($test))
                                <a href="{{ route('student.questions.answer', $question->id) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Re-attempt
                                </a>
                                @endif
                            </div>

                            <!-- Existing answer view code follows -->

                            <!-- MCQ Single Answer View -->
                            @if($type === 'mcq_single')
                                <div class="answer-section view-mode-section">
                                    <h3 class="section-title">Choose One Option</h3>
                                    <div class="mcq-options-grid">
                                        @php
                                            $submittedAnswerIndex = is_string($studentAnswer->answer_data) 
                                                ? intval($studentAnswer->answer_data) 
                                                : $studentAnswer->answer_data;
                                        @endphp
                                        @foreach(($qdata['options'] ?? []) as $i => $option)
                                            <label class="mcq-option-item {{ $i === $submittedAnswerIndex ? 'submitted-answer' : '' }}">
                                                <input type="radio" name="answer" value="{{ $i }}" class="mcq-radio" 
                                                       {{ $i === $submittedAnswerIndex ? 'checked disabled' : 'disabled' }}>
                                                <div class="option-card">
                                                    <div class="option-indicator">{{ chr(65 + $i) }}</div>
                                                    <span class="option-text">{{ $option }}</span>
                                                    <div class="selection-mark">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- MCQ Multiple Answer View -->
                            @if($type === 'mcq_multiple')
                                <div class="answer-section view-mode-section">
                                    <h3 class="section-title">Multiple Choice Questions</h3>
                                    <div class="info-banner">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Select all correct options for each sub-question below.
                                    </div>
                                    
                                    @php
                                        $submittedAnswers = is_string($studentAnswer->answer_data) 
                                            ? json_decode($studentAnswer->answer_data, true) 
                                            : $studentAnswer->answer_data;
                                    @endphp
                                    
                                    @foreach(($qdata['sub_questions'] ?? []) as $subIdx => $sub)
                                        <div class="sub-question-item" data-sub-question="{{ $subIdx }}">
                                            <h4 class="sub-question-title">{{ chr(97+$subIdx) }}) {{ $sub['question'] ?? '' }}</h4>
                                            <div class="checkbox-options-grid">
                                                @foreach(($sub['options'] ?? []) as $optIdx => $opt)
                                                    <label class="checkbox-option-item" data-option="{{ $optIdx }}">
                                                        <input type="checkbox" 
                                                               name="answer[{{ $subIdx }}][]" 
                                                               value="{{ $optIdx }}" 
                                                               class="checkbox-input"
                                                               data-sub-question="{{ $subIdx }}"
                                                               data-option="{{ $optIdx }}">
                                                        <div class="checkbox-card">
                                                            <div class="checkbox-indicator">
                                                                <svg class="w-3 h-3 checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                            </div>
                                                            <span class="option-text">{{ $opt }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- True/False Single View -->
                            @if($type === 'true_false')
                                <div class="answer-section view-mode-section">
                                    <h3 class="section-title">True or False</h3>
                                    <div class="true-false-grid">
                                        @php
                                            $submittedAnswer = is_string($studentAnswer->answer_data) 
                                                ? $studentAnswer->answer_data 
                                                : ($studentAnswer->answer_data ?? null);
                                        @endphp
                                        <label class="tf-option-item true-option {{ $submittedAnswer === 'true' ? 'submitted-answer' : '' }}">
                                            <input type="radio" name="answer" value="true" class="tf-radio"
                                                   {{ $submittedAnswer === 'true' ? 'checked disabled' : 'disabled' }}>
                                            <div class="tf-card">
                                                <div class="tf-indicator">
                                                    <svg class="w-4 h-4 checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <span class="tf-text">TRUE</span>
                                            </div>
                                        </label>
                                        
                                        <label class="tf-option-item false-option {{ $submittedAnswer === 'false' ? 'submitted-answer' : '' }}">
                                            <input type="radio" name="answer" value="false" class="tf-radio"
                                                   {{ $submittedAnswer === 'false' ? 'checked disabled' : 'disabled' }}>
                                            <div class="tf-card">
                                                <div class="tf-indicator">
                                                    <svg class="w-4 h-4 checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <span class="tf-text">FALSE</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <!-- True/False Multiple View -->
                            @if($type === 'true_false_multiple')
                                <div class="answer-section view-mode-section">
                                    <h3 class="section-title">True or False Questions</h3>
                                    <div class="info-banner">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Answer each statement as True or False.
                                    </div>
                                    
                                    @php
                                        $submittedAnswers = is_string($studentAnswer->answer_data) 
                                            ? json_decode($studentAnswer->answer_data, true) 
                                            : $studentAnswer->answer_data;
                                    @endphp
                                    
                                    @foreach(($question->true_false_questions ?? $qdata['questions'] ?? []) as $i => $tf)
                                        <div class="sub-question-item">
                                            <h4 class="sub-question-title">{{ chr(97+$i) }}) {{ $tf['statement'] ?? '' }}</h4>
                                            <div class="true-false-grid">
                                                <label class="tf-option-item true-option {{ 
                                                    isset($submittedAnswers[$i]) && $submittedAnswers[$i] === 'true' 
                                                    ? 'submitted-answer' : '' 
                                                }}">
                                                    <input type="radio" name="answer[{{ $i }}]" value="true" class="tf-radio"
                                                           {{ 
                                                           isset($submittedAnswers[$i]) && $submittedAnswers[$i] === 'true' 
                                                           ? 'checked disabled' : 'disabled' 
                                                           }}>
                                                    <div class="tf-card">
                                                        <div class="tf-indicator">
                                                            <svg class="w-4 h-4 checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                        <span class="tf-text">TRUE</span>
                                                    </div>
                                                </label>
                                                
                                                <label class="tf-option-item false-option {{ 
                                                    isset($submittedAnswers[$i]) && $submittedAnswers[$i] === 'false' 
                                                    ? 'submitted-answer' : '' 
                                                }}">
                                                    <input type="radio" name="answer[{{ $i }}]" value="false" class="tf-radio"
                                                           {{ 
                                                           isset($submittedAnswers[$i]) && $submittedAnswers[$i] === 'false' 
                                                           ? 'checked disabled' : 'disabled' 
                                                           }}>
                                                    <div class="tf-card">
                                                        <div class="tf-indicator">
                                                            <svg class="w-4 h-4 checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                        <span class="tf-text">FALSE</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Show the answer form as usual -->
                        <!-- MCQ Single Answer -->
                        @if($type === 'mcq_single')
                            <div class="answer-section">
                                <h3 class="section-title">Choose One Option</h3>
                                <div class="mcq-options-grid">
                                    @foreach(($qdata['options'] ?? []) as $i => $option)
                                        <label class="mcq-option-item">
                                            <input type="radio" name="answer" value="{{ $i }}" class="mcq-radio">
                                            <div class="option-card">
                                                <div class="option-indicator">{{ chr(65 + $i) }}</div>
                                                <span class="option-text">{{ $option }}</span>
                                                <div class="selection-mark">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- MCQ Multiple Answers -->
                        @if($type === 'mcq_multiple')
                            <div class="answer-section">
                                <h3 class="section-title">Multiple Choice Questions</h3>
                                <div class="info-banner">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Select all correct options for each sub-question below.
                                </div>
                                
                                @foreach(($qdata['sub_questions'] ?? []) as $subIdx => $sub)
                                    <div class="sub-question-item" data-sub-question="{{ $subIdx }}">
                                        <h4 class="sub-question-title">{{ chr(97+$subIdx) }}) {{ $sub['question'] ?? '' }}</h4>
                                        <div class="mcq-options-grid">
                                            @foreach(($sub['options'] ?? []) as $optIdx => $opt)
                                                <label class="mcq-option-item" data-option="{{ $optIdx }}">
                                                    <input type="checkbox" 
                                                           name="answer[{{ $subIdx }}][]" 
                                                           value="{{ $optIdx }}" 
                                                           class="mcq-checkbox">
                                                    <div class="option-card">
                                                        <div class="option-indicator">{{ chr(65 + $optIdx) }}</div>
                                                        <span class="option-text">{{ $opt }}</span>
                                                        <div class="selection-mark">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- True/False Single -->
                        @if($type === 'true_false')
                            <div class="answer-section">
                                <h3 class="section-title">True or False</h3>
                                <div class="true-false-grid">
                                    <label class="tf-option-item true-option">
                                        <input type="radio" name="answer" value="true" class="tf-radio">
                                        <div class="tf-card">
                                            <div class="tf-indicator">
                                                <svg class="w-4 h-4 checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <span class="tf-text">TRUE</span>
                                        </div>
                                    </label>
                                    
                                    <label class="tf-option-item false-option">
                                        <input type="radio" name="answer" value="false" class="tf-radio">
                                        <div class="tf-card">
                                            <div class="tf-indicator">
                                                <svg class="w-4 h-4 checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <span class="tf-text">FALSE</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endif

                        <!-- True/False Multiple -->
                        @if($type === 'true_false_multiple')
                            <div class="answer-section">
                                <h3 class="section-title">True or False Questions</h3>
                                <div class="info-banner">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Answer each statement as True or False.
                                </div>
                                
                                @foreach(($question->true_false_questions ?? $qdata['questions'] ?? []) as $i => $tf)
                                    <div class="sub-question-item">
                                        <h4 class="sub-question-title">{{ chr(97+$i) }}) {{ $tf['statement'] ?? '' }}</h4>
                                        <div class="true-false-grid">
                                            <label class="tf-option-item true-option">
                                                <input type="radio" name="answer[{{ $i }}]" value="true" class="tf-radio">
                                                <div class="tf-card">
                                                    <div class="tf-indicator">
                                                        <svg class="w-4 h-4 checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="tf-text">TRUE</span>
                                                </div>
                                            </label>
                                            
                                            <label class="tf-option-item false-option">
                                                <input type="radio" name="answer[{{ $i }}]" value="false" class="tf-radio">
                                                <div class="tf-card">
                                                    <div class="tf-indicator">
                                                        <svg class="w-4 h-4 checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="tf-text">FALSE</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Fill in the Blanks -->
                        @if($type === 'form_fill')
                            <div class="answer-section">
                                <h3 class="section-title">Fill in the Blanks</h3>
                                @php
                                    $paragraph = $question->form_fill_paragraph ?? $qdata['paragraph'] ?? '';
                                    $blanks = preg_match_all('/___/', $paragraph, $matches);
                                    $options = $qdata['options'] ?? [];
                                @endphp
                                
                                <div class="paragraph-display-card">
                                    <h4 class="paragraph-title">Complete the passage below:</h4>
                                    <div class="paragraph-content">{{ $paragraph }}</div>
                                </div>
                                
                                @if($blanks)
                                    <div class="blanks-grid">
                                        @for($i=0; $i<$blanks; $i++)
                                            <div class="blank-input-item">
                                                <label class="blank-label">Blank {{ $i+1 }}</label>
                                                <input type="text" name="answer[{{ $i }}]" class="blank-input" placeholder="Your answer...">
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                                
                                @if($options)
                                    <div class="options-reference-card">
                                        <h4 class="options-title">Available Options:</h4>
                                        <div class="options-tags">
                                            @foreach($options as $option)
                                                <span class="option-tag">{{ $option }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Reorder/Rearrange -->
                        @if($type === 'reorder')
                            <div class="answer-section">
                                <h3 class="section-title">Sentence Reordering</h3>
                                
                                <div class="info-banner">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Arrange the fragments below to form the correct sentence.
                                </div>
                                @php $fragments = $question->reorder_fragments ?? $qdata['fragments'] ?? []; @endphp
                                <div class="fragments-display-card">
                                    <h4 class="fragments-title">Fragments to arrange:</h4>
                                    <div class="fragments-grid">
                                        @foreach($fragments as $index => $frag)
                                            <div class="fragment-item">
                                                <span class="fragment-number">{{ $index + 1 }}</span>
                                                <span class="fragment-text">{{ $frag }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="reorder-input-item">
                                    <label class="reorder-label">Enter the correct order:</label>
                                    <input type="text" name="answer" class="reorder-input" placeholder="e.g., 2,1,3,4">
                                    <div class="input-hint">Separate numbers with commas</div>
                                </div>
                            </div>
                        @endif

                        <!-- Statement Match -->
                        @if($type === 'statement_match')
                            <div class="answer-section">
                                <h3 class="section-title">Match the Following</h3>
                                @php
                                    $left = $question->left_options ?? $qdata['left_options'] ?? [];
                                    $right = $question->right_options ?? $qdata['right_options'] ?? [];
                                @endphp
                                <div class="matching-layout">
                                    <div class="left-column">
                                        <h4 class="column-title">Items to Match</h4>
                                        <div class="left-items">
                                            @foreach($left as $i => $l)
                                                <div class="left-item-card">
                                                    <span class="item-number">{{ $i + 1 }}</span>
                                                    <span class="item-text">{{ $l }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="right-column">
                                        <h4 class="column-title">Your Answers</h4>
                                        <div class="matching-inputs">
                                            @foreach($left as $i => $l)
                                                <div class="match-input-item">
                                                    <label class="match-label">Item {{ $i + 1 }} matches with:</label>
                                                    <input type="number" name="answer[{{ $i }}]" min="1" max="{{ count($right) }}" class="match-input" placeholder="Enter number">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="reference-card">
                                    <h4 class="reference-title">Answer Options:</h4>
                                    <div class="reference-grid">
                                        @foreach($right as $j => $r)
                                            <div class="reference-item">
                                                <span class="ref-number">{{ $j+1 }}</span>
                                                <span class="ref-text">{{ $r }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Opinion/Essay -->
                        @if($type === 'opinion')
                            @php
                                $isSpeakingSubject = $question->subject && strtolower($question->subject->name) === 'speaking';
                            @endphp
                            <div class="answer-section">
                                <h3 class="section-title">
                                    @if($isSpeakingSubject)
                                        Speaking Response
                                    @else
                                        Essay Response
                                    @endif
                                </h3>
                                <div class="info-banner">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @if($isSpeakingSubject)
                                        Provide either an audio/video response OR a written response (or both). At least one is required.
                                    @else
                                        Write your detailed response below. Express your thoughts clearly and completely.
                                    @endif
                                </div>
                                
                                @if($isSpeakingSubject)
                                    <div class="file-upload-section">
                                        <label class="file-upload-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 6.464a9 9 0 010 12.728m-4.242-4.242a3 3 0 010 4.242m6.364-6.364a5 5 0 010 7.072m-2.828-2.828a7 7 0 010 9.899"/>
                                            </svg>
                                            Audio/Video Response (Optional)
                                        </label>
                                        <input type="file" name="audio_video_file" accept="audio/*,video/*" class="file-upload-input">
                                        <div class="file-upload-info">
                                            <p class="text-sm text-gray-600">
                                                Supported formats: MP3, WAV, MP4, WebM, OGG | Max size: 50MB
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="essay-input-item">
                                    <label class="essay-label">
                                        @if($isSpeakingSubject)
                                            Written Response (Optional - for additional context)
                                        @else
                                            Your Response
                                        @endif
                                    </label>
                                    <textarea name="answer" rows="6" class="essay-textarea" 
                                        @if(!$isSpeakingSubject) required @endif
                                        placeholder="@if($isSpeakingSubject)Write your response here (required if no audio/video file is uploaded)...@elseWrite your detailed response here...@endif">{{ old('answer', isset($existingAnswer) && $existingAnswer ? (is_array($existingAnswer->answer_data) ? implode(' ', $existingAnswer->answer_data) : $existingAnswer->answer_data) : (is_array($answer) ? implode(' ', $answer) : $answer)) }}</textarea>
                                    <div class="character-info">
                                        <span class="char-count">0 characters</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Picture MCQ -->
                        @if($type === 'picture_mcq')
                            <div class="answer-section">
                                <h3 class="section-title">Image to Text Matching</h3>
                                <div class="info-banner">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Match each image to the correct text option. <strong>Click images to view full size.</strong>
                                </div>
                                
                                @php
                                    $images = $question->picture_mcq_images ?? $qdata['images'] ?? [];
                                    $right = $question->picture_mcq_right_options ?? $qdata['right_options'] ?? [];
                                @endphp
                                
                                <div class="image-matching-grid">
                                    @foreach($images as $i => $img)
                                        <div class="image-match-item">
                                            <div class="image-container">
                                                <a href="{{ asset('storage/'.$img) }}" target="_blank" rel="noopener noreferrer" title="Click to view full size image">
                                                    <img src="{{ asset('storage/'.$img) }}" alt="Image {{ $i+1 }}" class="match-image cursor-pointer hover:opacity-80 transition-opacity duration-200">
                                                </a>
                                                <div class="image-badge">Image {{ $i+1 }}</div>
                                            </div>
                                            
                                            <div class="select-area">
                                                <label class="select-label">Select answer:</label>
                                                <select name="answer[{{ $i }}]" class="image-select">
                                                    <option value="">Choose option...</option>
                                                    @foreach($right as $j => $opt)
                                                        <option value="{{ $j }}">{{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Audio MCQ Single -->
                        @if($type === 'audio_mcq_single')
                            <div class="answer-section">
                                <h3 class="section-title">Audio Questions</h3>
                                <div class="info-banner">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Listen to the audio carefully and answer the questions below.
                                </div>
                                
                                @if(!empty($qdata['audio_file']))
                                    <div class="audio-player-item">
                                        <div class="audio-header">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                            </svg>
                                            <span class="audio-title">Listen to Audio</span>
                                        </div>
                                        <audio controls class="audio-player">
                                            <source src="{{ asset('storage/'.$qdata['audio_file']) }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                @endif
                                
                                @foreach(($qdata['sub_questions'] ?? []) as $subIdx => $sub)
                                    <div class="sub-question-item">
                                        <h4 class="sub-question-title">{{ chr(97+$subIdx) }}) {{ $sub['question'] ?? '' }}</h4>
                                        <div class="mcq-options-grid">
                                            @foreach(($sub['options'] ?? []) as $optIdx => $opt)
                                                <label class="mcq-option-item">
                                                    <input type="radio" name="answer[{{ $subIdx }}]" value="{{ $optIdx }}" class="mcq-radio">
                                                    <div class="option-card">
                                                        <div class="option-indicator">{{ chr(65 + $optIdx) }}</div>
                                                        <span class="option-text">{{ $opt }}</span>
                                                        <div class="selection-mark">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Audio Image Text Single -->
                        @if($type === 'audio_image_text_single')
                            <div class="answer-section">
                                <h3 class="section-title">Audio with Image Matching</h3>
                                <div class="info-banner">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Listen to the audio and match each image to the correct text. <strong>Click images to view full size.</strong>
                                </div>
                                
                                @if(!empty($qdata['audio_file']))
                                    <div class="audio-player-item">
                                        <div class="audio-header">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                            </svg>
                                            <span class="audio-title">Listen to Audio</span>
                                        </div>
                                        <audio controls class="audio-player">
                                            <source src="{{ asset('storage/'.$qdata['audio_file']) }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @endif
                                
                                @php
                                    $images = $qdata['images'] ?? [];
                                    $right = $qdata['right_options'] ?? [];
                                @endphp
                                
                                <div class="image-matching-grid">
                                    @foreach($images as $i => $img)
                                        <div class="image-match-item">
                                            <div class="image-container">
                                                <a href="{{ asset('storage/'.$img) }}" target="_blank" rel="noopener noreferrer" title="Click to view full size image">
                                                    <img src="{{ asset('storage/'.$img) }}" alt="Image {{ $i+1 }}" class="match-image cursor-pointer hover:opacity-80 transition-opacity duration-200">
                                                </a>
                                                <div class="image-badge">Image {{ $i+1 }}</div>
                                            </div>
                                            
                                            <div class="select-area">
                                                <label class="select-label">Select answer:</label>
                                                <select name="answer[{{ $i }}]" class="image-select">
                                                    <option value="">Choose option...</option>
                                                    @foreach($right as $j => $opt)
                                                        <option value="{{ $j }}">{{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Audio Image Text Multiple -->
                        @if($type === 'audio_image_text_multiple')
                            <div class="answer-section">
                                <h3 class="section-title">Audio/Image to Text Matching</h3>
                                <div class="info-banner">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    For each audio/image pair, select the correct text option. <strong>Click images to view full size.</strong>
                                </div>
                                
                                @php
                                    $pairs = $question->audio_image_text_multiple_pairs ?? 
                                             $qdata['audio_pairs'] ?? 
                                             $qdata['image_audio_pairs'] ?? 
                                             $qdata['pairs'] ?? [];
                                    $right = $question->right_options ?? $qdata['right_options'] ?? [];
                                @endphp
                                
                                <div class="audio-image-multiple-grid">
                                    @foreach($pairs as $i => $pair)
                                        <div class="audio-image-multiple-item">
                                            <div class="pair-header">
                                                <h4 class="pair-title">Pair {{ $i + 1 }}</h4>
                                            </div>
                                            
                                            <div class="media-grid">
                                                @if(!empty($pair['audio']))
                                                    <div class="audio-container">
                                                        <div class="media-header">
                                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                            </svg>
                                                            <span class="media-label">Audio</span>
                                                        </div>
                                                        <div class="audio-player-wrapper">
                                                            <audio controls class="audio-player-compact">
                                                            <source src="{{ asset('storage/'.$pair['audio']) }}" type="audio/mpeg">
                                                                Your browser does not support the audio element.
                                                    </audio>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if(!empty($pair['image']))
                                                    <div class="image-container">
                                                        <div class="media-header">
                                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            <span class="media-label">Image</span>
                                                        </div>
                                                        <div class="image-wrapper">
                                                            <a href="{{ asset('storage/'.$pair['image']) }}" target="_blank" rel="noopener noreferrer" title="Click to view full size image">
                                                                <img src="{{ asset('storage/'.$pair['image']) }}" alt="Image {{ $i+1 }}" class="pair-image-optimized cursor-pointer hover:opacity-80 transition-opacity duration-200">
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="select-area">
                                                <label class="select-label">Select matching text option:</label>
                                                <select name="answer[{{ $i }}]" class="pair-select">
                                                    <option value="">Choose option...</option>
                                                    @foreach($right as $j => $opt)
                                                        <option value="{{ $j }}">{{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Audio Picture Match -->
                        @if($type === 'audio_picture_match')
                            <div class="answer-section">
                                <h3 class="section-title">Audio Picture Matching</h3>
                                <div class="info-banner">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Listen to each audio clip and select the matching image. <strong>Click images to view full size.</strong>
                                </div>
                                
                                @php
                                    $audios = $qdata['audios'] ?? [];
                                    $images = $qdata['images'] ?? [];
                                @endphp
                                
                                @if(!empty($audios) && !empty($images))
                                    <div class="audio-picture-match-container">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Audio Files Section -->
                                            <div class="audio-section">
                                                <h4 class="media-section-title">🎵 Audio Files</h4>
                                                <div class="audio-items-grid">
                                                    @foreach($audios as $audioIndex => $audioPath)
                                                        <div class="audio-item-card">
                                                            <div class="audio-item-header">
                                                                <span class="audio-item-label">Audio {{ $audioIndex + 1 }}</span>
                                                            </div>
                                                            <div class="audio-player-container">
                                                                <audio controls class="audio-player">
                                                                    <source src="{{ asset('storage/' . $audioPath) }}" type="audio/mpeg">
                                                                    Your browser does not support the audio element.
                                                                </audio>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                            <!-- Images Section -->
                                            <div class="images-section">
                                                <h4 class="media-section-title">🖼️ Images</h4>
                                                <div class="images-grid">
                                                    @foreach($images as $imageIndex => $imagePath)
                                                        <div class="image-item-card">
                                                            <div class="image-item-header">
                                                                <span class="image-item-label">Image {{ $imageIndex + 1 }}</span>
                                                            </div>
                                                                                                <div class="image-container">
                                            <a href="{{ asset('storage/' . $imagePath) }}" target="_blank" rel="noopener noreferrer" title="Click to view full size image">
                                                <img src="{{ asset('storage/' . $imagePath) }}" alt="Image {{ $imageIndex + 1 }}" class="matching-image cursor-pointer hover:opacity-80 transition-opacity duration-200">
                                            </a>
                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        <!-- Matching Interface -->
                                        <div class="matching-interface">
                                            <h4 class="matching-title">Your Matches</h4>
                                            <div class="matching-grid">
                                                @foreach($audios as $audioIndex => $audioPath)
                                                    <div class="match-row">
                                                        <div class="match-audio-label">
                                                            <span class="audio-number">Audio {{ $audioIndex + 1 }}</span>
                                                        </div>
                                                        <div class="match-arrow">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                            </svg>
                                                        </div>
                                                        <div class="match-image-select">
                                                            <select name="answer[{{ $audioIndex }}]" class="image-select">
                                                                <option value="">Select an image...</option>
                                                                @foreach($images as $imageIndex => $imagePath)
                                                                    <option value="{{ $imageIndex }}">Image {{ $imageIndex + 1 }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="error-message">
                                        <p>This question does not have proper audio and image files configured.</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Audio/Picture/Video Fill Blank -->
                        @if($type === 'audio_fill_blank' || $type === 'picture_fill_blank' || $type === 'video_fill_blank')
                            <div class="answer-section">
                                <h3 class="section-title">
                                    @if($type === 'audio_fill_blank') Audio @elseif($type === 'picture_fill_blank') Picture @else Video @endif
                                    Fill in the Blanks
                                </h3>
                                
                                <!-- Media Display -->
                                @if($type === 'audio_fill_blank' && !empty($qdata['audio_file']))
                                    <div class="audio-player-item">
                                        <div class="audio-header">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                            </svg>
                                            <span class="audio-title">Listen to Audio</span>
                                        </div>
                                        <audio controls class="audio-player">
                                            <source src="{{ asset('storage/'.$qdata['audio_file']) }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @elseif($type === 'picture_fill_blank' && !empty($qdata['image_file']))
                                    <div class="image-display-item">
                                        <div class="image-header">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="image-title">Reference Image (Click to view full size)</span>
                                        </div>
                                        <a href="{{ asset('storage/'.$qdata['image_file']) }}" target="_blank" rel="noopener noreferrer" title="Click to view full size image">
                                            <img src="{{ asset('storage/'.$qdata['image_file']) }}" alt="Reference Image" class="reference-image cursor-pointer hover:opacity-80 transition-opacity duration-200">
                                        </a>
                                    </div>
                                @elseif($type === 'video_fill_blank' && !empty($qdata['video_file']))
                                    <div class="video-player-container">
                                        <div class="video-header">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="video-title">Watch Video</span>
                                        </div>
                                        <div class="video-player-wrapper">
                                        <video controls class="video-player">
                                            <source src="{{ asset('storage/'.$qdata['video_file']) }}" type="video/mp4">
                                        </video>
                                        </div>
                                    </div>
                                @endif
                                
                                @php
                                    $paragraph = $question->audio_fill_paragraph ?? $question->picture_fill_paragraph ?? $question->video_fill_paragraph ?? $qdata['paragraph'] ?? '';
                                    $blanks = preg_match_all('/___/', $paragraph, $matches);
                                    $options = $qdata['options'] ?? []; // ADD THIS LINE
                                @endphp
                                
                                <div class="paragraph-display-card">
                                    <h4 class="paragraph-title">Fill in the blanks:</h4>
                                    <div class="paragraph-content">{{ $paragraph }}</div>
                                </div>
                                
                                @if($blanks)
                                    <div class="blanks-grid">
                                        @for($i=0; $i<$blanks; $i++)
                                            <div class="blank-input-item">
                                                <label class="blank-label">Blank {{ $i+1 }}</label>
                                                <input type="text" name="answer[{{ $i }}]" class="blank-input" placeholder="Your answer..."
                                                    @if(isset($answer[$i])) value="{{ $answer[$i] }}" @endif
                                                    @if($isReadOnly) disabled @endif>
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                                
                                <!-- ADD THIS OPTIONS REFERENCE SECTION -->
                                @if($options)
                                    <div class="options-reference-card">
                                        <h4 class="options-title">Available Options:</h4>
                                        <div class="options-tags">
                                            @foreach($options as $option)
                                                <span class="option-tag">{{ $option }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="submit-section">
                            @php
                                // For tests, only show view mode if there's actually a submitted answer with data
                                $showViewModeButtons = $isViewMode && 
                                    (!isset($test) || 
                                     (isset($test) && isset($submittedAnswer) && !empty($submittedAnswer) && 
                                      null !== ($submittedAnswer->answer_data ?? $submittedAnswer['answer_data'] ?? null) && ($submittedAnswer->answer_data ?? $submittedAnswer['answer_data'] ?? null) !== null && ($submittedAnswer->answer_data ?? $submittedAnswer['answer_data'] ?? null) !== ''));
                                
                                // Show submit button if not in view mode, not read only, and not showing view mode buttons
                                $showSubmitButton = !$showViewModeButtons && !$isReadOnly;
                            @endphp
                            
                            @if($showViewModeButtons)
                                <div class="flex justify-between space-x-4">
                                    <a href="{{ isset($test) ? route('filament.student.pages.test-questions') : route('filament.student.pages.questions') }}" class="btn btn-secondary flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                        </svg>
                                        All {{ isset($test) ? 'Tests' : 'Questions' }}
                                    </a>
                                    @if(isset($test))
                                        <a href="{{ route('filament.student.pages.tests.show', $test) }}" class="btn btn-primary flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Back to Test
                                        </a>
                                    @endif
                                </div>
                            @endif
                            
                            @if($showSubmitButton)
                                <button type="submit" class="submit-btn mx-auto" style="max-width:300px;">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Submit Answer
                                </button>
                            @endif
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Answer Form Base Styles */
.modern-answer-form {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.5rem;
}

.modern-answer-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    border: 1px solid #e5e7eb;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-answer-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.card-content {
    padding: 2rem;
}

/* Question Header Styles */
.question-header-section {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f3f4f6;
}

.question-meta {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.question-type-badge {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    padding: 0.375rem 0.875rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.points-badge {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.375rem 0.875rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.question-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
    line-height: 1.4;
}

/* Explanation File */
.explanation-file-section {
    margin-top: 1rem;
}

.file-download-card {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: 1px solid #3b82f6;
    transition: all 0.3s ease;
}

.file-download-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px -4px rgba(59, 130, 246, 0.3);
}

.file-title {
    font-weight: 600;
    color: #1e40af;
    margin: 0;
    font-size: 0.875rem;
}

.file-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
}

/* Explanation Text */
.explanation-text-section {
    margin-top: 1rem;
}

.explanation-card {
    display: inline-flex;
    align-items: flex-start;
    gap: 0.5rem;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: 1px solid #f59e0b;
    transition: all 0.3s ease;
}

.explanation-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px -4px rgba(245, 158, 11, 0.3);
}

.explanation-title {
    font-weight: 600;
    color: #92400e;
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
}

.explanation-text {
    color: #78350f;
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
    white-space: pre-wrap;
}

/* Audio and Video Players */
.audio-player-section,
.video-player-section {
    margin-top: 0.5rem;
}

.audio-player,
.video-player {
    width: 100%;
    max-width: 400px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.audio-player {
    height: 40px;
}

.video-player {
    max-height: 300px;
}

.audio-player::-webkit-media-controls-panel {
    background-color: #f8fafc;
}

.audio-player::-webkit-media-controls-play-button {
    background-color: #3b82f6;
    border-radius: 50%;
}

.audio-player::-webkit-media-controls-play-button:hover {
    background-color: #2563eb;
}

.video-player::-webkit-media-controls-panel {
    background-color: #f8fafc;
}

/* Answer Sections */
.answer-section {
    margin-bottom: 2rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 16px;
    padding: 1.5rem;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.answer-section:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px -4px rgba(59, 130, 246, 0.15);
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title::before {
    content: '';
    width: 3px;
    height: 1.5rem;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border-radius: 2px;
}

/* Info Banner */
.info-banner {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: 1px solid #f59e0b;
    margin-bottom: 1.25rem;
    font-weight: 500;
    color: #92400e;
    font-size: 0.875rem;
}

/* MCQ Styles */
.mcq-options-grid {
    display: grid;
    gap: 0.75rem;
}

.mcq-option-item {
    cursor: pointer;
}

.mcq-radio,
.mcq-checkbox {
    display: none;
}

.option-card {
    display: flex;
    align-items: center;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
    position: relative;
}

.option-card:hover {
    border-color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px -4px rgba(59, 130, 246, 0.2);
}

.mcq-option-item:has(.mcq-radio:checked) .option-card,
.mcq-option-item:has(.mcq-checkbox:checked) .option-card {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.mcq-option-item:has(.mcq-checkbox:checked) .selection-mark {
    background: #10b981;
    border-color: #10b981;
    color: white;
}

.option-indicator {
    width: 2rem;
    height: 2rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.option-text {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    flex: 1;
}

.selection-mark {
    width: 1.5rem;
    height: 1.5rem;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.mcq-option-item:has(.mcq-radio:checked) .selection-mark,
.mcq-option-item:has(.mcq-checkbox:checked) .selection-mark {
    background: #10b981;
    border-color: #10b981;
    color: white;
}

/* Checkbox Styles */
.checkbox-options-grid {
    display: grid;
    gap: 0.5rem;
}

.checkbox-option-item {
    cursor: pointer;
}

.checkbox-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    margin: 0;
    padding: 0;
    pointer-events: none;
}

.checkbox-card {
    display: flex;
    align-items: center;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.checkbox-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 8px -2px rgba(59, 130, 246, 0.2);
}

.checkbox-option-item:has(.checkbox-input:checked) .checkbox-card,
.mcq-option-item:has(.mcq-checkbox:checked) .option-card {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border-color: #3b82f6;
}

.checkbox-indicator {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.checkbox-option-item:has(.checkbox-input:checked) .checkbox-indicator,
.mcq-option-item:has(.mcq-checkbox:checked) .selection-mark {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.checkmark {
    opacity: 0;
    transform: scale(0.5);
    transition: all 0.3s ease;
}

.checkbox-option-item:has(.checkbox-input:checked) .checkmark,
.mcq-option-item:has(.mcq-checkbox:checked) .selection-mark svg {
    opacity: 1;
    transform: scale(1);
}

/* True/False Styles */
.true-false-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.tf-option-item {
    cursor: pointer;
}

.tf-radio {
    display: none;
}

.tf-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.tf-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.15);
}

.tf-option-item:has(.tf-radio:checked).true-option .tf-card {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border-color: #10b981;
}

.tf-option-item:has(.tf-radio:checked).false-option .tf-card {
    background: linear-gradient(135deg, #fee2e2, #fca5a5);
    border-color: #ef4444;
}

.tf-indicator {
    width: 2rem;
    height: 2rem;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    transition: all 0.3s ease;
}

.tf-option-item:has(.tf-radio:checked) .tf-indicator {
    border-color: #10b981;
    color: white;
}

.true-option:has(.tf-radio:checked) .tf-indicator {
    background: #10b981;
}

.false-option:has(.tf-radio:checked) .tf-indicator {
    background: #ef4444;
    border-color: #ef4444;
}

.tf-text {
    font-size: 1rem;
    font-weight: 700;
    color: #374151;
}

/* Sub Question Items */
.sub-question-item {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.sub-question-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 2px 8px -2px rgba(59, 130, 246, 0.15);
}

.sub-question-title {
    font-size: 1rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Fill in Blanks */
.paragraph-display-card {
    background: linear-gradient(135deg, #fef7ff, #f3e8ff);
    border: 2px solid #a855f7;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

.paragraph-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #7c2d92;
    margin-bottom: 1rem;
    text-align: center;
}

.paragraph-content {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #1f2937;
    white-space: pre-wrap;
    text-align: left;
    max-width: 800px;
    margin: 0 auto;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.blanks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.25rem;
    margin-top: 1.5rem;
}

.blank-input-item {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.blank-input-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
}

.blank-label {
    display: block;
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.75rem;
    font-size: 1rem;
    text-align: center;
}

.blank-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #d1d5db;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    text-align: center;
    font-weight: 500;
}

.blank-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    transform: scale(1.02);
}

/* Options Reference */
.options-reference-card {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 1px solid #0ea5e9;
    border-radius: 12px;
    padding: 1rem;
    margin-top: 1rem;
}

.options-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #0c4a6e;
    margin-bottom: 0.75rem;
}

.options-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.option-tag {
    background: white;
    border: 1px solid #0ea5e9;
    color: #0c4a6e;
    padding: 0.375rem 0.625rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Reorder Styles */
.fragments-display-card {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #f59e0b;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.25rem;
}

.fragments-title {
    font-size: 1rem;
    font-weight: 700;
    color: #92400e;
    margin-bottom: 0.75rem;
}

.fragments-grid {
    display: grid;
    gap: 0.75rem;
}

.fragment-item {
    display: flex;
    align-items: center;
    background: white;
    border: 1px solid #f59e0b;
    border-radius: 8px;
    padding: 0.75rem;
    gap: 0.75rem;
}

.fragment-number {
    width: 1.5rem;
    height: 1.5rem;
    background: #f59e0b;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.fragment-text {
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.reorder-input-item {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
}

.reorder-label {
    display: block;
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.reorder-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.reorder-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-hint {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: #6b7280;
    font-style: italic;
}

/* Statement Match */
.matching-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.column-title {
    font-size: 1rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.75rem;
}

.left-items {
    display: grid;
    gap: 0.75rem;
}

.left-item-card {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border: 1px solid #3b82f6;
    border-radius: 8px;
    padding: 0.75rem;
    gap: 0.75rem;
}

.item-number {
    width: 1.5rem;
    height: 1.5rem;
    background: #3b82f6;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.item-text {
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.matching-inputs {
    display: grid;
    gap: 0.75rem;
}

.match-input-item {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
}

.match-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.75rem;
}

.match-input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.match-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.reference-card {
    background: linear-gradient(135deg, #fef7ff, #f3e8ff);
    border: 1px solid #a855f7;
    border-radius: 12px;
    padding: 1rem;
}

.reference-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #7c2d92;
    margin-bottom: 0.75rem;
}

.reference-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 0.5rem;
}

.reference-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ref-number {
    background: #a855f7;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 700;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.ref-text {
    color: #374151;
    font-weight: 500;
    font-size: 0.875rem;
}

/* Essay Styles */
.essay-input-item {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.essay-input-item:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.essay-label {
    display: block;
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
}

.essay-textarea {
    width: 100%;
    border: none;
    outline: none;
    font-size: 0.875rem;
    line-height: 1.6;
    color: #374151;
    resize: vertical;
    min-height: 120px;
}

.character-info {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.5rem;
}

.char-count {
    font-size: 0.75rem;
    color: #6b7280;
}

/* Image Matching */
.image-matching-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.image-match-item {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.image-match-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px -4px rgba(59, 130, 246, 0.15);
}

.image-container {
    text-align: center;
    margin-bottom: 1rem;
}

.match-image {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
}

.image-badge {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
}

.select-area {
    display: grid;
    gap: 0.5rem;
}

.select-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.75rem;
}

.image-select, .pair-select {
    padding: 0.5rem;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    background: white;
    transition: all 0.3s ease;
}

.image-select:focus, .pair-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Audio/Video Players */
.audio-player-item, .video-player-item, .image-display-item {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 2px solid #0ea5e9;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    max-height: unset;
}

/* Video Player Container - Enhanced for Video Fill in Blanks */
.video-player-container {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 2px solid #0ea5e9;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

.video-player-wrapper {
    margin-top: 1rem;
    display: flex;
    justify-content: center;
    align-items: center;
}

.video-player {
    width: 100%;
    max-width: 800px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: 1px solid #e5e7eb;
}

.audio-header, .video-header, .image-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    margin-bottom: 0;
    min-width: 120px;
}
.audio-title, .video-title, .image-title {
    font-weight: 600;
    color: #0c4a6e;
    font-size: 0.85rem;
    margin-left: 0.25rem;
}
.audio-player {
    width: 100%;
    height: 36px;
    border-radius: 4px;
    margin-left: 0.5rem;
}
.audio-player-small {
    width: 100%;
    height: 28px;
    border-radius: 4px;
    margin-left: 0.5rem;
}
@media (max-width: 768px) {
    .audio-player-item, .video-player-item, .image-display-item {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
        padding: 0.5rem 0.5rem;
    }
    .audio-header, .video-header, .image-header {
        min-width: 0;
        margin-bottom: 0.25rem;
    }
    .audio-player, .audio-player-small {
        margin-left: 0;
    }
    .video-player-container {
        padding: 1rem;
    }
    .video-player {
        max-width: 100%;
    }
}

/* Audio Picture Match Styles */
.audio-picture-match-container {
    margin-top: 1.5rem;
}

.media-section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.audio-items-grid {
    display: grid;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.audio-item-card {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 2px solid #0ea5e9;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.audio-item-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -4px rgba(14, 165, 233, 0.3);
}

.audio-item-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.audio-item-label {
    font-weight: 600;
    color: #0c4a6e;
    font-size: 0.875rem;
}

.audio-player-container {
    width: 100%;
}

.audio-player-container .audio-player {
    width: 100%;
    height: 40px;
    border-radius: 6px;
}

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.image-item-card {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #f59e0b;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.image-item-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -4px rgba(245, 158, 11, 0.3);
}

.image-item-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.image-item-label {
    font-weight: 600;
    color: #92400e;
    font-size: 0.875rem;
}

.image-container {
    width: 100%;
    height: 150px;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    border: 1px solid #e5e7eb;
}

.matching-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.matching-image:hover {
    transform: scale(1.05);
}

.matching-interface {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid #e5e7eb;
}

.matching-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1.5rem;
    text-align: center;
}

.matching-grid {
    display: grid;
    gap: 1rem;
}

.match-row {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 1rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.match-row:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px -4px rgba(59, 130, 246, 0.3);
}

.match-audio-label {
    text-align: center;
}

.audio-number {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.875rem;
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 1px solid #3b82f6;
}

.match-arrow {
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
}

.match-image-select {
    display: flex;
    justify-content: center;
}

.image-select {
    width: 100%;
    max-width: 200px;
    padding: 0.625rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    font-size: 0.875rem;
    font-weight: 500;
    color: #1f2937;
    transition: all 0.3s ease;
}

.image-select:hover {
    border-color: #3b82f6;
}

.image-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.error-message {
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border: 2px solid #ef4444;
    border-radius: 12px;
    color: #991b1b;
    font-weight: 500;
}

/* Responsive Design for Audio Picture Match */
@media (max-width: 768px) {
    .images-grid {
        grid-template-columns: 1fr;
    }
    
    .match-row {
        grid-template-columns: 1fr;
        gap: 0.75rem;
        text-align: center;
    }
    
    .match-arrow {
        transform: rotate(90deg);
    }
    
    .image-container {
        height: 120px;
    }
    
    .audio-item-card,
    .image-item-card {
        padding: 0.75rem;
    }
}

@media (max-width: 480px) {
    .audio-picture-match-container .grid {
        grid-template-columns: 1fr;
    }
    
    .media-section-title {
        font-size: 1rem;
    }
    
    .image-container {
        height: 100px;
    }
    
    .audio-item-card,
    .image-item-card {
        padding: 0.5rem;
    }
    
    .match-row {
        padding: 0.75rem;
    }
    
    .matching-title {
        font-size: 1.125rem;
    }
}

/* Audio Image Text Multiple Styles */
.audio-image-multiple-grid {
    display: grid;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.audio-image-multiple-item {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px -4px rgba(0, 0, 0, 0.1);
}

.audio-image-multiple-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 8px 24px -8px rgba(59, 130, 246, 0.3);
    transform: translateY(-2px);
}

.media-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    align-items: start;
}

.audio-container, .image-container {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.audio-container:hover, .image-container:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px -4px rgba(59, 130, 246, 0.2);
}

.media-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.media-label {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.audio-player-wrapper {
    width: 100%;
}

.audio-player-compact {
    width: 100%;
    height: 40px;
    border-radius: 8px;
    outline: none;
}

.audio-player-compact:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.image-wrapper {
    width: 100%;
    height: 150px;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    border: 1px solid #e5e7eb;
}

.pair-image-optimized {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
    cursor: pointer;
}

.pair-image-optimized:hover {
    transform: scale(1.05);
}

.select-area {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 1px solid #0ea5e9;
    border-radius: 12px;
    padding: 1rem;
}

.select-label {
    display: block;
    font-weight: 600;
    color: #0c4a6e;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.pair-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    font-size: 0.875rem;
    font-weight: 500;
    color: #1f2937;
    transition: all 0.3s ease;
}

.pair-select:hover {
    border-color: #3b82f6;
}

.pair-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.pair-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e5e7eb;
    text-align: center;
}

/* Responsive Design for Audio Image Multiple */
@media (max-width: 768px) {
    .media-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .audio-image-multiple-item {
        padding: 1rem;
    }
    
    .image-wrapper {
        height: 120px;
    }
    
    .audio-container, .image-container {
        padding: 0.75rem;
    }
    
    .pair-title {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .audio-image-multiple-item {
        padding: 0.75rem;
    }
    
    .image-wrapper {
        height: 100px;
    }
    
    .audio-container, .image-container {
        padding: 0.5rem;
    }
    
    .media-header {
        margin-bottom: 0.5rem;
    }
    
    .media-label {
        font-size: 0.75rem;
    }
    
    .select-area {
        padding: 0.75rem;
    }
    
    .pair-title {
        font-size: 0.875rem;
        margin-bottom: 0.75rem;
    }
}

/* Enhanced selected states for test mode */
.mcq-option-item.selected,
.mcq-option-item:has(.mcq-radio:checked) {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

.checkbox-option-item.selected,
.checkbox-option-item:has(.checkbox-input:checked) {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important;
    border-color: #3b82f6 !important;
}

.tf-option-item.selected.true-option,
.tf-option-item:has(.tf-radio:checked).true-option {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0) !important;
    border-color: #10b981 !important;
}

.tf-option-item.selected.false-option,
.tf-option-item:has(.tf-radio:checked).false-option {
    background: linear-gradient(135deg, #fee2e2, #fca5a5) !important;
    border-color: #ef4444 !important;
}

.mcq-option-item.selected .selection-mark,
.mcq-option-item:has(.mcq-radio:checked) .selection-mark {
    background: #10b981 !important;
    border-color: #10b981 !important;
    color: white !important;
    opacity: 1 !important;
}

.checkbox-option-item.selected .checkbox-indicator,
.checkbox-option-item:has(.checkbox-input:checked) .checkbox-indicator {
    background: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: white !important;
}

.tf-option-item.selected .tf-indicator,
.tf-option-item:has(.tf-radio:checked) .tf-indicator {
    border-color: #10b981 !important;
    color: white !important;
}

.tf-option-item.selected.true-option .tf-indicator,
.tf-option-item:has(.tf-radio:checked).true-option .tf-indicator {
    background: #10b981 !important;
}

.tf-option-item.selected.false-option .tf-indicator,
.tf-option-item:has(.tf-radio:checked).false-option .tf-indicator {
    background: #ef4444 !important;
    border-color: #ef4444 !important;
}

.submit-section {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid #e5e7eb;
}
.submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 1.1rem 0;
    border: none;
    border-radius: 12px;
    font-size: 1.15rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px -4px rgba(16, 185, 129, 0.4);
    margin: 0 auto;
    max-width: 400px;
}
.submit-btn svg {
    width: 1.5rem;
    height: 1.5rem;
    margin-right: 0.5rem;
}

/* Animation */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.answer-section {
    animation: slideInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Focus and Accessibility */
.mcq-option-item:focus-within,
.checkbox-option-item:focus-within,
.tf-option-item:focus-within {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
    border-radius: 12px;
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .modern-answer-card {
        border: 2px solid #000;
    }
    
    .option-card,
    .checkbox-card,
    .tf-card {
        border: 2px solid #000;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-answer-form {
        padding: 0.75rem;
    }
    
    .card-content {
        padding: 1rem;
    }
    
    .question-title {
        font-size: 1.125rem;
        line-height: 1.4;
    }
    
    .true-false-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .matching-layout {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .image-matching-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .pair-media {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .blanks-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .match-image, .pair-image {
        width: 120px;
        height: 120px;
    }
    
    .reference-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    /* Enhanced Audio Player Mobile Styles */
    .audio-player-item, .video-player-item, .image-display-item {
        flex-direction: column;
        align-items: stretch;
        padding: 0.75rem;
        gap: 0.5rem;
        min-height: auto;
        max-height: none;
    }
    
    .audio-header, .video-header, .image-header {
        justify-content: center;
        margin-bottom: 0.5rem;
        min-width: auto;
    }
    
    .audio-title, .video-title, .image-title {
        font-size: 0.875rem;
    }
    
    .audio-player, .audio-player-compact {
        height: 32px;
        margin-left: 0;
        width: 100%;
    }
    
    .reference-image {
        max-height: 200px;
        width: 100%;
        object-fit: contain;
    }
    
    /* Audio Picture Match Mobile */
    .audio-picture-match-container .grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .audio-items-grid {
        gap: 0.75rem;
    }
    
    .audio-item-card {
        padding: 0.75rem;
    }
    
    .audio-player-container .audio-player {
        height: 36px;
    }
    
    .images-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
    }
    
    .image-container {
        height: 120px;
    }
    
    .matching-grid {
        gap: 0.75rem;
    }
    
    .match-row {
        grid-template-columns: 1fr;
        gap: 0.75rem;
        text-align: center;
        padding: 0.75rem;
    }
    
    .match-arrow {
        transform: rotate(90deg);
        margin: 0.5rem 0;
    }
    
    .audio-number {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
    }
    
    /* MCQ Options Mobile */
    .mcq-options-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .option-card {
        padding: 0.75rem;
    }
    
    .option-indicator {
        font-size: 0.875rem;
        width: 1.75rem;
        height: 1.75rem;
    }
    
    .option-text {
        font-size: 0.875rem;
        line-height: 1.4;
    }
    
    /* Section Titles Mobile */
    .section-title {
        font-size: 1.125rem;
        margin-bottom: 1rem;
    }
    
    .sub-question-title {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .pair-title {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
}

@media (max-width: 480px) {
    .modern-answer-form {
        padding: 0.5rem;
    }
    
    .card-content {
        padding: 0.75rem;
    }
    
    .question-title {
        font-size: 1rem;
        line-height: 1.4;
        margin-bottom: 1rem;
    }
    
    .question-meta {
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    
    .option-card {
        padding: 0.625rem;
    }
    
    .option-text {
        font-size: 0.8rem;
    }
    
    .match-image, .pair-image {
        width: 100px;
        height: 100px;
    }
    
    .fragments-grid {
        gap: 0.5rem;
    }
    
    .fragment-item {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Enhanced Mobile Audio Player Styles */
    .audio-player-item, .video-player-item, .image-display-item {
        padding: 0.5rem;
        gap: 0.375rem;
    }
    
    .audio-header, .video-header, .image-header {
        margin-bottom: 0.375rem;
        gap: 0.25rem;
    }
    
    .audio-title, .video-title, .image-title {
        font-size: 0.75rem;
    }
    
    .audio-player, .audio-player-compact {
        height: 28px;
    }
    
    .audio-section {
        padding: 0.5rem;
    }
    
    .reference-image {
        max-height: 150px;
    }
    
    /* Audio Picture Match Very Small Screens */
    .audio-item-card {
        padding: 0.5rem;
    }
    
    .audio-player-container .audio-player {
        height: 32px;
    }
    
    .images-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.5rem;
    }
    
    .image-container {
        height: 100px;
    }
    
    .match-row {
        padding: 0.5rem;
        gap: 0.5rem;
    }
    
    .audio-number {
        padding: 0.3rem 0.6rem;
        font-size: 0.75rem;
    }
    
    .image-select, .pair-select {
        font-size: 0.75rem;
        padding: 0.5rem;
    }
    
    /* Section titles for very small screens */
    .section-title {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .sub-question-title {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .pair-title {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .media-section-title {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    /* Info banner mobile */
    .info-banner {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        margin-bottom: 1rem;
    }
    
    /* Submit button mobile */
    .submit-btn {
        font-size: 1rem;
        padding: 1rem 0;
    }
    
    .submit-btn svg {
        width: 1.25rem;
        height: 1.25rem;
    }
}

@keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fade-in 0.4s ease; }

/* Image Preview Modal */
.image-preview-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease-out;
}

.image-preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.image-preview-content {
    position: relative;
    max-width: 95vw;
    max-height: 95vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.image-preview-close {
    position: absolute;
    top: -50px;
    right: -50px;
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10001;
    color: #374151;
}

.image-preview-close:hover {
    background: white;
    transform: scale(1.1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.preview-image {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    animation: zoomIn 0.3s ease-out;
}

.image-preview-info {
    background: rgba(255, 255, 255, 0.95);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.image-preview-info span {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* General clickable image styles - applied via JavaScript */
.answer-section img,
.image-container img,
.image-wrapper img,
.image-match-item img,
.image-display-item img {
    transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
    border-radius: 8px;
}

/* Ensure cursor pointer shows on clickable images */
.answer-section img.cursor-pointer,
.image-container img.cursor-pointer,
.image-wrapper img.cursor-pointer,
.image-match-item img.cursor-pointer,
.image-display-item img.cursor-pointer {
    cursor: pointer !important;
}

/* Style for image links */
.answer-section a,
.image-container a,
.image-wrapper a,
.image-match-item a,
.image-display-item a {
    display: block;
    text-decoration: none;
    border-radius: 8px;
    overflow: hidden;
}

.answer-section a:hover img,
.image-container a:hover img,
.image-wrapper a:hover img,
.image-match-item a:hover img,
.image-display-item a:hover img {
    transform: scale(1.02);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

/* Responsive Design for Image Preview */
@media (max-width: 768px) {
    .image-preview-close {
        top: -40px;
        right: -20px;
        width: 40px;
        height: 40px;
    }
    
    .preview-image {
        max-height: 70vh;
    }
    
    .image-preview-info {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
}

/* File Upload Styling */
.file-upload-section {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.file-upload-section:hover {
    border-color: #cbd5e1;
}

.file-upload-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.file-upload-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.file-upload-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.file-upload-input:hover {
    border-color: #cbd5e1;
}

.file-upload-info {
    margin-top: 0.5rem;
}

.file-upload-info p {
    margin: 0;
    color: #64748b;
    font-size: 0.85rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for essay textarea
    const essayTextarea = document.querySelector('.essay-textarea');
    if (essayTextarea) {
        const charCount = document.querySelector('.char-count');
        if (charCount) {
            essayTextarea.addEventListener('input', function() {
                const count = this.value.length;
                charCount.textContent = `${count} characters`;
                
                // Visual feedback for length
                if (count > 500) {
                    charCount.style.color = '#059669'; // green
                } else if (count > 200) {
                    charCount.style.color = '#d97706'; // orange  
                } else {
                    charCount.style.color = '#6b7280'; // gray
                }
            });
        }
    }
    
    // Auto-save functionality
    const form = document.querySelector('form') || document.querySelector('.answer-form');
    const inputs = form ? form.querySelectorAll('input, textarea, select') : [];
    
    // Save answers to localStorage as backup
    if (form && inputs.length > 0) {
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                saveAnswersToLocalStorage();
            });
        });
    }
    
    function saveAnswersToLocalStorage() {
        if (!form) return;
        const formData = new FormData(form);
        const answers = {};
        for (let [key, value] of formData.entries()) {
            if (answers[key]) {
                if (Array.isArray(answers[key])) {
                    answers[key].push(value);
                } else {
                    answers[key] = [answers[key], value];
                }
            } else {
                answers[key] = value;
            }
        }
        localStorage.setItem('temp_answers', JSON.stringify(answers));
    }
    
    // Enhanced form validation before submit
    if (window.form) {
        window.form.addEventListener('submit', function(e) {
            let hasErrors = false;
            const errorElements = document.querySelectorAll('.error-highlight');
            errorElements.forEach(el => el.classList.remove('error-highlight'));
            
            // Check for required fields based on question type
            const questionTypeElement = document.querySelector('.question-type-badge');
            const questionType = questionTypeElement ? questionTypeElement.textContent.toLowerCase() : '';
            
        // Specialized validation for True/False questions
        if (questionType.includes('true')) {
            // Check single true/false
            if (questionType === 'true false' || questionType === 'true_false' || (questionType.includes('true') && !questionType.includes('multiple'))) {
                const tfRadios = form.querySelectorAll('input[name="answer"]:checked');
                if (tfRadios.length === 0) {
                    hasErrors = true;
                    const tfContainer = form.querySelector('.true-false-grid');
                    if (tfContainer) {
                        tfContainer.style.border = '2px solid #ef4444';
                        tfContainer.style.borderRadius = '12px';
                        tfContainer.style.padding = '1rem';
                        tfContainer.style.backgroundColor = '#fee2e2';
                        tfContainer.classList.add('error-highlight');
                    }
                }
            }
            
            // Check multiple true/false - IMPROVED VALIDATION
            if (questionType.includes('multiple') || questionType.includes('true false multiple') || questionType.includes('true_false_multiple')) {
                const allSubQuestions = form.querySelectorAll('.sub-question-item');
                
                allSubQuestions.forEach((subQuestion, index) => {
                    // Check for radio buttons with specific names like answer[0], answer[1], etc.
                    const answerName = `answer[${index}]`;
                    const allRadios = subQuestion.querySelectorAll(`input[name="${answerName}"]`);
                    const checkedRadios = subQuestion.querySelectorAll(`input[name="${answerName}"]:checked`);
                    
                    // Only validate if there are radio buttons in this sub-question
                    if (allRadios.length > 0 && checkedRadios.length === 0) {
                        hasErrors = true;
                        subQuestion.style.border = '2px solid #ef4444';
                        subQuestion.style.backgroundColor = '#fee2e2';
                        subQuestion.style.borderRadius = '12px';
                        subQuestion.style.padding = '1rem';
                        subQuestion.classList.add('error-highlight');
                        
                        // Add error message
                        let errorMsg = subQuestion.querySelector('.validation-error-msg');
                        if (!errorMsg) {
                            errorMsg = document.createElement('div');
                            errorMsg.className = 'validation-error-msg';
                            errorMsg.style.cssText = 'color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem; font-weight: 500;';
                            errorMsg.textContent = 'Please select True or False for this statement.';
                            subQuestion.appendChild(errorMsg);
                        }
                    }
                });
            }
        }
        
        // MCQ validation
        else if (questionType.includes('mcq') || (questionType.includes('audio') && !questionType.includes('fill') && !questionType.includes('true')) || (questionType.includes('picture') && !questionType.includes('fill'))) {
            
            // MCQ Single - Radio button validation
            if (questionType.includes('mcq single') || questionType === 'mcq single' || (questionType.includes('mcq') && !questionType.includes('multiple'))) {
                const radioGroups = {};
                form.querySelectorAll('input[type="radio"]').forEach(radio => {
                    if (!radioGroups[radio.name]) {
                        radioGroups[radio.name] = false;
                    }
                    if (radio.checked) {
                        radioGroups[radio.name] = true;
                    }
                });
                
                Object.keys(radioGroups).forEach(groupName => {
                    if (!radioGroups[groupName]) {
                        hasErrors = true;
                        const radioGroup = form.querySelector(`input[name="${groupName}"]`).closest('.sub-question-item, .answer-section');
                        if (radioGroup) {
                            radioGroup.style.border = '2px solid #ef4444';
                            radioGroup.style.borderRadius = '12px';
                            radioGroup.style.padding = '1rem';
                            radioGroup.style.backgroundColor = '#fee2e2';
                            radioGroup.classList.add('error-highlight');
                        }
                    }
                });
            }
            
            // MCQ Multiple - Checkbox validation
            else if (questionType.includes('mcq multiple') || questionType === 'mcq multiple' || questionType.includes('multiple')) {
                const allSubQuestions = form.querySelectorAll('.sub-question-item');
                allSubQuestions.forEach(subQuestion => {
                    const checkboxes = subQuestion.querySelectorAll('input[type="checkbox"]:checked');
                    if (checkboxes.length === 0) {
                        hasErrors = true;
                        subQuestion.style.border = '2px solid #ef4444';
                        subQuestion.style.backgroundColor = '#fee2e2';
                        subQuestion.style.borderRadius = '12px';
                        subQuestion.style.padding = '1rem';
                        subQuestion.classList.add('error-highlight');
                    }
                });
            }
            
            // Other MCQ types (audio, picture) - Radio button validation
            else {
                const radioGroups = {};
                form.querySelectorAll('input[type="radio"]').forEach(radio => {
                    if (!radioGroups[radio.name]) {
                        radioGroups[radio.name] = false;
                    }
                    if (radio.checked) {
                        radioGroups[radio.name] = true;
                    }
                });
                
                Object.keys(radioGroups).forEach(groupName => {
                    if (!radioGroups[groupName]) {
                        hasErrors = true;
                        const radioGroup = form.querySelector(`input[name="${groupName}"]`).closest('.sub-question-item, .answer-section');
                        if (radioGroup) {
                            radioGroup.style.border = '2px solid #ef4444';
                            radioGroup.style.borderRadius = '12px';
                            radioGroup.style.padding = '1rem';
                            radioGroup.style.backgroundColor = '#fee2e2';
                            radioGroup.classList.add('error-highlight');
                        }
                    }
                });
            }
        }
        
        // Statement match validation (number inputs)
        if (questionType.includes('statement')) {
            form.querySelectorAll('input[type="number"]').forEach(input => {
                if (!input.value.trim()) {
                    hasErrors = true;
                    input.style.borderColor = '#ef4444';
                    input.classList.add('error-highlight');
                }
            });
        }
        
        // Reorder validation (text input for order)
        if (questionType.includes('reorder')) {
            form.querySelectorAll('.reorder-input').forEach(input => {
                if (!input.value.trim()) {
                    hasErrors = true;
                    input.style.borderColor = '#ef4444';
                    input.classList.add('error-highlight');
                    const container = input.closest('.reorder-input-item');
                    if (container) {
                        container.style.border = '2px solid #ef4444';
                        container.style.backgroundColor = '#fee2e2';
                        container.classList.add('error-highlight');
                    }
                }
            });
        }
        
        // Check text inputs (excluding reorder inputs which are handled separately)
        // Skip validation for opinion questions as they are subjective
        if (!questionType.includes('opinion')) {
            form.querySelectorAll('input[type="text"], textarea').forEach(input => {
                if ((input.hasAttribute('required') || input.closest('.blank-input-item, .match-input-item')) && !input.closest('.reorder-input-item')) {
                    if (!input.value.trim()) {
                        hasErrors = true;
                        input.style.borderColor = '#ef4444';
                        input.classList.add('error-highlight');
                    }
                }
            });
        }
            
        // Check selects (skip those in statement match since they use number inputs)
        if (!questionType.includes('statement')) {
            form.querySelectorAll('select').forEach(select => {
                if (!select.value) {
                    hasErrors = true;
                    select.style.borderColor = '#ef4444';
                    select.classList.add('error-highlight');
                }
            });
        }
            
            if (hasErrors) {
                e.preventDefault();
                
                // Scroll to first error
                const firstError = document.querySelector('.error-highlight');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Show error message
                showMessage('Please complete all required fields before submitting.', 'error');
            } else {
                // Clear localStorage backup on successful submit
                localStorage.removeItem('temp_answers');
                
                // Add loading state to submit button
                const submitBtn = document.querySelector('.submit-btn');
                if (submitBtn) {
                    const originalHTML = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Submitting...';
                    submitBtn.disabled = true;
                    
                    // Restore button after 5 seconds (in case of network issues)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalHTML;
                        submitBtn.disabled = false;
                    }, 5000);
                }
            }
        });
    }

    // Clear error highlighting on input - ENHANCED FOR TRUE/FALSE MULTIPLE
    if (form && inputs.length > 0) {
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                this.classList.remove('error-highlight');
                const container = this.closest('.sub-question-item, .answer-section, .true-false-grid, .reorder-input-item');
                if (container) {
                    container.style.border = '';
                    container.style.backgroundColor = '';
                    container.style.padding = '';
                    container.style.borderRadius = '';
                    container.classList.remove('error-highlight');
                    
                    // Remove validation error message
                    const errorMsg = container.querySelector('.validation-error-msg');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            // Special handling for radio buttons (including true/false)
            if (input.type === 'radio') {
                input.addEventListener('change', function() {
                    // Clear error styling from the container
                    const container = this.closest('.sub-question-item, .answer-section, .true-false-grid');
                    if (container) {
                        container.style.border = '';
                        container.style.backgroundColor = '';
                        container.style.padding = '';
                        container.style.borderRadius = '';
                        container.classList.remove('error-highlight');
                        
                        // Remove validation error message
                        const errorMsg = container.querySelector('.validation-error-msg');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    }
                });
            }
            
            // Special handling for checkboxes (MCQ multiple)
            if (input.type === 'checkbox') {
                input.addEventListener('change', function() {
                    // Clear error styling from the container
                    const container = this.closest('.sub-question-item, .answer-section');
                    if (container) {
                        container.style.border = '';
                        container.style.backgroundColor = '';
                        container.style.padding = '';
                        container.style.borderRadius = '';
                        container.classList.remove('error-highlight');
                    }
                });
            }
        });
    }
    
    // Message system
    function showMessage(text, type = 'info') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-alert ${type}`;
        messageDiv.innerHTML = `
            <div class="message-content">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'error' ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span>${text}</span>
            </div>
        `;
        
        // Style the message
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            font-weight: 500;
            max-width: 300px;
            animation: slideInRight 0.3s ease;
            ${type === 'error' ? 
                'background: linear-gradient(135deg, #fee2e2, #fca5a5); border: 1px solid #ef4444; color: #7f1d1d;' :
                'background: linear-gradient(135deg, #dbeafe, #bfdbfe); border: 1px solid #3b82f6; color: #1e3a8a;'
            }
        `;
        
        messageDiv.querySelector('.message-content').style.cssText = 'display: flex; align-items: center;';
        
        document.body.appendChild(messageDiv);
        
        // Remove message after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
    
    // Add visual feedback for interactions
    if (form) {
        const interactiveElements = form.querySelectorAll('.mcq-option-item, .checkbox-option-item, .tf-option-item');
        interactiveElements.forEach(element => {
            element.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    }
    
    // Enhanced keyboard navigation
    if (form) {
        document.addEventListener('keydown', function(e) {
            // Submit with Ctrl+Enter
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
            
            // Clear all answers with Ctrl+R (prevent page refresh)
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                if (confirm('Clear all answers?')) {
                    form.reset();
                    localStorage.removeItem('temp_answers');
                    // Silent clear - no message shown
                }
            }
        });
    }
    
    // Auto-resize textareas
    document.querySelectorAll('textarea').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
    
    // Image Preview Functionality (now simplified since we use direct links)
    function setupImagePreview() {
        // Include all possible image classes from different question types
        const images = document.querySelectorAll('img:not(.preview-image)');
        
        images.forEach(image => {
            // Only add hover effects to images that are part of questions (not UI elements)
            const isQuestionImage = image.closest('.answer-section, .image-container, .image-wrapper, .audio-image-multiple-item, .image-match-item, .image-display-item, .audio-picture-match-container');
            
            // Skip images that are already wrapped in links (new tab functionality)
            const isWrappedInLink = image.closest('a');
            
            // Only add click handler to images that don't have link wrappers (fallback)
            if (isQuestionImage && !isWrappedInLink) {
                image.style.cursor = 'pointer';
                image.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const imageSrc = this.src;
                    const imageAlt = this.alt || 'Image Preview';
                    
                    // Open in new tab as fallback
                    window.open(imageSrc, '_blank', 'noopener,noreferrer');
                });
            }
        });
    }
    
    // Initialize image preview on page load
    setupImagePreview();
    
    // View Mode Detection
    const isViewMode = {{ $isViewMode ? 'true' : 'false' }};
    
    if (isViewMode) {
        // Disable all inputs
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name === '_token') {
                continue;
            }
            
            input.disabled = true;
            input.classList.add('view-mode');
        });
        
        // Add view mode class to parent containers
        const containers = document.querySelectorAll('.answer-section, .mcq-options-grid, .checkbox-options-grid, .true-false-grid');
        containers.forEach(container => {
            container.classList.add('view-mode');
        });
        
        // Highlight submitted answers
        @if($isViewMode && $viewModeAnswerData)
            // MCQ Single
            @if($type === 'mcq_single')
                const singleOptionInputs = document.querySelectorAll('input[name="answer"]');
                const submittedAnswerIndex = parseInt('{{ $viewModeAnswerData }}', 10);
                singleOptionInputs.forEach((input, index) => {
                    if (index === submittedAnswerIndex) {
                        input.checked = true;
                        input.closest('.mcq-option-item').classList.add('submitted-answer');
                    }
                });
            @endif
            
            // MCQ Multiple
            @if($type === 'mcq_multiple')
                @foreach($viewModeAnswerData as $subIdx => $selectedOptions)
                    @foreach($selectedOptions as $optIdx)
                        const multiOptionInput = document.querySelector(`input[name="answer[${subIdx}][]"][value="${optIdx}"]`);
                        if (multiOptionInput) {
                            multiOptionInput.checked = true;
                            multiOptionInput.closest('.checkbox-option-item').classList.add('submitted-answer');
                        }
                    @endforeach
                @endforeach
            @endif
            
            // True/False
            @if($type === 'true_false')
                const tfInput = document.querySelector(`input[name="answer"][value="{{ $viewModeAnswerData }}"]`);
                if (tfInput) {
                    tfInput.checked = true;
                    tfInput.closest('.tf-option-item').classList.add('submitted-answer');
                }
            @endif
            
            // True/False Multiple
            @if($type === 'true_false_multiple')
                @foreach($viewModeAnswerData as $statementIdx => $answer)
                    const tfMultiInput = document.querySelector(`input[name="answer[${statementIdx}]"][value="{{ $answer }}"]`);
                    if (tfMultiInput) {
                        tfMultiInput.checked = true;
                        tfMultiInput.closest('.tf-option-item').classList.add('submitted-answer');
                    }
                @endforeach
            @endif
            
            // Fill in Blanks
            @if($type === 'form_fill')
                @foreach($viewModeAnswerData as $blankIdx => $blankAnswer)
                    const blankInput = document.querySelector(`input[name="answer[${blankIdx}]"]`);
                    if (blankInput) {
                        blankInput.value = "{{ $blankAnswer }}";
                        blankInput.classList.add('submitted-answer');
                    }
                @endforeach
            @endif
            
            // Statement Match
            @if($type === 'statement_match')
                @foreach($viewModeAnswerData as $itemIdx => $matchedItemIndex)
                    const matchInput = document.querySelector(`input[name="answer[${itemIdx}]"]`);
                    if (matchInput) {
                        matchInput.value = "{{ $matchedItemIndex }}";
                        matchInput.classList.add('submitted-answer');
                    }
                @endforeach
            @endif
            
            // Picture MCQ / Audio Image Text
            @if($type === 'picture_mcq' || $type === 'audio_image_text_single' || $type === 'audio_image_text_multiple')
                @foreach($viewModeAnswerData as $imageIdx => $selectedOptionIndex)
                    const imageSelect = document.querySelector(`select[name="answer[${imageIdx}]"]`);
                    if (imageSelect) {
                        imageSelect.value = "{{ $selectedOptionIndex }}";
                        imageSelect.classList.add('submitted-answer');
                    }
                @endforeach
            @endif
            
            // Audio Picture Match
            @if($type === 'audio_picture_match')
                @foreach($viewModeAnswerData as $audioIdx => $imageIndex)
                    const audioSelect = document.querySelector(`select[name="answer[${audioIdx}]"]`);
                    if (audioSelect) {
                        audioSelect.value = "{{ $imageIndex }}";
                        audioSelect.classList.add('submitted-answer');
                    }
                @endforeach
            @endif
            
            // Opinion
            @if($type === 'opinion')
                const opinionTextarea = document.querySelector('textarea[name="answer"]');
                if (opinionTextarea) {
                    opinionTextarea.value = `{{ $viewModeAnswerData }}`;
                    opinionTextarea.classList.add('submitted-answer');
                }
            @endif
        @endif
    }
});

// Image Preview Functions (kept for backward compatibility)
function showImagePreview(src, title) {
    // Fallback: open in new tab instead of modal
    window.open(src, '_blank', 'noopener,noreferrer');
}

function closeImagePreview() {
    // No longer needed with new tab approach
    return;
}

function handleEscapeKey(event) {
    // No longer needed with new tab approach  
    return;
}

// Function for opening image modal (simplified - now opens in new tab)
function openImageModal(src, title) {
    window.open(src, '_blank', 'noopener,noreferrer');
}

// Make functions globally accessible (kept for backward compatibility)
window.openImageModal = openImageModal;
window.showImagePreview = showImagePreview;
window.closeImagePreview = closeImagePreview;

// Add CSS animation for message alerts
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
`;
document.head.appendChild(style);

</script>

<style>
@keyframes fade-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.animate-fade-in { animation: fade-in 0.3s ease; }
</style>

<!-- Security Watermark -->
@include('components.student-watermark')

<!-- Form Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form') || document.querySelector('.answer-form');
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    
    if (!form || !submitButton) return;
    
    // Debugging: Log all input elements
    console.log('Question Type:', '{{ $type }}');
    console.log('All Input Elements:', form.querySelectorAll('input, select, textarea'));
    
    function validateForm() {
        const questionType = '{{ $type }}';
        let isValid = false;
        
        console.log('Validating form for type:', questionType);
        
        switch (questionType) {
            case 'mcq_single':
                const singleRadios = window.form.querySelectorAll('input[name="answer"][type="radio"]');
                console.log('MCQ Single Radios found:', singleRadios.length);
                console.log('MCQ Single Radios:', singleRadios);
                singleRadios.forEach((radio, idx) => {
                    console.log(`Radio ${idx}: name=${radio.name}, value=${radio.value}, checked=${radio.checked}`);
                });
                isValid = Array.from(singleRadios).some(radio => radio.checked);
                console.log('MCQ Single Valid:', isValid);
                break;
            
            case 'true_false':
                const tfRadios = form.querySelectorAll('input[name="answer"][type="radio"]');
                console.log('True/False Radios:', tfRadios);
                isValid = Array.from(tfRadios).some(radio => radio.checked);
                console.log('True/False Valid:', isValid);
                break;
            
            case 'mcq_multiple':
                const multiCheckboxGroups = form.querySelectorAll('.sub-question-item');
                console.log('MCQ Multiple Groups:', multiCheckboxGroups);
                isValid = Array.from(multiCheckboxGroups).every(group => {
                    const checkboxes = group.querySelectorAll('input[type="checkbox"]');
                    const groupValid = Array.from(checkboxes).some(cb => cb.checked);
                    console.log('MCQ Multiple Group Valid:', groupValid);
                    return groupValid;
                });
                console.log('MCQ Multiple Overall Valid:', isValid);
                break;
            
            case 'true_false_multiple':
                const tfMultiGroups = form.querySelectorAll('.sub-question-item');
                console.log('True/False Multiple Groups:', tfMultiGroups);
                isValid = Array.from(tfMultiGroups).every(group => {
                    const radios = group.querySelectorAll('input[type="radio"]');
                    const groupValid = Array.from(radios).some(radio => radio.checked);
                    console.log('True/False Multiple Group Valid:', groupValid);
                    return groupValid;
                });
                console.log('True/False Multiple Overall Valid:', isValid);
                break;
            
            case 'form_fill':
                const blankInputs = form.querySelectorAll('input[name^="answer"]');
                console.log('Form Fill Inputs:', blankInputs);
                isValid = Array.from(blankInputs).every(input => input.value.trim() !== '');
                console.log('Form Fill Valid:', isValid);
                break;
            
            case 'opinion':
                const textArea = form.querySelector('textarea[name="answer"]');
                const fileInput = form.querySelector('input[name="audio_video_file"]');
                console.log('Opinion Text Area:', textArea);
                console.log('Opinion File Input:', fileInput);
                isValid = (textArea && textArea.value.trim() !== '') || 
                          (fileInput && fileInput.files.length > 0);
                console.log('Opinion Valid:', isValid);
                break;
            
            default:
                // For other question types like picture_mcq, audio_mcq, etc.
                // Check if any form inputs have values
                const allInputs = form.querySelectorAll('input, select, textarea');
                isValid = Array.from(allInputs).some(input => {
                    if (input.type === 'radio' || input.type === 'checkbox') {
                        return input.checked;
                    } else if (input.tagName === 'SELECT') {
                        return input.value !== '';
                    } else {
                        return input.value.trim() !== '';
                    }
                });
                console.log('Default validation - Valid:', isValid);
                break;
        }
        
        return isValid;
    }
    
    // Add visual feedback for selected options - Enhanced with debugging
    function updateOptionVisuals() {
        console.log('Updating option visuals...');
        
        // Handle ALL .mcq-option-item elements (both radio and checkbox)
        const mcqOptions = form.querySelectorAll('.mcq-option-item');
        console.log('Found MCQ options:', mcqOptions.length);
        
        mcqOptions.forEach((option, index) => {
            const radio = option.querySelector('input[type="radio"]');
            const checkbox = option.querySelector('input[type="checkbox"]');
            const input = radio || checkbox;
            const optionCard = option.querySelector('.option-card');
            const selectionMark = option.querySelector('.selection-mark');
            
            console.log(`Option ${index}: radio=${radio ? radio.checked : 'none'}, checkbox=${checkbox ? checkbox.checked : 'none'}`);
            
            if (input && input.checked) {
                console.log(`Selecting option ${index}`);
                option.classList.add('selected');
                option.style.cssText = 'background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important; border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;';
                
                if (optionCard) {
                    optionCard.style.cssText = 'background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important; border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;';
                }
                if (selectionMark) {
                    selectionMark.style.cssText = 'background: #10b981 !important; border-color: #10b981 !important; color: white !important; opacity: 1 !important;';
                }
            } else {
                console.log(`Deselecting option ${index}`);
                option.classList.remove('selected');
                option.style.cssText = '';
                
                if (optionCard) {
                    optionCard.style.cssText = '';
                }
                if (selectionMark) {
                    selectionMark.style.cssText = '';
                }
            }
        });
        
        // True/False options
        const tfOptions = form.querySelectorAll('.tf-option-item');
        tfOptions.forEach((option, index) => {
            const radio = option.querySelector('input[type="radio"]');
            const tfCard = option.querySelector('.tf-card');
            const tfIndicator = option.querySelector('.tf-indicator');
            
            if (radio && radio.checked) {
                option.classList.add('selected');
                
                if (option.classList.contains('true-option')) {
                    if (tfCard) {
                        tfCard.style.cssText = 'background: linear-gradient(135deg, #d1fae5, #a7f3d0) !important; border-color: #10b981 !important;';
                    }
                    if (tfIndicator) {
                        tfIndicator.style.cssText = 'background: #10b981 !important; border-color: #10b981 !important; color: white !important;';
                    }
                } else {
                    if (tfCard) {
                        tfCard.style.cssText = 'background: linear-gradient(135deg, #fee2e2, #fca5a5) !important; border-color: #ef4444 !important;';
                    }
                    if (tfIndicator) {
                        tfIndicator.style.cssText = 'background: #ef4444 !important; border-color: #ef4444 !important; color: white !important;';
                    }
                }
            } else {
                option.classList.remove('selected');
                if (tfCard) {
                    tfCard.style.cssText = '';
                }
                if (tfIndicator) {
                    tfIndicator.style.cssText = '';
                }
            }
        });
    }
    
    // Add enhanced event listeners with more aggressive triggering
    const allInputs = form.querySelectorAll('input, select, textarea');
    allInputs.forEach(input => {
        if (input.type === 'radio' || input.type === 'checkbox') {
            input.addEventListener('change', function() {
                console.log('Input changed:', this.name, this.value, this.checked);
                updateOptionVisuals();
                hideValidationError();
            });
            input.addEventListener('click', function() {
                console.log('Input clicked:', this.name, this.value, this.checked);
                setTimeout(updateOptionVisuals, 1);
                hideValidationError();
            });
            input.addEventListener('input', function() {
                console.log('Input input event:', this.name, this.value, this.checked);
                setTimeout(updateOptionVisuals, 1);
                hideValidationError();
            });
        } else {
            input.addEventListener('change', function() {
                updateOptionVisuals();
                hideValidationError();
            });
            input.addEventListener('input', function() {
                updateOptionVisuals();
                hideValidationError();
            });
        }
    });
    
    // Function to hide validation error banner
    function hideValidationError() {
        const jsErrorBanner = document.getElementById('js-validation-error');
        if (jsErrorBanner) {
            jsErrorBanner.classList.add('hidden');
            jsErrorBanner.style.display = 'none';
        }
    }
    
    // Add click handlers with immediate visual feedback - FIXED VERSION
    const mcqOptions = form.querySelectorAll('.mcq-option-item');
    console.log('Setting up MCQ options:', mcqOptions.length);
    
    mcqOptions.forEach((option, index) => {
        option.addEventListener('click', function(e) {
            console.log(`MCQ Option ${index} clicked`);
            
            // FIRST: Handle the radio button selection
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                console.log('Found radio button:', radio);
                console.log('Radio details:', {
                    name: radio.name,
                    value: radio.value,
                    checked: radio.checked,
                    disabled: radio.disabled
                });

                // CRITICAL: Enable the radio if it's disabled
                if (radio.disabled) {
                    radio.disabled = false;
                    console.log('Enabled disabled radio button');
                }
                
                // Clear all radios in this group
                const allRadios = document.querySelectorAll(`input[name="${radio.name}"]`);
                console.log('Clearing', allRadios.length, 'radios');
                allRadios.forEach((r, idx) => {
                    r.checked = false;
                    r.disabled = false; // Enable all radios
                    console.log(`Cleared and enabled radio ${idx}`);
                });
                
                // Set this radio to checked
                radio.checked = true;
                radio.disabled = false; // Ensure it's enabled
                console.log('Set radio checked. New state:', radio.checked);
                
                // Verify it worked
                if (radio.checked) {
                    console.log('✅ Radio successfully checked');
                } else {
                    console.log('❌ Radio check failed, trying alternative method');
                    // Try alternative method
                    radio.setAttribute('checked', 'true');
                    radio.defaultChecked = true;
                    // Force trigger
                    radio.click();
                }
            }
            
            // SECOND: Apply visual styling
            console.log('Applying styles to clicked option');
            
            // Clear all options first
            mcqOptions.forEach(opt => {
                opt.style.cssText = '';
                opt.classList.remove('selected');
                const optCard = opt.querySelector('.option-card');
                const optMark = opt.querySelector('.selection-mark');
                if (optCard) optCard.style.cssText = '';
                if (optMark) optMark.style.cssText = '';
            });
            
            // Style the clicked option
            this.style.cssText = 'background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important; border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;';
            this.classList.add('selected');
            
            const clickedCard = this.querySelector('.option-card');
            const clickedMark = this.querySelector('.selection-mark');
            
            if (clickedCard) {
                clickedCard.style.cssText = 'background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important; border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;';
            }
            if (clickedMark) {
                clickedMark.style.cssText = 'background: #10b981 !important; border-color: #10b981 !important; color: white !important; opacity: 1 !important;';
            }
            
            // THIRD: Verify the result
            setTimeout(() => {
                console.log('=== VERIFICATION ===');
                const verifyRadio = this.querySelector('input[type="radio"]');
                console.log('Radio final state:', verifyRadio ? verifyRadio.checked : 'not found');
                
                const allAnswerRadios = document.querySelectorAll('input[name="answer"]');
                console.log('All answer radios:', allAnswerRadios);
                const checkedCount = Array.from(allAnswerRadios).filter(r => r.checked).length;
                console.log('Total checked radios:', checkedCount);
                
                if (checkedCount === 0) {
                    console.log('❌ CRITICAL: No radios are checked! Form will fail validation.');
                } else {
                    console.log('✅ SUCCESS: Radio is properly checked');
                }
            }, 100);
            
            hideValidationError();
        });
    });
    
    // Also add direct event listeners to radio buttons for backup
    const radioInputs = form.querySelectorAll('input[type="radio"]');
    radioInputs.forEach((radio, index) => {
        radio.addEventListener('change', function() {
            console.log(`Radio ${index} changed:`, this.checked);
            
            // Clear all option styles
            const allOptions = form.querySelectorAll('.mcq-option-item');
            allOptions.forEach(opt => {
                opt.style.cssText = '';
                opt.classList.remove('selected');
                const optCard = opt.querySelector('.option-card');
                const optMark = opt.querySelector('.selection-mark');
                if (optCard) optCard.style.cssText = '';
                if (optMark) optMark.style.cssText = '';
            });
            
            if (this.checked) {
                // Style the parent option
                const parentOption = this.closest('.mcq-option-item');
                if (parentOption) {
                    console.log('Styling parent option from radio change');
                    parentOption.style.cssText = 'background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important; border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;';
                    parentOption.classList.add('selected');
                    
                    const optCard = parentOption.querySelector('.option-card');
                    const optMark = parentOption.querySelector('.selection-mark');
                    
                    if (optCard) {
                        optCard.style.cssText = 'background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important; border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;';
                    }
                    if (optMark) {
                        optMark.style.cssText = 'background: #10b981 !important; border-color: #10b981 !important; color: white !important; opacity: 1 !important;';
                    }
                }
            }
            
            hideValidationError();
        });
    });
    
    // UNIFIED MCQ handling for both single (radio) and multiple (checkbox)
    const mcqOptions = form.querySelectorAll('.mcq-option-item');
    console.log('Setting up unified MCQ options:', mcqOptions.length);

    mcqOptions.forEach((option, index) => {
        option.addEventListener('click', function(e) {
            // Don't trigger if clicking directly on the input
            if (e.target.type === 'radio' || e.target.type === 'checkbox') {
                console.log('Clicked directly on input, letting it handle itself');
                return;
            }
            
            const radio = this.querySelector('input[type="radio"]');
            const checkbox = this.querySelector('input[type="checkbox"]');
            const input = radio || checkbox;
            
            console.log('Found input:', input?.type, 'checked:', input?.checked);
            
            if (input && !input.disabled) {
                if (radio) {
                    // For radio buttons, clear all others in the same group first
                    const allRadios = form.querySelectorAll(`input[name="${radio.name}"]`);
                    allRadios.forEach(r => {
                        r.checked = false;
                        const parentOpt = r.closest('.mcq-option-item');
                        if (parentOpt) {
                            parentOpt.classList.remove('selected');
                        }
                    });
                    
                    // Set this radio to checked
                    radio.checked = true;
                    this.classList.add('selected');
                    
                } else if (checkbox) {
                    // For checkboxes, just toggle the state
                    checkbox.checked = !checkbox.checked;
                    
                    if (checkbox.checked) {
                        this.classList.add('selected');
                    } else {
                        this.classList.remove('selected');
                    }
                }
                
                // Update visuals and trigger events
                updateOptionVisuals();
                input.dispatchEvent(new Event('change', { bubbles: true }));
                hideValidationError();
                
                console.log(`Updated input state: checked=${input.checked}`);
            }
        });
    });
    
    // Note: updateMCQVisuals function removed - now using unified updateOptionVisuals()
    
    const tfOptions = form.querySelectorAll('.tf-option-item');
    tfOptions.forEach((option, index) => {
        option.addEventListener('click', function(e) {
            console.log(`TF Option ${index} clicked`);
            
            const radio = this.querySelector('input[type="radio"]');
            if (radio && !radio.disabled) {
                // Clear all TF options in the same group first
                const radioGroup = form.querySelectorAll(`input[name="${radio.name}"]`);
                radioGroup.forEach(r => {
                    const parentOpt = r.closest('.tf-option-item');
                    if (parentOpt) {
                        parentOpt.style.cssText = '';
                        parentOpt.classList.remove('selected');
                        const tfCard = parentOpt.querySelector('.tf-card');
                        const tfIndicator = parentOpt.querySelector('.tf-indicator');
                        if (tfCard) tfCard.style.cssText = '';
                        if (tfIndicator) tfIndicator.style.cssText = '';
                    }
                    r.checked = false;
                });
                
                // Check this radio and style it
                radio.checked = true;
                this.classList.add('selected');
                
                const tfCard = this.querySelector('.tf-card');
                const tfIndicator = this.querySelector('.tf-indicator');
                
                if (this.classList.contains('true-option')) {
                    if (tfCard) {
                        tfCard.style.cssText = 'background: linear-gradient(135deg, #d1fae5, #a7f3d0) !important; border-color: #10b981 !important;';
                    }
                    if (tfIndicator) {
                        tfIndicator.style.cssText = 'background: #10b981 !important; border-color: #10b981 !important; color: white !important;';
                    }
                } else {
                    if (tfCard) {
                        tfCard.style.cssText = 'background: linear-gradient(135deg, #fee2e2, #fca5a5) !important; border-color: #ef4444 !important;';
                    }
                    if (tfIndicator) {
                        tfIndicator.style.cssText = 'background: #ef4444 !important; border-color: #ef4444 !important; color: white !important;';
                    }
                }
                
                // Trigger events
                radio.dispatchEvent(new Event('change', { bubbles: true }));
                radio.dispatchEvent(new Event('input', { bubbles: true }));
                
                hideValidationError();
            }
        });
    });
    
    // Initial visual update
    updateOptionVisuals();
    
    // Add global debugging function
    window.debugRadios = function() {
        console.log('=== DEBUGGING RADIO BUTTONS ===');
        const allRadios = document.querySelectorAll('input[type="radio"]');
        console.log('Total radios found:', allRadios.length);
        
        allRadios.forEach((radio, idx) => {
            console.log(`Radio ${idx}:`, {
                name: radio.name,
                value: radio.value,
                checked: radio.checked,
                hasCheckedAttr: radio.hasAttribute('checked'),
                disabled: radio.disabled,
                defaultChecked: radio.defaultChecked
            });
        });
        
        const answerRadios = document.querySelectorAll('input[name="answer"]');
        console.log('Answer radios found:', answerRadios.length);
        
        const checkedAnswerRadios = document.querySelectorAll('input[name="answer"]:checked');
        console.log('Checked answer radios:', checkedAnswerRadios.length);
        
        console.log('=== END DEBUG ===');
        return {
            totalRadios: allRadios.length,
            answerRadios: answerRadios.length,
            checkedRadios: checkedAnswerRadios.length
        };
    };
    
    // Add test function to manually set radio
    window.testRadio = function(index) {
        const radios = document.querySelectorAll('input[name="answer"]');
        if (radios[index]) {
            console.log('Manually setting radio', index);
            radios.forEach(r => r.checked = false);
            radios[index].checked = true;
            console.log('Radio set. Current state:', radios[index].checked);
        }
    };
    
    // Add test function to manually test checkboxes
    window.testCheckbox = function(subIndex, optionIndex) {
        const checkbox = document.querySelector(`input[name="answer[${subIndex}][]"][value="${optionIndex}"]`);
        if (checkbox) {
            console.log('Manually toggling checkbox:', checkbox.name, checkbox.value);
            checkbox.checked = !checkbox.checked;
            console.log('Checkbox state:', checkbox.checked);
            
            // Update visuals using the unified function
            updateOptionVisuals();
            
            // Force a CSS update
            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Log the visual state
            const option = checkbox.closest('.mcq-option-item');
            if (option) {
                const optionCard = option.querySelector('.option-card');
                const selectionMark = option.querySelector('.selection-mark');
                console.log('Visual state after toggle:');
                console.log('- Option card background:', optionCard ? getComputedStyle(optionCard).background : 'none');
                console.log('- Selection mark background:', selectionMark ? getComputedStyle(selectionMark).background : 'none');
                console.log('- Option selected class:', option.classList.contains('selected'));
            }
        } else {
            console.log('Checkbox not found:', `answer[${subIndex}][]`, optionIndex);
        }
    };
    
    // Add debug function to check selection states
    window.debugSelection = function() {
        console.log('=== DEBUG SELECTION STATES ===');
        const mcqOptions = document.querySelectorAll('.mcq-option-item');
        mcqOptions.forEach((option, index) => {
            const radio = option.querySelector('input[type="radio"]');
            const checkbox = option.querySelector('input[type="checkbox"]');
            const input = radio || checkbox;
            
            console.log(`Option ${index}:`, {
                hasRadio: radio !== null,
                hasCheckbox: checkbox !== null,
                inputType: input ? input.type : 'none',
                inputName: input ? input.name : 'none',
                inputValue: input ? input.value : 'none',
                inputChecked: input ? input.checked : 'none',
                optionSelected: option.classList.contains('selected'),
                optionBackground: getComputedStyle(option).background
            });
        });
    };
    
    console.log('Debug functions added:');
    console.log('- debugRadios() - check all radio states');
    console.log('- testRadio(0) - manually set first radio (0,1,2,3)');
    console.log('- testCheckbox(0, 0) - manually toggle first checkbox of first sub-question');
    
    submitButton.addEventListener('click', function(e) {
        const isValid = validateForm();
        console.log('Form validation result:', isValid);
        
        if (!isValid) {
            e.preventDefault();
            
            // Show JavaScript validation error banner
            const jsErrorBanner = document.getElementById('js-validation-error');
            if (jsErrorBanner) {
                jsErrorBanner.classList.remove('hidden');
                jsErrorBanner.style.display = 'block';
            }
            
            // Scroll to top to show error
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return false;
        }
        
        // Hide error banner if form is valid
        const jsErrorBanner = document.getElementById('js-validation-error');
        if (jsErrorBanner) {
            jsErrorBanner.classList.add('hidden');
            jsErrorBanner.style.display = 'none';
        }
        
        // If valid, allow form submission
        console.log('Form is valid, allowing submission');
    });
    });
    
    // Debug CSRF token on page load and define form variable
    document.addEventListener('DOMContentLoaded', function() {
        // Define the form variable globally
        window.form = document.querySelector('form[method="POST"]');
        console.log('Form found:', window.form ? 'Yes' : 'No');
        
        // Test submit button click
        const submitButton = window.form ? window.form.querySelector('button[type="submit"]') : null;
        if (submitButton) {
            console.log('Submit button found:', submitButton.textContent.trim());
            submitButton.addEventListener('click', function(e) {
                console.log('=== SUBMIT BUTTON CLICKED ===');
            });
        } else {
            console.log('Submit button not found');
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        console.log('Page loaded - CSRF token available:', csrfToken ? 'Yes' : 'No');
        if (csrfToken) {
            console.log('CSRF token content:', csrfToken.content);
            console.log('CSRF token length:', csrfToken.content.length);
        }
        
        // Also check if @csrf directive is working
        const formToken = document.querySelector('input[name="_token"]');
        console.log('Form CSRF token available:', formToken ? 'Yes' : 'No');
        if (formToken) {
            console.log('Form CSRF token value:', formToken.value);
            console.log('Form CSRF token length:', formToken.value.length);
        }
        
                    // Debug MCQ Multiple checkboxes
        const questionType = '{{ $type }}';
        console.log('Question type:', questionType);
        
        if (questionType === 'mcq_multiple') {
            console.log('=== MCQ MULTIPLE DEBUG ===');
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            console.log('Total checkboxes found:', checkboxes.length);
            
            checkboxes.forEach((checkbox, index) => {
                console.log(`Checkbox ${index}:`, {
                    name: checkbox.name,
                    value: checkbox.value,
                    checked: checkbox.checked,
                    disabled: checkbox.disabled,
                    visible: checkbox.offsetParent !== null
                });
                
                // Add a visual indicator to show the checkbox is found
                const option = checkbox.closest('.mcq-option-item');
                if (option) {
                    option.style.border = '2px solid #10b981';
                    option.style.backgroundColor = '#f0fdf4';
                    console.log(`Checkbox ${index} is properly connected to option element`);
                } else {
                    console.log(`Checkbox ${index} is NOT connected to an option element`);
                }
            });
            
            const mcqOptions = document.querySelectorAll('.mcq-option-item');
            console.log('MCQ option items found:', mcqOptions.length);
            
            mcqOptions.forEach((option, index) => {
                const checkbox = option.querySelector('input[type="checkbox"]');
                const radio = option.querySelector('input[type="radio"]');
                const input = checkbox || radio;
                console.log(`MCQ option ${index}:`, {
                    element: option,
                    hasInput: input !== null,
                    inputType: input ? input.type : 'none',
                    inputName: input ? input.name : 'none',
                    inputValue: input ? input.value : 'none'
                });
                
                if (!input) {
                    option.style.border = '2px solid #ef4444';
                    option.style.backgroundColor = '#fef2f2';
                    console.log(`Option ${index} is missing input`);
                }
            });
        }
    });
    
    // Form submission handler
    if (window.form) {
        console.log('Setting up form submission handler...');
        window.form.addEventListener('submit', function(e) {
            console.log('=== FORM SUBMISSION HANDLER TRIGGERED ===');
            console.log('Form submission detected');
            
            // Validate form
            const isValid = validateForm();
            if (!isValid) {
                console.error('Form validation failed');
                e.preventDefault();
                
                // Show error message to user
                alert('Please select an answer before submitting.');
                return false;
            }
            
            console.log('Form validation passed, proceeding with submission');
            
            // For MCQ Multiple, ensure all checkboxes are properly included
            const questionType = '{{ $type }}';
            if (questionType === 'mcq_multiple') {
                console.log('Processing MCQ Multiple form data...');
                const formData = new FormData(window.form);
                
                // Log all form data for debugging
                console.log('Form data before submission:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                
                // Ensure all checked checkboxes are included
                const checkedCheckboxes = window.form.querySelectorAll('input[type="checkbox"]:checked');
                console.log('Checked checkboxes found:', checkedCheckboxes.length);
                checkedCheckboxes.forEach((checkbox, index) => {
                    console.log(`Checkbox ${index}: name=${checkbox.name}, value=${checkbox.value}`);
                });
                
                // Also log all checkboxes to see their state
                const allCheckboxes = window.form.querySelectorAll('input[type="checkbox"]');
                console.log('All checkboxes state:');
                allCheckboxes.forEach((checkbox, index) => {
                    console.log(`Checkbox ${index}: name=${checkbox.name}, value=${checkbox.value}, checked=${checkbox.checked}`);
                });
                
                // Log all MCQ options to see their state
                const allMCQOptions = window.form.querySelectorAll('.mcq-option-item');
                console.log('All MCQ options state:');
                allMCQOptions.forEach((option, index) => {
                    const checkbox = option.querySelector('input[type="checkbox"]');
                    const radio = option.querySelector('input[type="radio"]');
                    const input = checkbox || radio;
                    console.log(`MCQ Option ${index}: type=${input ? input.type : 'none'}, name=${input ? input.name : 'none'}, value=${input ? input.value : 'none'}, checked=${input ? input.checked : 'none'}`);
                });
            }
            
            // Show loading state
            const submitButton = window.form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Submitting...';
                submitButton.style.opacity = '0.7';
            }
            
            // Allow form to submit normally
            console.log('Form is valid, submitting...');
        });
    }
  </script>

</body>
</html>