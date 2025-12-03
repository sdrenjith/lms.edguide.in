<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions - {{ $course->name }} - {{ $subject->name }} - {{ $day->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <style>
        /* Custom header styles */
        .header-logo {
            transition: transform 0.2s ease-in-out;
        }
        .header-logo:hover {
            transform: scale(1.05);
        }
        
        .user-menu-button {
            transition: all 0.2s ease-in-out;
        }
        .user-menu-button:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
        }
        
        .mobile-menu-button {
            transition: all 0.2s ease-in-out;
        }
        .mobile-menu-button:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
        }
        
        /* Smooth dropdown animation */
        .user-dropdown {
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
            transform-origin: top right;
        }
        
        .user-dropdown.hidden {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        
        .user-dropdown:not(.hidden) {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    </style>
    
    <!-- Custom Translation Script -->
    <script type="text/javascript">
        // Detect current page language
        function detectLanguage() {
            // Check if any Google Translate elements exist
            var translateSelect = document.querySelector('.goog-te-combo');
            if (translateSelect) {
                return translateSelect.value || 'de';
            }
            
            // Fallback to detecting text
            var bodyText = document.body.innerText.toLowerCase();
            return bodyText.match(/[äöüß]/) ? 'de' : 'en';
        }

        // Perform translation
        function translatePage(targetLang) {
            // Method 1: Use Google Translate Element if available
            var translateSelect = document.querySelector('.goog-te-combo');
            if (translateSelect) {
                try {
                    translateSelect.value = targetLang;
                    translateSelect.dispatchEvent(new Event('change'));
                    
                    // Try to trigger translate button
                    var translateButton = document.querySelector('.goog-te-button button');
                    if (translateButton) {
                        translateButton.click();
                    }
                    return true;
                } catch (error) {
                    console.error('Google Translate method failed:', error);
                }
            }

            // Method 2: Fallback to browser's built-in translation
            if ('translate' in window.navigator) {
                try {
                    window.navigator.translate.translate(targetLang);
                    return true;
                } catch (error) {
                    console.error('Browser translation failed:', error);
                }
            }

            // Method 3: Reload with translation parameter
            var currentUrl = window.location.href;
            var separator = currentUrl.includes('?') ? '&' : '?';
            window.location.href = currentUrl + separator + 'lang=' + targetLang;

            return false;
        }

        // Toggle language function
        function toggleLanguage() {
            var currentLang = detectLanguage();
            var btn = document.getElementById('language-toggle-btn');
            
            if (currentLang === 'de') {
                translatePage('en');
                if (btn) {
                    btn.querySelector('.language-toggle-text').textContent = 'Convert to German';
                }
            } else {
                translatePage('de');
                if (btn) {
                    btn.querySelector('.language-toggle-text').textContent = 'Convert to English';
                }
            }
        }

        // Ensure translation is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Add global translate function for potential external calls
            window.toggleLanguage = toggleLanguage;
        });
    </script>

    <!-- Advanced Translation Script -->
    <script>
        // Comprehensive Translation Mechanism
        (function() {
            // Translation State Management
            const TranslationState = {
                currentLang: 'de',
                isTranslating: false
            };

            // Translation Utility
            const TranslationEngine = {
                // Translate entire page via server
                translatePage: function(targetLang) {
                    // Prevent multiple translations
                    if (TranslationState.isTranslating) return;
                    TranslationState.isTranslating = true;

                    // Show loading indicator
                    this.showLoadingIndicator();

                    // Fetch CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Send translation request
                    fetch('{{ route("translate.page") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ lang: targetLang })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.applyTranslation(data.content);
                            this.updateUI(targetLang);
                        } else {
                            this.fallbackTranslation(targetLang);
                        }
                    })
                    .catch(error => {
                        console.error('Translation error:', error);
                        this.fallbackTranslation(targetLang);
                    })
                    .finally(() => {
                        TranslationState.isTranslating = false;
                        this.hideLoadingIndicator();
                    });
                },

                // Apply translated content
                applyTranslation: function(translatedContent) {
                    // Update specific page elements
                    if (translatedContent.title) {
                        const titleElements = document.querySelectorAll('h1, .page-title');
                        titleElements.forEach(el => el.textContent = translatedContent.title);
                    }

                    if (translatedContent.instructions) {
                        const instructionElements = document.querySelectorAll('.page-instructions');
                        instructionElements.forEach(el => el.textContent = translatedContent.instructions);
                    }
                },

                // Fallback translation method
                fallbackTranslation: function(targetLang) {
                    // Manual translation of key elements
                    const translations = {
                        'de_to_en': {
                            'Listening Questions': 'Listening Questions',
                            'Complete all listening questions first': 'Complete all listening questions first'
                        },
                        'en_to_de': {
                            'Listening Questions': 'Listening Questions',
                            'Complete all listening questions first': 'Beenden Sie zuerst alle Listening-Fragen'
                        }
                    };

                    const key = `${TranslationState.currentLang}_to_${targetLang}`;
                    const translationMap = translations[key] || {};

                    // Replace text in key elements
                    document.body.innerHTML = document.body.innerHTML.replace(
                        new RegExp(Object.keys(translationMap).join('|'), 'gi'),
                        matched => translationMap[matched] || matched
                    );
                },

                // Update UI after translation
                updateUI: function(targetLang) {
                    const btn = document.getElementById('language-toggle-btn');
                    if (btn) {
                        btn.querySelector('.language-toggle-text').textContent = 
                            targetLang === 'en' ? 'Convert to German' : 'Convert to English';
                    }
                    TranslationState.currentLang = targetLang;
                },

                // Show loading indicator
                showLoadingIndicator: function() {
                    const loadingIndicator = document.createElement('div');
                    loadingIndicator.id = 'translation-loading';
                    loadingIndicator.innerHTML = `
                        <div class="fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center">
                            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500"></div>
                        </div>
                    `;
                    document.body.appendChild(loadingIndicator);
                },

                // Hide loading indicator
                hideLoadingIndicator: function() {
                    const loadingIndicator = document.getElementById('translation-loading');
                    if (loadingIndicator) {
                        loadingIndicator.remove();
                    }
                }
            };

            // Global toggle function
            window.toggleLanguage = function() {
                const currentLang = TranslationState.currentLang;
                TranslationEngine.translatePage(currentLang === 'de' ? 'en' : 'de');
            };

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', () => {
                // Add click listener to translation toggle button
                const btn = document.getElementById('language-toggle-btn');
                if (btn) {
                    btn.addEventListener('click', window.toggleLanguage);
                }
            });
        })();
    </script>

    <style>
        /* Google Translate Styling */
        .translate-container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            height: 100%;
        }
        
        #google_translate_element,
        #google_translate_element_mobile {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        #google_translate_element .goog-te-gadget,
        #google_translate_element_mobile .goog-te-gadget {
            font-family: inherit !important;
            font-size: 12px !important;
            color: transparent !important;
        }
        
        #google_translate_element .goog-te-combo,
        #google_translate_element_mobile .goog-te-combo {
            background: white !important;
            border: 1px solid #d1d5db !important;
            border-radius: 6px !important;
            padding: 4px 8px !important;
            font-size: 12px !important;
            color: #374151 !important;
            outline: none !important;
            transition: all 0.2s !important;
            min-width: 120px !important;
            max-width: 140px !important;
            height: 32px !important;
            cursor: pointer !important;
            appearance: none !important;
            -webkit-appearance: none !important;
        }
        
        #google_translate_element .goog-te-combo:focus,
        #google_translate_element_mobile .goog-te-combo:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        /* Hide original Google Translate elements */
        .goog-te-banner-frame.skiptranslate,
        .goog-te-banner,
        #goog-gt-tt,
        .goog-te-balloon-frame {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            position: absolute !important;
            top: -9999px !important;
            left: -9999px !important;
        }
        
        body {
            top: 0px !important;
        }
        
        /* Custom dropdown arrow */
        #google_translate_element .goog-te-combo {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23374151' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 8px center !important;
            padding-right: 24px !important;
        }

        /* Language Toggle Button Styling */
        .language-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
            outline: none;
            gap: 8px;
            height: 36px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        
        .language-toggle-btn:hover {
            background-color: #e5e7eb;
            border-color: #9ca3af;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .language-toggle-btn:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .language-toggle-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            color: #6b7280;
        }
        
        .language-toggle-text {
            white-space: nowrap;
            color: #374151;
        }

        /* Prevent Google Translate from adding extra space */
        .goog-te-banner-frame.skiptranslate {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
        }
        
        body {
            top: 0px !important;
        }
    </style>
</head>
<body class="bg-gray-50">
    @php
        use Illuminate\Support\Str;
    @endphp
    <div class="min-h-screen flex flex-col">
        <!-- Enhanced Navigation Header -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Left Side: Back Button & Logo -->
                    <div class="flex items-center space-x-3 sm:space-x-4 min-w-0 flex-1">
                        <a href="{{ route('filament.student.pages.courses') }}" 
                           class="flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200 min-w-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            <span class="hidden sm:inline text-sm sm:text-base">Back to Courses</span>
                            <span class="sm:hidden text-sm">Back</span>
                        </a>
                        
                        <div class="hidden sm:block w-px h-6 sm:h-8 bg-gray-300"></div>
                        
                        <!-- Logo -->
                        <img src="{{ asset('/edguide-logo.png') }}" alt="EdGuide" class="header-logo h-8 sm:h-10 md:h-12 flex-shrink-0" style="width: auto; max-width: 120px; sm:max-width: 140px;" />
                    </div>
                    
                    <!-- Right Side: User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button id="mobileMenuButton" class="mobile-menu-button md:hidden flex items-center p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        
                        <!-- Desktop User Menu -->
                        <div class="hidden md:block relative group">
                            <button id="userMenuButton" class="user-menu-button flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none p-2">
                                <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full border-2 border-gray-200" />
                                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="userMenuDropdown" class="user-dropdown hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                                <a href="{{ route('filament.student.pages.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
                <div class="px-4 py-3 space-y-2">
                    <a href="{{ route('filament.student.pages.dashboard') }}" class="block px-4 py-3 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Dashboard</a>
                    <a href="{{ route('filament.student.pages.courses') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Courses</a>
                    {{-- <a href="{{ route('filament.student.pages.tests') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.tests') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Test</a> --}}
                    <a href="{{ route('filament.student.pages.study-materials') }}" class="block px-4 py-3 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Study Materials</a>
                    <a href="{{ route('filament.student.pages.profile') }}" class="block px-4 py-3 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Profile</a>
                    {{-- <a href="{{ route('filament.student.pages.opinion-verification') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.opinion-verification') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Opinion Verification</a> --}}
                    <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="block px-4 py-3 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Live Classes</a>
                    {{-- <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="block px-4 py-3 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Doubt Clearance</a> --}}
                    
                    <!-- Mobile Google Translate -->
                    <div class="flex items-center px-4 py-3">
                        <span class="text-sm text-gray-600 mr-2">Translate:</span>
                        <div id="google_translate_element_mobile" class="inline-block"></div>
                    </div>
                    
                    <!-- Mobile User Menu -->
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex items-center px-4 py-3 bg-gray-50 rounded-lg mb-2">
                            <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" alt="{{ auth()->user()->name }}" class="h-10 w-10 rounded-full border-2 border-gray-200" />
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                                <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-base font-medium text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 w-full mx-auto px-4 py-6 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Access Denied!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if(session('info'))
                <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Info:</strong>
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            <!-- Page Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4 sm:mb-6">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-cyan-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 truncate">{{ $subject->name }} Questions</h1>
                            <p class="text-sm sm:text-base text-gray-600 truncate">{{ $course->name }} - {{ $day->title }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-6">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-xs sm:text-sm text-gray-600">{{ $totalQuestions }} Questions</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-xs sm:text-sm text-gray-600">{{ $completedQuestions }} Completed</span>
                            </div>
                        </div>
                        <div class="text-xs sm:text-sm text-gray-500">
                            @if($allSubjectQuestionsCompleted && $subjectResults)
                                <span class="font-semibold text-{{ $subjectResults['grade'] === 'F' ? 'red' : ($subjectResults['percentage'] >= 80 ? 'green' : 'yellow') }}-600 break-words">
                                    <span class="hidden sm:inline">Mark: {{ $subjectResults['earned_points'] }}/{{ $subjectResults['total_points'] }} ({{ $subjectResults['percentage'] }}%)</span>
                                    <span class="sm:hidden">{{ $subjectResults['percentage'] }}%</span>
                                </span>
                            @else
                                Progress: {{ $progressPercentage }}%
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions List -->
            @if($questions->count() > 0)
                <div class="grid gap-4 md:gap-6 w-full">
                    @foreach($questions as $index => $question)
                        @php
                            $isAnswered = in_array($question->id, $answeredQuestionIds);
                            $studentAnswer = $studentAnswers->where('question_id', $question->id)->first();
                            $isOpinionQuestion = $question->questionType && $question->questionType->name === 'opinion';
                        @endphp
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 w-full">
                            <div class="p-4 sm:p-6">
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between space-y-4 sm:space-y-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="flex-shrink-0 w-8 h-8 {{ $isAnswered ? 'bg-green-500' : 'bg-gray-400' }} rounded-full flex items-center justify-center">
                                                @if($isAnswered)
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <span class="text-white text-sm font-medium">{{ $index + 1 }}</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">Question {{ $index + 1 }}</h3>
                                                <div class="flex flex-wrap items-center gap-2 sm:space-x-4 text-xs sm:text-sm text-gray-500 mt-1">
                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $question->questionType->name ?? 'Unknown Type' }}</span>
                                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ $question->points ?? 1 }} {{ ($question->points ?? 1) == 1 ? 'point' : 'points' }}</span>
                                                    @if($isAnswered)
                                                        @if($isOpinionQuestion)
                                                            @if($studentAnswer && $studentAnswer->verification_status === 'pending')
                                                                <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full font-medium">Awaiting Teacher</span>
                                                            @elseif($studentAnswer && $studentAnswer->verification_status === 'verified_correct')
                                                                <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full font-medium">Correct</span>
                                                            @elseif($studentAnswer && $studentAnswer->verification_status === 'verified_incorrect')
                                                                <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full font-medium">Needs Revision</span>
                                                            @else
                                                                <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full font-medium">Submitted</span>
                                                            @endif
                                                        @else
                                                            <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full font-medium">Completed</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($question->instruction)
                                            <div class="mb-3 sm:mb-4 p-3 sm:p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                                <p class="text-xs sm:text-sm text-blue-800 leading-relaxed">{{ $question->instruction }}</p>
                                            </div>
                                        @endif
                                        
                                        @if($question->question_text)
                                            <div class="mb-3 sm:mb-4">
                                                <p class="text-sm sm:text-base text-gray-700 leading-relaxed">{{ Str::limit($question->question_text, 200) }}</p>
                                            </div>
                                        @endif
                                        
                                        @if($isOpinionQuestion && $studentAnswer && $studentAnswer->verification_comment)
                                            <div class="mb-3 sm:mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                <h4 class="text-xs font-medium text-yellow-800 mb-1">Teacher's Comment:</h4>
                                                <p class="text-sm text-gray-700">{{ $studentAnswer->verification_comment }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-shrink-0 w-full sm:w-auto sm:ml-4">
                                        @if($isAnswered && !$allSubjectQuestionsCompleted)
                                            <span class="inline-flex items-center justify-center w-full sm:w-auto px-3 py-2 text-xs sm:text-sm font-medium text-gray-500 bg-gray-100 rounded-md">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="hidden sm:inline">Complete all {{ $subject->name }} questions first</span>
                                                <span class="sm:hidden">Complete all questions first</span>
                                            </span>
                                        @elseif($isAnswered)
                                            <a href="{{ route('student.questions.answer', $question->id) }}" 
                                               class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Re-attempt
                                            </a>
                                        @else
                                            <a href="{{ route('student.questions.answer', $question->id) }}" 
                                               class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors duration-200">
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m2 2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h8z"></path>
                                                </svg>
                                                Start Question
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($allSubjectQuestionsCompleted && $subjectResults)
                    <!-- Results Summary Card -->
                    <div class="mt-6 sm:mt-8 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-xl p-4 sm:p-6 shadow-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 space-y-2 sm:space-y-0">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <span class="truncate">{{ $subject->name }} - {{ $day->title }} Results</span>
                            </h3>
                            <div class="text-center sm:text-right">
                                <div class="text-xl sm:text-2xl font-bold text-{{ $subjectResults['grade'] === 'F' ? 'red' : ($subjectResults['percentage'] >= 80 ? 'green' : 'yellow') }}-600">
                                    {{ $subjectResults['percentage'] }}%
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4">
                            <div class="text-center p-2 sm:p-3 bg-white rounded-lg border">
                                <div class="text-base sm:text-lg font-bold text-blue-600">{{ $subjectResults['total_questions'] }}</div>
                                <div class="text-xs text-gray-600">Total Questions</div>
                            </div>
                            <div class="text-center p-2 sm:p-3 bg-white rounded-lg border">
                                <div class="text-base sm:text-lg font-bold text-green-600">{{ $subjectResults['correct_answers'] }}</div>
                                <div class="text-xs text-gray-600">Correct</div>
                            </div>
                            <div class="text-center p-2 sm:p-3 bg-white rounded-lg border">
                                <div class="text-base sm:text-lg font-bold text-red-600">{{ $subjectResults['wrong_answers'] }}</div>
                                <div class="text-xs text-gray-600">Wrong</div>
                            </div>
                            <div class="text-center p-2 sm:p-3 bg-white rounded-lg border">
                                <div class="text-sm sm:text-lg font-bold text-purple-600">{{ $subjectResults['earned_points'] }}/{{ $subjectResults['total_points'] }}</div>
                                <div class="text-xs text-gray-600">Points</div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg p-4 border">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                <span class="text-sm text-gray-600">{{ $subjectResults['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-3 rounded-full transition-all duration-300" 
                                     style="width: {{ $subjectResults['percentage'] }}%"></div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-600 mb-2">
                                @if($subjectResults['percentage'] >= 90)
                                    🎉 Outstanding performance! You've mastered {{ $subject->name }}!
                                @elseif($subjectResults['percentage'] >= 80)
                                    🌟 Excellent work! You have a strong understanding of {{ $subject->name }}!
                                @elseif($subjectResults['percentage'] >= 70)
                                    👍 Good job! Consider reviewing the incorrect {{ $subject->name }} answers.
                                @elseif($subjectResults['percentage'] >= 60)
                                    📚 Fair performance. More {{ $subject->name }} practice will help improve your score.
                                @else
                                    💪 Keep practicing {{ $subject->name }}! Review the material and try again.
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">You can now re-attempt any question to improve your {{ $subject->name }} score!</p>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Questions Available</h3>
                    <p class="text-gray-500 mb-4">There are no questions available for this combination yet.</p>
                    <a href="{{ route('filament.student.pages.courses') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Courses
                    </a>
                </div>
            @endif
        </main>

        <!-- Enhanced Footer -->
        <div class="bg-gradient-to-r from-gray-100 to-gray-200 py-1 w-full"></div>
        <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-6 sm:py-8 w-full mt-auto">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-0">
                    <div class="text-center sm:text-left">
                        <p class="text-lg sm:text-xl font-semibold">EdGuide</p>
                        <p class="text-sm text-gray-300 mt-1">Empowering learning</p>
                    </div>
                    <div class="text-center sm:text-right">
                        <p class="text-sm text-gray-300">© 2025 All rights reserved</p>
                        <p class="text-xs text-gray-400 mt-1">Terms & Privacy Policy</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Security Watermark -->
    @include('components.student-watermark')

    <script>
        // Mobile menu functionality
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobileMenuButton');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                // Change icon to X
                button.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            } else {
                menu.classList.add('hidden');
                // Change icon back to hamburger
                button.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
            }
        }
        
        function closeMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobileMenuButton');
            menu.classList.add('hidden');
            // Change icon back to hamburger
            button.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
        }

        // User dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu button
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', toggleMobileMenu);
            }
            
            // User dropdown
            const userMenuButton = document.getElementById('userMenuButton');
            const userMenuDropdown = document.getElementById('userMenuDropdown');
            if (userMenuButton && userMenuDropdown) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenuDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userMenuButton.contains(e.target)) {
                        userMenuDropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html> 