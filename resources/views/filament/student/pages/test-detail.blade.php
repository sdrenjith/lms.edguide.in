@php
    $user = auth()->user();
    $answeredQuestionIds = \App\Models\StudentAnswer::where('user_id', auth()->id())->pluck('question_id')->toArray();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test: {{ $test->name }} | {{ $user->name }} | EdGuide</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .fi-header { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        @media (max-width: 640px) {
            .mobile-text-base { font-size: 0.875rem; }
            .mobile-text-sm { font-size: 0.75rem; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 flex flex-col">
        <!-- Enhanced Navigation Header -->
        <nav class="bg-white shadow-sm sticky top-0 z-50 w-full border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <img src="{{ asset('/edguide-logo.png') }}" alt="Logo" class="h-10 sm:h-12 mr-4" style="width: 140px;" />
                    </div>
                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <!-- User Menu -->
                        <div class="relative group">
                            <button id="userMenuButton" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="{{ $user->name }}" class="h-8 w-8 rounded-full border-2 border-gray-200" />
                                <span class="text-sm font-medium">{{ $user->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                                <a href="{{ route('filament.student.pages.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button id="mobileMenuButton" class="text-gray-600 hover:text-gray-900 focus:outline-none" onclick="toggleMobileMenu()">
                            <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex justify-center border-t border-gray-100 py-3">
                    <div class="flex space-x-8">
                        <a href="{{ route('filament.student.pages.dashboard') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.dashboard') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Dashboard</a>
                        <a href="{{ route('filament.student.pages.courses') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Courses</a>
                        <a href="{{ route('filament.student.pages.tests') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.tests') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Test</a>
                        <a href="{{ route('filament.student.pages.study-materials') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Study Materials</a>
                        <a href="{{ route('filament.student.pages.profile') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Profile</a>
                        {{-- <a href="{{ route('filament.student.pages.opinion-verification') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.opinion-verification') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Opinion Verification</a> --}}
                        <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.speaking-sessions') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Live Classes</a>
                        {{-- <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Doubt Clearance</a> --}}
                    </div>
                </div>
                <style>
                .active-menu-link {
                    color: #0891b2 !important;
                    border-bottom: 2px solid #0891b2 !important;
                    font-weight: 700 !important;
                    text-decoration: underline !important;
                }
                </style>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
                <div class="px-4 py-3 space-y-2">
                    <a href="{{ route('filament.student.pages.dashboard') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.dashboard') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Dashboard</a>
                    <a href="{{ route('filament.student.pages.courses') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Courses</a>
                    {{-- <a href="{{ route('filament.student.pages.tests') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.tests') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Test</a> --}}
                    <a href="{{ route('filament.student.pages.study-materials') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Study Materials</a>
                    <a href="{{ route('filament.student.pages.profile') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Profile</a>
                    {{-- <a href="{{ route('filament.student.pages.opinion-verification') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.opinion-verification') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Opinion Verification</a> --}}
                    <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.speaking-sessions') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Live Classes</a>
                    {{-- <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Doubt Clearance</a> --}}
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
            <div class="max-w-6xl w-full mx-auto px-2 sm:px-8 py-6 sm:py-10">
                <h1 class="text-3xl font-normal mb-8 tracking-tight text-gray-900 text-center mx-auto">Test: {{ $test->name }}</h1>
                <!-- Test Summary Horizontal Blocks -->
                <div class="mx-auto max-w-4xl grid grid-cols-1 md:grid-cols-3 gap-x-12 gap-y-8 mb-12">
                    <!-- Score Card -->
                    <div class="bg-orange-50 rounded-2xl shadow-2xl p-8 flex flex-col items-center justify-center border-2 border-orange-100 h-32 w-full gap-y-4">
                        <span class="text-base text-orange-700 mb-2">Score</span>
                        <span class="font-bold text-4xl text-orange-600">{{ $earnedPoints }}/{{ $totalPoints }}</span>
                    </div>
                    <!-- Answered Card -->
                    <div class="bg-green-50 rounded-2xl shadow-2xl p-8 flex flex-col items-center justify-center border-2 border-green-100 h-32 w-full gap-y-4">
                        <span class="text-base text-green-700 mb-2">Answered</span>
                        <span class="font-bold text-4xl text-green-700">{{ $answeredCount }}/{{ $totalQuestions }}</span>
                    </div>
                    <!-- Result Card -->
                    <div class="bg-white rounded-2xl shadow-2xl p-8 flex flex-col items-center justify-center border-2 border-gray-100 h-32 w-full gap-y-4">
                        <span class="text-base text-gray-500 mb-2">Result</span>
                        @if($result === 'Pass')
                            <span class="inline-block px-5 py-2 rounded-full bg-green-100 text-green-700 text-xl font-semibold">Pass</span>
                        @elseif($result === 'Fail')
                            <span class="inline-block px-5 py-2 rounded-full bg-red-100 text-red-600 text-xl font-semibold">Fail</span>
                        @else
                            <span class="inline-block px-5 py-2 rounded-full bg-gray-100 text-gray-600 text-xl font-semibold">Pending</span>
                        @endif
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 sm:px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <svg class="w-6 h-6 text-cyan-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                            </svg>
                            Test Questions
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            @if($unansweredQuestions->count() > 0)
                                {{ $unansweredQuestions->count() }} unanswered question(s) remaining
                            @else
                                All questions answered
                            @endif
                        </p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($questions as $i => $question)
                            @php
                                $isAnswered = $studentAnswers->has($question->id);
                                $studentAnswer = $isAnswered ? $studentAnswers->get($question->id) : null;
                            @endphp
                            <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                                <div class="flex-shrink-0 flex flex-col items-center w-full sm:w-auto">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $isAnswered ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-400' }} mb-2">
                                        @if($isAnswered)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span class="font-bold">{{ $i+1 }}</span>
                                        @endif
                                    </span>
                                    <span class="font-semibold text-sm text-gray-700">Question {{ $i+1 }}</span>
                                </div>
                                <div class="flex-1 w-full">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="inline-block bg-blue-50 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{{ $question->questionType->name ?? '' }}</span>
                                        @if($question->points)
                                            <span class="inline-block bg-green-50 text-green-700 px-2 py-1 rounded-full text-xs font-medium">{{ $question->points }} point</span>
                                        @endif
                                        @if($isAnswered)
                                            @php
                                                $isOpinionQuestion = $question->questionType && $question->questionType->name === 'opinion';
                                            @endphp
                                            @if($isOpinionQuestion)
                                                @if($studentAnswer->verification_status === 'pending')
                                                    <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                                        Awaiting Teacher
                                                    </span>
                                                @elseif($studentAnswer->verification_status === 'verified_correct')
                                                    <span class="inline-block bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                                        Correct
                                                    </span>
                                                @elseif($studentAnswer->verification_status === 'verified_incorrect')
                                                    <span class="inline-block bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">
                                                        Needs Revision
                                                    </span>
                                                @else
                                                    <span class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium">
                                                        Submitted
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-block bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                                    {{ $studentAnswer && $studentAnswer->is_correct ? 'Correct' : 'Incorrect' }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-gray-800 mb-2">{{ $question->instruction }}</div>
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <a href="{{ route('filament.student.pages.tests.question', [$test, $question]) }}" class="inline-flex items-center px-4 py-2 border {{ $isAnswered ? 'border-cyan-600 text-cyan-700 bg-white hover:bg-cyan-50' : 'border-blue-600 text-blue-700 bg-white hover:bg-blue-50' }} font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition w-full sm:w-auto text-center justify-center">
                                            {{ $isAnswered ? 'View Submitted Answer' : 'Start' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </main>
        <!-- Enhanced Footer -->
        <div class="bg-gradient-to-r from-gray-100 to-gray-200 py-1 w-full"></div>
        <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-6 sm:py-8 w-full mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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