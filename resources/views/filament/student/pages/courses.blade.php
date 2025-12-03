@php
    $user = auth()->user();
    $firstUnansweredFound = false;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - EdGuide</title>
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
        
        /* Line clamp utility for text truncation */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Additional mobile optimizations */
        @media (max-width: 640px) {
            .mobile-text-base { font-size: 0.875rem; }
            .mobile-text-sm { font-size: 0.75rem; }
        }
    </style>
</head>
<body class="bg-white">
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
                    <div class="flex space-x-4 lg:space-x-8 overflow-x-auto scrollbar-hide">
                        <a href="{{ route('filament.student.pages.profile') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Profile</a>
                        <a href="{{ route('filament.student.pages.courses') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Courses</a>
                        <a href="{{ route('filament.student.pages.study-materials') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Study Materials</a>
                        <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.speaking-sessions') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Live Classes</a>
                        <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Doubt Clearance</a>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
                <div class="px-4 py-3 space-y-2">
                    <a href="{{ route('filament.student.pages.profile') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Profile</a>
                    <a href="{{ route('filament.student.pages.courses') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Courses</a>
                    <a href="{{ route('filament.student.pages.study-materials') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Study Materials</a>
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
            <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8 sm:px-6 lg:px-8">
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Access Denied!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                
                <!-- Page Header -->
                <div class="mb-8 sm:mb-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-4 sm:mb-0">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                            📚 Available Courses
                        </h1>
                        <p class="text-gray-600 text-base sm:text-lg" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                            Explore and access your assigned courses
                        </p>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>{{ count($assignedCourses) }} course{{ count($assignedCourses) !== 1 ? 's' : '' }} assigned</span>
                    </div>
                </div>
            </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 md:gap-8 mb-6 sm:mb-8 md:mb-12">
            @forelse($assignedCourses as $course)
                <div class="bg-[#F5F5F5] rounded-lg shadow-sm overflow-hidden transform transition-transform duration-200 hover:scale-[1.02] block relative">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 p-3 sm:p-4 text-gray-900 flex justify-between items-start sm:items-center">
                        <span class="text-sm sm:text-base font-medium flex-1 mr-2">{{ $course->name }}</span>
                        <span class="inline-block bg-green-500 text-gray-900 text-xs px-2 py-1 rounded-full whitespace-nowrap">Available</span>
                    </div>
                    <div class="p-3 sm:p-4">
                        <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 leading-relaxed">{{ $course->description ?? 'Course curriculum and daily structure' }}</p>
                        <div class="flex items-center justify-between text-xs sm:text-sm text-gray-500">
                            <span>Interactive Learning</span>
                            <span class="text-lg">📚</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-[#F5F5F5] rounded-lg shadow-sm p-8 sm:p-12 text-center">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-600 mb-2">No Courses Available</h3>
                        <p class="text-sm sm:text-base text-gray-500">No courses have been created yet. Check back later!</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Course Structure Explorer -->
        @if($assignedCourses->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 text-cyan-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Course Structure
                </h2>
                <p class="text-gray-600 mt-1">Explore the detailed structure of each course</p>
            </div>
            
            <div class="p-6">
                @forelse($assignedCourses as $course)
                    <div x-data="{ open: false }" class="mb-4 last:mb-0">
                        <!-- Course Header -->
                        <div class="bg-gradient-to-r from-cyan-50 to-blue-50 rounded-lg border border-cyan-200 hover:border-cyan-300 transition-colors duration-200 relative">
                            <button @click="open = !open" class="w-full px-4 sm:px-6 py-3 sm:py-4 text-left focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-cyan-500 rounded-lg flex items-center justify-center mr-3 sm:mr-4">
                                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-base sm:text-lg font-bold text-gray-800 truncate">{{ $course->name }}</h3>
                                            <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2">{{ $course->description ?? 'Course curriculum and daily structure' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 flex-shrink-0 ml-3">
                                        <span class="hidden sm:inline text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Available</span>
                                        <div class="flex-shrink-0">
                                            <svg class="w-5 h-5 text-cyan-600 transform transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </div>

                        <!-- Course Content: Subjects -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="mt-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                            @foreach($subjects as $subject)
                                @php
                                    // Check if this subject has any questions for any day in this course
                                    $subjectHasQuestions = false;
                                    foreach(\App\Models\Day::where('course_id', $course->id)->get() as $checkDay) {
                                        if(in_array($checkDay->id, $assignedDayIds)) {
                                            $checkDayQuestions = $questions->get($course->id . '-' . $subject->id . '-' . $checkDay->id, collect());
                                            if($checkDayQuestions->count() > 0) {
                                                $subjectHasQuestions = true;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                @if($subjectHasQuestions)
                                    <div x-data="{ subjectOpen: false }" class="border-b border-gray-100 last:border-b-0">
                                        <!-- Subject Header -->
                                        <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                            <button @click="subjectOpen = !subjectOpen" class="w-full text-left focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-opacity-50 rounded">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center flex-1 min-w-0">
                                                        <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <h4 class="text-sm sm:text-base font-semibold text-gray-800 truncate">{{ $subject->name }}</h4>
                                                            <p class="text-xs sm:text-sm text-gray-600 mt-1">Practice exercises and activities</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2 flex-shrink-0 ml-3">
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 transform transition-transform duration-200" :class="subjectOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </button>
                                        </div>

                                        <!-- Subject Content: Days -->
                                    <div x-show="subjectOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-screen" x-transition:leave-end="opacity-0 max-h-0" class="bg-white">
                                        @php
                                            $courseDays = \App\Models\Day::where('course_id', $course->id)->get();
                                        @endphp
                                        @if($courseDays->count() > 0)
                                            <div class="p-4 space-y-3">
                                                @foreach($courseDays as $day)
                                                    @php
                                                        $isDayAssigned = in_array($day->id, $assignedDayIds);
                                                        // Check if this day has any questions for this subject
                                                        $dayQuestions = $questions->get($course->id . '-' . $subject->id . '-' . $day->id, collect());
                                                        $totalQuestions = $dayQuestions->count();
                                                    @endphp
                                                    @if($isDayAssigned && $totalQuestions > 0)
                                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200 hover:border-green-300 transition-colors duration-200 space-y-3 sm:space-y-0">
                                                            <div class="flex items-center flex-1 min-w-0">
                                                                <div class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                </div>
                                                                <div class="min-w-0 flex-1">
                                                                    <h5 class="text-sm sm:text-base font-semibold text-gray-800 truncate">{{ $day->title }}</h5>
                                                                    <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2">{{ $day->description ?? 'Daily exercises and practice' }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center justify-between sm:justify-end space-x-2 sm:space-x-3 flex-shrink-0">
                                                                @php
                                                                    $answeredCount = $dayQuestions->filter(function($q) use ($answeredQuestionIds) {
                                                                        return in_array($q->id, $answeredQuestionIds);
                                                                    })->count();
                                                                @endphp
                                                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                                                    <span class="hidden sm:inline bg-green-100 text-green-800 px-2 py-1 rounded-full">Available</span>
                                                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full">{{ $answeredCount }}/{{ $totalQuestions }}</span>
                                                                </div>
                                                                <a href="{{ route('filament.student.pages.questions', ['course' => $course->id, 'subject' => $subject->id, 'day' => $day->id]) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-xs sm:text-sm leading-4 font-medium rounded-md text-gray-900 bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors duration-200 whitespace-nowrap">
                                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m2 2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h8z"></path>
                                                                    </svg>
                                                                    Start
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <p class="text-gray-500 text-sm">No days/questions for this subject yet.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Courses Available</h3>
                        <p class="text-gray-500">Course structure will appear here once courses are created</p>
                    </div>
                @endforelse
            </div>
        </div>
        @endif
            </div>
        </main>

        <!-- Enhanced Footer -->
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 py-1 w-full"></div>
        <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-gray-900 py-6 sm:py-8 w-full mt-auto">
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

    <!-- Security Watermark -->
    @include('components.student-watermark')

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

</body>
</html>