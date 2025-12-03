<x-filament-panels::page>
    <style>
        .fi-header { display: none !important; }
        body { background-color: white !important; }
        html { background-color: white !important; }
        .bg-black, .bg-gray-900, .bg-gray-800, .bg-gray-700 { background-color: white !important; }
        .text-white { color: #14b8a6 !important; }
        .text-gray-900 { color: #1f2937 !important; }
        .text-gray-600 { color: #4b5563 !important; }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 24px 0;
        }
        .material-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .material-tag.pdf {
            background-color: #fce7f3;
            color: #be185d;
        }
        .material-tag.video {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .main-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .main-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .main-card.active {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .content-section {
            display: none;
        }
        .content-section.active {
            display: block;
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @php
        // Data is now passed from the controller
        // $user and $studyMaterials are available from the controller
        
        // Calculate counts for display
        $pdfCount = 0;
        $videoCount = 0;
        
        if (!empty($studyMaterials)) {
            foreach ($studyMaterials as $dayData) {
                $dayMaterials = $dayData['materials'];
                if (!empty($dayMaterials['notes'])) {
                    $pdfCount += count($dayMaterials['notes']);
                }
                if (!empty($dayMaterials['videos'])) {
                    $videoCount += count($dayMaterials['videos']);
                }
            }
        }
    @endphp

    <div class="bg-white">
    <div class="min-h-screen bg-white flex flex-col">
        <!-- Enhanced Navigation Header -->
        <nav class="bg-white shadow-sm sticky top-0 z-50 w-full border-b border-gray-200">
            <div class="w-full max-w-[120rem] mx-auto px-4 sm:px-6 lg:px-8">
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
                        {{-- <a href="{{ route('filament.student.pages.dashboard') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.dashboard') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Dashboard</a> --}}
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
                    {{-- <a href="{{ route('filament.student.pages.dashboard') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.dashboard') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Dashboard</a> --}}
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
                        <a href="{{ route('filament.student.pages.profile') }}" class="block px-4 py-3 text-base font-medium text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-base font-medium text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="w-full px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Study Materials</h1>
                            <p class="text-gray-600 mt-1">Access notes and videos for your assigned courses</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>{{ count($studyMaterials) }} days with materials</span>
                    </div>
                </div>
            </div>

            @if(!empty($studyMaterials))
                <!-- Main Content Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- PDF Card -->
                    <div id="pdf-card" class="main-card bg-gradient-to-br from-pink-50 to-rose-50 rounded-2xl p-6 border-2 border-pink-200 hover:border-pink-300" onclick="showContent('pdf')">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-rose-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-pink-600" id="pdf-count">{{ $pdfCount }}</div>
                                <div class="text-sm text-pink-500">PDF Notes</div>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Study Notes</h3>
                        <p class="text-gray-600 mb-4">Access all your PDF study materials organized by day</p>
                        <div class="flex items-center text-pink-600 font-medium">
                            <span>View Notes</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Video Card -->
                    <div id="video-card" class="main-card bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 border-2 border-blue-200 hover:border-blue-300" onclick="showContent('video')">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600" id="video-count">{{ $videoCount }}</div>
                                <div class="text-sm text-blue-500">Videos</div>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Video Lessons</h3>
                        <p class="text-gray-600 mb-4">Watch instructional videos and tutorials</p>
                        <div class="flex items-center text-blue-600 font-medium">
                            <span>View Videos</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Content Sections -->
                <div class="space-y-8">
                    <!-- PDF Content Section -->
                    <div id="pdf-content" class="content-section">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Study Notes</h2>
                                <p class="text-gray-600">All your PDF study materials organized by day</p>
                            </div>
                        </div>

                        @foreach($studyMaterials as $dayData)
                            @php
                                $dayNumber = $dayData['day_number'];
                                $dayMaterials = $dayData['materials'];
                            @endphp
                            @if(!empty($dayMaterials['notes']))
                                <div class="mb-8 animate-fade-in-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                                    <div class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl p-4 mb-4">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-900 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Day {{ $dayNumber }}</h3>
                                                <p class="text-pink-100 text-sm">Study materials for Day {{ $dayNumber }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                                        @foreach($dayMaterials['notes'] as $note)
                                            <div class="group relative bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl p-4 border border-pink-200 hover:border-pink-300 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 overflow-hidden">
                                                <div class="absolute inset-0 opacity-5">
                                                    <div class="absolute inset-0 bg-gradient-to-br from-pink-400 via-rose-400 to-pink-600"></div>
                                                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%23ffffff" fill-opacity="0.1" fill-rule="evenodd"%3E%3Ccircle cx="3" cy="3" r="3"/%3E%3Ccircle cx="13" cy="13" r="3"/%3E%3C/g%3E%3C/svg%3E')"></div>
                                                </div>
                                                
                                                <div class="relative z-10 flex flex-col items-center text-center">
                                                    <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl flex items-center justify-center mb-3 shadow-lg">
                                                        <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="material-tag pdf mb-2">📄 PDF</span>
                                                    <div class="text-xs text-pink-600 font-medium mb-1">{{ $note->course->name ?? 'N/A' }} - {{ $note->subject->name ?? 'N/A' }}</div>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <h6 class="font-semibold text-gray-900 text-xs group-hover:text-pink-600 transition-colors duration-200 leading-tight line-clamp-2 flex-1">{{ $note->title }}</h6>
                                                        @if($note->topic)
                                                            <span class="text-xs text-pink-500 font-medium bg-pink-100 px-2 py-1 rounded-full whitespace-nowrap flex-shrink-0">📚 {{ $note->topic }}</span>
                                                        @endif
                                                    </div>
                                                    @if($note->description)
                                                        <p class="text-gray-600 text-xs mb-3 line-clamp-2 leading-relaxed">{{ $note->description }}</p>
                                                    @endif
                                                    <button onclick="openSecurePreview('note', {{ $note->id }})" 
                                                       class="w-full bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-gray-900 rounded-lg py-2 px-3 text-xs font-medium transition-all duration-200 group-hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Preview
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Video Content Section -->
                    <div id="video-content" class="content-section">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Video Lessons</h2>
                                <p class="text-gray-600">All your video lessons organized by day</p>
                            </div>
                        </div>

                        @foreach($studyMaterials as $dayData)
                            @php
                                $dayNumber = $dayData['day_number'];
                                $dayMaterials = $dayData['materials'];
                            @endphp
                            @if(!empty($dayMaterials['videos']))
                                <div class="mb-8 animate-fade-in-up" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl p-4 mb-4">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-900 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">Day {{ $dayNumber }}</h3>
                                                <p class="text-blue-100 text-sm">Video lessons for Day {{ $dayNumber }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                                        @foreach($dayMaterials['videos'] as $video)
                                            <div class="group relative bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-4 border border-blue-200 hover:border-blue-300 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 overflow-hidden">
                                                <div class="absolute inset-0 opacity-5">
                                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400 via-cyan-400 to-blue-600"></div>
                                                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%23ffffff" fill-opacity="0.1" fill-rule="evenodd"%3E%3Cpolygon points="5,5 10,0 15,5 10,10"/%3E%3Cpolygon points="5,15 10,10 15,15 10,20"/%3E%3C/g%3E%3C/svg%3E')"></div>
                                                </div>
                                                
                                                <div class="relative z-10 flex flex-col items-center text-center">
                                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mb-3 shadow-lg">
                                                        <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v4a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="material-tag video mb-2">🎥 VIDEO</span>
                                                    <div class="text-xs text-blue-600 font-medium mb-1">{{ $video->course->name ?? 'N/A' }} - {{ $video->subject->name ?? 'N/A' }}</div>
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <h6 class="font-semibold text-gray-900 text-xs group-hover:text-blue-600 transition-colors duration-200 leading-tight line-clamp-2 flex-1">{{ $video->title }}</h6>
                                                        @if($video->topic)
                                                            <span class="text-xs text-blue-500 font-medium bg-blue-100 px-2 py-1 rounded-full whitespace-nowrap flex-shrink-0">🎯 {{ $video->topic }}</span>
                                                        @endif
                                                    </div>
                                                    @if($video->description)
                                                        <p class="text-gray-600 text-xs mb-3 line-clamp-2 leading-relaxed">{{ $video->description }}</p>
                                                    @endif
                                                    <button onclick="openSecurePreview('video', {{ $video->id }})" 
                                                       class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-gray-900 rounded-lg py-2 px-3 text-xs font-medium transition-all duration-200 group-hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Preview
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 sm:py-20">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2">No Study Materials Available</h3>
                        <p class="text-gray-600 mb-6">Study materials will appear here once they are added to your assigned courses.</p>
                        <a href="{{ route('filament.student.pages.courses') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-gray-900 font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            View Courses
                        </a>
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

        <!-- Security Watermark -->
        <div class="watermark-wrapper">
            @include('components.student-watermark')
        </div>

    <script>
        // Get counts from PHP variables
        let pdfCount = {{ $pdfCount }};
        let videoCount = {{ $videoCount }};

        function showContent(type) {
            // Remove active class from all cards
            document.getElementById('pdf-card').classList.remove('active');
            document.getElementById('video-card').classList.remove('active');
            
            // Hide all content sections
            document.getElementById('pdf-content').classList.remove('active');
            document.getElementById('video-content').classList.remove('active');
            
            // Add active class to selected card
            document.getElementById(type + '-card').classList.add('active');
            
            // Show selected content
            document.getElementById(type + '-content').classList.add('active');
        }

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

        // User menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('userMenuButton');
            const dropdown = document.getElementById('userMenuDropdown');
            
            if (btn && dropdown) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                
                document.addEventListener('click', function(e) {
                    if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
        });

        function openSecurePreview(type, id) {
            // Open secure preview in a new window with specific features
            const previewWindow = window.open(
                `/secure-modal/${type}/${id}`,
                'securePreview',
                'width=1200,height=800,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,directories=no,status=no'
            );
            
            if (previewWindow) {
                previewWindow.focus();
            } else {
                alert('Please allow popups for this site to view study materials securely.');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Show PDF content by default if available
            if (pdfCount > 0) {
                showContent('pdf');
            } else if (videoCount > 0) {
                showContent('video');
            }
        });
    </script>
</x-filament-panels::page> 