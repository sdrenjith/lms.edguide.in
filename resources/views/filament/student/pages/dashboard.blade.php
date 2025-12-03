@php
    $user = auth()->user();
    
    // Get assigned courses
    $assignedCourses = $user->assignedCourses();
    
    // Get assigned days
    $assignedDays = $user->assignedDays();
    $assignedDayIds = $assignedDays->pluck('id')->toArray();
    
    // Get all subjects
    $subjects = \App\Models\Subject::all();
    
    // Get study materials count
    $totalNotes = \App\Models\Note::whereIn('course_id', $assignedCourses->pluck('id'))->count();
    $totalVideos = \App\Models\Video::whereIn('course_id', $assignedCourses->pluck('id'))->count();
    
    // Get answered questions for progress
    $answeredQuestionIds = \App\Models\StudentAnswer::where('user_id', $user->id)->pluck('question_id')->toArray();
    
    // Calculate day progress
    $totalAssignedDays = $assignedDays->count();
    $completedDays = 0;
    $pendingDays = 0;
    
    foreach($assignedDays as $day) {
        $dayQuestions = \App\Models\Question::where('day_id', $day->id)->get();
        $dayAnsweredCount = $dayQuestions->filter(function($q) use ($answeredQuestionIds) {
            return in_array($q->id, $answeredQuestionIds);
        })->count();
        
        if($dayQuestions->count() > 0 && $dayAnsweredCount == $dayQuestions->count()) {
            $completedDays++;
        } else {
            $pendingDays++;
        }
    }
    
    // Calculate test progress
    $assignedTests = \App\Models\Test::where('is_active', true)
        ->whereIn('course_id', $assignedCourses->pluck('id'))
        ->get();
    
    $totalTests = $assignedTests->count();
    $completedTests = 0;
    $passedTests = 0;
    $testResults = [];
    
    foreach($assignedTests as $test) {
        $testQuestions = $test->questions()->where('is_active', true)->get();
        $testQuestionIds = $testQuestions->pluck('id');
        $testStudentAnswers = \App\Models\StudentAnswer::where('user_id', $user->id)
            ->whereIn('question_id', $testQuestionIds)
            ->get()->keyBy('question_id');
        
        $testAnsweredCount = $testStudentAnswers->count();
        $testTotalQuestions = $testQuestions->count();
        
        $testResult = [
            'name' => $test->name,
            'status' => 'Not Started',
            'completed' => false,
            'passed' => false,
            'earnedPoints' => 0,
            'totalPoints' => $test->total_score,
            'passmark' => $test->passmark
        ];
        
        if($testTotalQuestions > 0 && $testAnsweredCount === $testTotalQuestions) {
            $completedTests++;
            $testResult['completed'] = true;
            
            // Check if test is passed
            $earnedPoints = 0;
            foreach ($testQuestions as $question) {
                if ($testStudentAnswers->has($question->id)) {
                    $studentAnswer = $testStudentAnswers->get($question->id);
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
            
            $testResult['earnedPoints'] = $earnedPoints;
            $passmark = $test->passmark;
            
            if ($earnedPoints >= $passmark) {
                $passedTests++;
                $testResult['passed'] = true;
                $testResult['status'] = 'Passed';
            } else {
                $testResult['status'] = 'Failed';
            }
        } elseif($testAnsweredCount > 0) {
            $testResult['status'] = 'In Progress';
        }
        
        $testResults[] = $testResult;
    }
@endphp

<x-filament-panels::page>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .fi-header { display: none !important; }
        body { background-color: white !important; }
        html { background-color: white !important; }
        .bg-black, .bg-gray-900, .bg-gray-800, .bg-gray-700 { background-color: white !important; }
        .text-white { color: #14b8a6 !important; }
        .text-gray-900 { color: #1f2937 !important; }
        .text-gray-600 { color: #4b5563 !important; }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        /* Hide scrollbar for IE, Edge and Firefox */
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    
    <div class="bg-white min-h-screen">
    <div class="min-h-screen bg-white flex flex-col">
        <!-- Enhanced Navigation Header -->
        <nav class="bg-white shadow-sm sticky top-0 z-50 w-full border-b border-gray-200">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <img src="{{ asset('/edguide-logo.png') }}" alt="Logo" class="h-10 sm:h-12 mr-4" style="width: 140px;" />
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">

                        
                        <!-- User Menu -->
                        <div class="relative group">
                            <button id="userMenuButton" class="flex items-center space-x-2 text-[#14b8a6] hover:text-gray-900 focus:outline-none">
                                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="{{ $user->name }}" class="h-8 w-8 rounded-full border-2 border-gray-200" />
                                <span class="text-sm font-medium">{{ $user->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="{{ route('filament.student.pages.profile') }}" class="block px-4 py-2 text-sm text-[#14b8a6] hover:bg-gray-100">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-[#14b8a6] hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button id="mobileMenuButton" class="text-[#14b8a6] hover:text-gray-900 focus:outline-none" onclick="toggleMobileMenu()">
                            <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex justify-center border-t border-gray-200 py-3">
                    <div class="flex space-x-8">
                        <a href="{{ route('filament.student.pages.dashboard') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.dashboard') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Dashboard</a>
                        <a href="{{ route('filament.student.pages.courses') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Courses</a>
                        {{-- <a href="{{ route('filament.student.pages.tests') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.tests') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Test</a> --}}
                        <a href="{{ route('filament.student.pages.study-materials') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Study Materials</a>
                        <a href="{{ route('filament.student.pages.profile') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Profile</a>
                        {{-- <a href="{{ route('filament.student.pages.opinion-verification') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.opinion-verification') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Opinion Verification</a> --}}
                        <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.speaking-sessions') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Live Classes</a>
                        <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Doubt Clearance</a>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
                <div class="px-4 py-3 space-y-2">
                    <a href="{{ route('filament.student.pages.dashboard') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.dashboard') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Dashboard</a>
                    <a href="{{ route('filament.student.pages.courses') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Courses</a>
                    {{-- <a href="{{ route('filament.student.pages.tests') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.tests') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Test</a> --}}
                    <a href="{{ route('filament.student.pages.study-materials') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Study Materials</a>
                    <a href="{{ route('filament.student.pages.profile') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Profile</a>
                    {{-- <a href="{{ route('filament.student.pages.opinion-verification') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.opinion-verification') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Opinion Verification</a> --}}
                    <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.speaking-sessions') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Live Classes</a>
                    <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Doubt Clearance</a>
                    
                    <!-- Mobile User Menu -->
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex items-center px-4 py-3 bg-gray-50 rounded-lg mb-2">
                            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full border-2 border-gray-200" />
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
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
        <main class="flex-1 w-full">
            <div class="w-full px-4 py-6 sm:py-8 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8 sm:mb-10">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="mb-4 sm:mb-0">
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                🏠 Welcome to <span class="text-[#14b8a6]">EdGuide</span>
                            </h1>
                            <p class="text-[#14b8a6] text-base sm:text-lg" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                Your learning journey starts here
                            </p>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-[#14b8a6]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ \Carbon\Carbon::now()->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 md:gap-10">
                    <!-- Card 1: Assigned Courses -->
                    <a href="{{ route('filament.student.pages.courses') }}" class="bg-white rounded-lg shadow-sm overflow-hidden transform transition-transform duration-200 hover:scale-[1.02] block border border-gray-200">
                        <div class="bg-[#14b8a6] p-3 sm:p-4 text-white flex justify-between items-center">
                            <span class="text-sm sm:text-base font-medium">My Courses</span>
                            <span class="text-lg sm:text-xl cursor-pointer hover:text-gray-200">📚</span>
                        </div>
                        <div class="p-3 sm:p-4 flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gray-700 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-[#14b8a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            @if($assignedCourses->count() > 0)
                                <div class="text-center w-full">
                                    <div class="text-xs text-[#14b8a6] mb-2">Course Assigned</div>
                                    <div class="text-2xl font-bold text-[#14b8a6] bg-gray-700 px-4 py-2 rounded-lg">
                                        {{ $assignedCourses->first()->name }}
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-[#14b8a6] text-center">No courses assigned yet</p>
                            @endif
                        </div>
                    </a>

                    <!-- Card 2: Course Subjects -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden transform transition-transform duration-200 hover:scale-[1.02] block border border-gray-200">
                        <div class="bg-[#14b8a6] p-3 sm:p-4 text-white flex justify-between items-center">
                            <span class="text-sm sm:text-base font-medium">Course Subjects</span>
                            <span class="text-lg sm:text-xl cursor-pointer hover:text-gray-200">📖</span>
                        </div>
                        <div class="p-3 sm:p-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-[#14b8a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            @if($subjects->count() > 0)
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($subjects as $subject)
                                        <div class="bg-gray-700 rounded-lg px-2 py-1 text-center">
                                            <span class="text-xs font-medium text-[#14b8a6]">{{ $subject->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-[#14b8a6] text-center">No subjects available</p>
                            @endif
                        </div>
                    </div>

                    <!-- Card 3: Study Materials Count -->
                    <a href="{{ route('filament.student.pages.study-materials') }}" class="bg-white rounded-lg shadow-sm overflow-hidden transform transition-transform duration-200 hover:scale-[1.02] block border border-gray-200">
                        <div class="bg-[#14b8a6] p-3 sm:p-4 text-white flex justify-between items-center">
                            <span class="text-sm sm:text-base font-medium">Study Materials</span>
                            <span class="text-lg sm:text-xl cursor-pointer hover:text-gray-200">📑</span>
                        </div>
                        <div class="p-3 sm:p-4 flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gray-700 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-[#14b8a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="text-center w-full">
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-gray-700 rounded-lg p-2">
                                        <div class="text-lg font-bold text-[#14b8a6]">{{ $totalNotes }}</div>
                                        <div class="text-xs text-[#14b8a6]">📄 Notes</div>
                                    </div>
                                    <div class="bg-gray-700 rounded-lg p-2">
                                        <div class="text-lg font-bold text-[#14b8a6]">{{ $totalVideos }}</div>
                                        <div class="text-xs text-[#14b8a6]">🎥 Videos</div>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-[#14b8a6]">Available study resources</p>
                            </div>
                        </div>
                    </a>

                    <!-- Card 4: Day Progress -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden transform transition-transform duration-200 hover:scale-[1.02] block border border-gray-200">
                        <div class="bg-[#14b8a6] p-3 sm:p-4 text-white flex justify-between items-center">
                            <span class="text-sm sm:text-base font-medium">Day Progress</span>
                            <span class="text-lg sm:text-xl cursor-pointer hover:text-gray-200">📊</span>
                        </div>
                        <div class="p-3 sm:p-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-[#14b8a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between bg-gray-700 rounded-lg px-2 py-1">
                                    <span class="text-xs font-medium text-[#14b8a6]">✅ Completed</span>
                                    <span class="text-xs font-bold text-[#14b8a6]">{{ $completedDays }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-gray-700 rounded-lg px-2 py-1">
                                    <span class="text-xs font-medium text-[#14b8a6]">⏳ Pending</span>
                                    <span class="text-xs font-bold text-[#14b8a6]">{{ $pendingDays }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-gray-700 rounded-lg px-2 py-1">
                                    <span class="text-xs font-medium text-[#14b8a6]">🔓 Unlocked</span>
                                    <span class="text-xs font-bold text-[#14b8a6]">{{ $totalAssignedDays }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Separator Section -->
        <div class="bg-gray-200 py-2 w-full"></div>

        <!-- New Design Section -->
        <section class="w-full bg-gray-50">
            <div class="w-full px-4 py-4 sm:py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6">
                <div class="flex-1 w-full lg:w-auto">
                    <h2 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4 text-left text-gray-900">EdGuide - Linguist Since 1971</h2>
                    <p class="text-sm sm:text-base text-[#14b8a6]">We Are The Best Choice For Your Dream</p>
                </div>
                <div class="flex-1 w-full lg:w-auto">
                    @php
                        // Calculate overall progress
                        $user = auth()->user();
                        $assignedCourseIds = $user->assignedCourses()->pluck('id')->toArray();
                        $assignedDayIds = $user->assignedDays()->pluck('id')->toArray();
                        
                        $totalQuestions = \App\Models\Question::whereIn('course_id', $assignedCourseIds)
                            ->whereIn('day_id', $assignedDayIds)
                            ->where('is_active', true)
                            ->count();
                        
                        $answeredQuestions = \App\Models\StudentAnswer::where('user_id', $user->id)
                            ->whereHas('question', function($q) use ($assignedCourseIds, $assignedDayIds) {
                                $q->whereIn('course_id', $assignedCourseIds)
                                  ->whereIn('day_id', $assignedDayIds)
                                  ->where('is_active', true);
                            })
                            ->count();
                        
                        $progressPercentage = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
                        
                        // Motivational message based on progress
                        $motivationalMessage = '';
                        if ($progressPercentage >= 90) {
                            $motivationalMessage = "🎉 Almost there! You're doing amazing!";
                        } elseif ($progressPercentage >= 75) {
                            $motivationalMessage = "🚀 Great progress! Keep it up!";
                        } elseif ($progressPercentage >= 50) {
                            $motivationalMessage = "💪 You're halfway there!";
                        } elseif ($progressPercentage >= 25) {
                            $motivationalMessage = "📚 Good start! Keep learning!";
                        } else {
                            $motivationalMessage = "🌟 Every question counts!";
                        }
                    @endphp
                    <div class="bg-orange-500 p-4 sm:p-6 text-white rounded-lg">
                        <h3 class="text-base sm:text-lg font-semibold">Course Progress</h3>
                        <p class="mt-2 text-sm sm:text-base">{{ $answeredQuestions }}/{{ $totalQuestions }} questions completed ({{ $progressPercentage }}%)</p>
                        <div class="w-full bg-orange-400 rounded-full h-2 mt-3 mb-3">
                            <div class="bg-white h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <p class="text-sm mb-3">{{ $motivationalMessage }}</p>
                        <a href="{{ route('filament.student.pages.courses') }}" class="mt-3 sm:mt-4 bg-white text-orange-500 px-3 sm:px-4 py-2 rounded inline-block text-sm sm:text-base font-medium hover:bg-gray-50 transition-colors duration-200">Continue Learning</a>
                    </div>
                </div>
            </div>
            </div>
        </section>

        <!-- Test Progress Section -->
        <section class="w-full bg-white">
            <div class="w-full px-4 py-4 sm:py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6">
                    <div class="flex-1 w-full lg:w-auto">
                        <h2 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4 text-left text-gray-900">Test Performance</h2>
                        <p class="text-sm sm:text-base text-[#14b8a6]">Track your test completion and success rate</p>
                    </div>
                    <div class="flex-1 w-full lg:w-auto">
                        @php
                            $testProgressPercentage = $totalTests > 0 ? round(($completedTests / $totalTests) * 100) : 0;
                            $testPassPercentage = $completedTests > 0 ? round(($passedTests / $completedTests) * 100) : 0;
                            
                            // Motivational message based on test progress
                            $testMotivationalMessage = '';
                            if ($totalTests === 0) {
                                $testMotivationalMessage = "📝 No tests assigned yet";
                            } elseif ($completedTests === 0) {
                                $testMotivationalMessage = "🚀 Start your first test!";
                            } elseif ($testPassPercentage >= 90) {
                                $testMotivationalMessage = "🎉 Outstanding test performance!";
                            } elseif ($testPassPercentage >= 75) {
                                $testMotivationalMessage = "⭐ Great test results!";
                            } elseif ($testPassPercentage >= 50) {
                                $testMotivationalMessage = "💪 Good test progress!";
                            } else {
                                $testMotivationalMessage = "📚 Keep practicing for better results!";
                            }
                        @endphp
                        <div class="bg-[#14b8a6] p-4 sm:p-6 text-white rounded-lg">
                            <h3 class="text-base sm:text-lg font-semibold">Test Progress</h3>
                            
                            <!-- Overall Summary -->
                            <div class="mt-3 mb-4 p-3 bg-gray-700 rounded-lg">
                                <p class="text-sm font-medium">Overall Summary</p>
                                <p class="text-sm">{{ $completedTests }}/{{ $totalTests }} tests completed ({{ $testProgressPercentage }}%)</p>
                                @if($completedTests > 0)
                                    <p class="text-sm">{{ $passedTests }}/{{ $completedTests }} tests passed ({{ $testPassPercentage }}%)</p>
                                @endif
                            </div>
                            
                            <!-- Individual Test Results -->
                            @if($totalTests > 0)
                                <div class="space-y-2 mb-4">
                                    @foreach($testResults as $test)
                                        <div class="flex items-center justify-between p-2 bg-purple-400 rounded-lg">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium truncate">{{ $test['name'] }}</p>
                                                @if($test['completed'])
                                                    <p class="text-xs opacity-90">{{ $test['earnedPoints'] }}/{{ $test['totalPoints'] }} points</p>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                @if($test['status'] === 'Passed')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Passed
                                                    </span>
                                                @elseif($test['status'] === 'Failed')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Failed
                                                    </span>
                                                @elseif($test['status'] === 'In Progress')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        In Progress
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                        Not Started
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-700 rounded-full h-2 mb-3">
                                <div class="bg-white h-2 rounded-full transition-all duration-300" style="width: {{ $testProgressPercentage }}%"></div>
                            </div>
                            
                            <p class="text-sm mb-3">{{ $testMotivationalMessage }}</p>
                            <a href="{{ route('filament.student.pages.tests') }}" class="mt-3 sm:mt-4 bg-white text-[#14b8a6] px-3 sm:px-4 py-2 rounded inline-block text-sm sm:text-base font-medium hover:bg-gray-50 transition-colors duration-200">View Tests</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enhanced Footer -->
        <div class="bg-gradient-to-r from-gray-200 to-gray-300 py-1 w-full"></div>
        <footer class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-900 py-6 sm:py-8 w-full mt-auto">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-0">
                    <div class="text-center sm:text-left">
                        <p class="text-lg sm:text-xl font-semibold text-[#14b8a6]">EdGuide</p>
                        <p class="text-sm text-[#14b8a6] mt-1">Empowering learning</p>
                    </div>
                    <div class="text-center sm:text-right">
                        <p class="text-sm text-[#14b8a6]">© 2025 All rights reserved</p>
                        <p class="text-xs text-[#14b8a6] mt-1">Terms & Privacy Policy</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
            } else {
                menu.classList.add('hidden');
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
            }
        }
        
        function closeMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            menu.classList.add('hidden');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
        }

        function setActiveTab(tab) {
            const navLinks = document.querySelectorAll('[onclick*="setActiveTab"]');
            navLinks.forEach(link => {
                link.classList.remove('border-cyan-500', 'text-cyan-600', 'bg-cyan-50');
                link.classList.add('border-transparent', 'text-gray-500');
            });
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-cyan-500', 'text-cyan-600');
            if (event.target.classList.contains('block')) {
                event.target.classList.remove('text-gray-700', 'hover:bg-gray-50', 'hover:text-gray-900');
                event.target.classList.add('bg-cyan-50', 'text-cyan-600');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('userMenuButton');
            const dropdown = document.getElementById('userMenuDropdown');
            if (btn && dropdown) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', function(e) {
                    if (!btn.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    
    <!-- Security Watermark -->
    @include('components.student-watermark')
    
    </div>
</x-filament-panels::page>