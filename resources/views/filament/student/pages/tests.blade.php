@php
    $user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tests - EdGuide</title>
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
                    <div class="flex space-x-4 lg:space-x-8 overflow-x-auto scrollbar-hide">
                        <a href="{{ route('filament.student.pages.dashboard') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.dashboard') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Dashboard</a>
                        <a href="{{ route('filament.student.pages.courses') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Courses</a>
                        {{-- <a href="{{ route('filament.student.pages.tests') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.tests') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Test</a> --}}
                        <a href="{{ route('filament.student.pages.study-materials') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Study Materials</a>
                        <a href="{{ route('filament.student.pages.profile') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Profile</a>
                        {{-- <a href="{{ route('filament.student.pages.opinion-verification') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.opinion-verification') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Opinion Verification</a> --}}
                        <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.speaking-sessions') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Live Classes</a>
                        {{-- <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="text-sm lg:text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-2 lg:px-3 py-2 transition-colors duration-200 whitespace-nowrap">Doubt Clearance</a> --}}
                    </div>
                </div>
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
            <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8 sm:px-6 lg:px-8">
                <h1 class="text-2xl sm:text-3xl font-bold mb-6">📝 Active Tests</h1>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Loop through tests --}}
                    @forelse($tests ?? [] as $test)
                        @php $stats = $testStats[$test->id] ?? null; @endphp
                        <div class="bg-white rounded-2xl shadow-lg p-7 flex flex-col justify-between border-l-8 border-cyan-400 hover:shadow-2xl transition-shadow duration-200 group relative overflow-hidden">
                            <div>
                                @if($test->subject)
                                    <span class="inline-block mb-2 px-3 py-1 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-800 tracking-wide shadow-sm">{{ $test->subject->name }}</span>
                                @endif
                                <h2 class="text-2xl font-bold mb-1 font-sans group-hover:text-cyan-700 transition-colors duration-200">{{ $test->name }}</h2>
                                <p class="text-gray-600 mb-4 line-clamp-2">{{ $test->description }}</p>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-{{ $test->is_active ? 'green' : 'gray' }}-100 text-{{ $test->is_active ? 'green' : 'gray' }}-800 mb-2">
                                    {{ $test->is_active ? 'Active' : 'Completed' }}
                                </span>
                                @if($stats)
                                    <div class="mt-4 space-y-1">
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="font-medium text-gray-700">Answered:</span>
                                            <span>{{ $stats['answered'] }}/{{ $stats['total_questions'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="font-medium text-gray-700">Status:</span>
                                            <span class="{{ $stats['status'] === 'Completed' ? 'text-green-600' : ($stats['status'] === 'In Progress' ? 'text-yellow-600' : 'text-gray-500') }} font-semibold">{{ $stats['status'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="font-medium text-gray-700">Result:</span>
                                            @if($stats['result'] === 'Pass')
                                                <span class="text-green-700 font-semibold">Pass</span>
                                            @elseif($stats['result'] === 'Fail')
                                                <span class="text-red-600 font-semibold">Fail</span>
                                            @elseif($stats['result'] === 'Pending')
                                                <span class="text-yellow-600 font-semibold">Pending</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                        @if($stats['status'] === 'Completed')
                                            <div class="flex items-center gap-2 text-sm">
                                                <span class="font-medium text-gray-700">Score:</span>
                                                <span class="text-blue-700 font-semibold">{{ $stats['score'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('filament.student.pages.tests.show', $test) }}" class="mt-6 inline-block bg-cyan-600 text-white px-6 py-2.5 rounded-lg font-semibold shadow hover:bg-cyan-700 transition-all duration-200 text-base tracking-wide">
                                @if($stats && $stats['status'] === 'Completed')
                                    Continue
                                @else
                                    Start
                                @endif
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500 py-12">
                            <p>No active tests available at the moment.</p>
                        </div>
                    @endforelse
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