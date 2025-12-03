<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Study Materials - EdGuide</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/idle-detection.js') }}"></script>
    <style>
        .fi-header { display: none !important; }
        body { background-color: white !important; }
        html { background-color: white !important; }
        .bg-black, .bg-gray-900, .bg-gray-800, .bg-gray-700 { background-color: white !important; }
        .text-white { color: white !important; }
        .text-gray-900 { color: #1f2937 !important; }
        .text-gray-600 { color: #4b5563 !important; }
    </style>
</head>
<body class="student-page">
    <div class="min-h-screen bg-white flex flex-col">
        <!-- Navigation Header -->
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
                </div>
                
                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex justify-center border-t border-gray-200 py-3">
                    <div class="flex space-x-8">
                        <a href="{{ route('filament.student.pages.profile') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Profile</a>
                        <a href="{{ route('filament.student.pages.courses') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Courses</a>
                        <a href="{{ route('filament.student.pages.study-materials') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Study Materials</a>
                        <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.speaking-sessions') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Live Classes</a>
                        <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Doubt Clearance</a>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="flex-1 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Study Materials</h1>
                    <p class="mt-2 text-gray-600">Access your course materials, notes, and videos</p>
                </div>
                
                <!-- Study Materials Content -->
                <div class="space-y-8">
                    @if(!empty($studyMaterials))
                        @php
                            // Collect all notes and videos from all days
                            $allNotes = [];
                            $allVideos = [];
                            
                            foreach($studyMaterials as $dayData) {
                                if(isset($dayData['day'])) {
                                    // Legacy format
                                    foreach($dayData['materials'] as $material) {
                                        if($material['type'] === 'note') {
                                            $allNotes[] = $material;
                                        } else {
                                            $allVideos[] = $material;
                                        }
                                    }
                                } else {
                                    // New format
                                    if(isset($dayData['materials']['notes'])) {
                                        foreach($dayData['materials']['notes'] as $note) {
                                            $allNotes[] = $note;
                                        }
                                    }
                                    if(isset($dayData['materials']['videos'])) {
                                        foreach($dayData['materials']['videos'] as $video) {
                                            $allVideos[] = $video;
                                        }
                                    }
                                }
                            }
                        @endphp
                        
                        <!-- Notes Section -->
                        @if(!empty($allNotes))
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">Study Notes</h2>
                                        <p class="text-gray-600">All your PDF study materials</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @foreach($allNotes as $note)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-center space-x-3 mb-3">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="text-sm font-medium text-gray-900 truncate">
                                                        @if(is_array($note))
                                                            {{ $note['title'] }}
                                                        @else
                                                            {{ $note->title }}
                                                        @endif
                                                    </h3>
                                                    <p class="text-sm text-gray-500">PDF Note</p>
                                                    @if(is_object($note) && $note->course)
                                                        <p class="text-xs text-gray-400">{{ $note->course->name }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ asset('storage/' . (is_array($note) ? $note['file_path'] : $note->pdf_path)) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition-colors duration-200">
                                                    View PDF
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Videos Section -->
                        @if(!empty($allVideos))
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center mb-6">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">Video Lessons</h2>
                                        <p class="text-gray-600">All your video lessons and tutorials</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @foreach($allVideos as $video)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-center space-x-3 mb-3">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="text-sm font-medium text-gray-900 truncate">
                                                        @if(is_array($video))
                                                            {{ $video['title'] }}
                                                        @else
                                                            {{ $video->title }}
                                                        @endif
                                                    </h3>
                                                    <p class="text-sm text-gray-500">Video</p>
                                                    @if(is_object($video) && $video->course)
                                                        <p class="text-xs text-gray-400">{{ $video->course->name }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                @if(is_array($video))
                                                    <a href="{{ asset('storage/' . $video['file_path']) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition-colors duration-200">
                                                        View Video
                                                    </a>
                                                @else
                                                    @if($video->video_path)
                                                        <a href="{{ asset('storage/' . $video->video_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition-colors duration-200">
                                                            View Video
                                                        </a>
                                                    @elseif($video->youtube_url)
                                                        <a href="{{ $video->youtube_url }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-500 hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400 transition-colors duration-200">
                                                            Watch on YouTube
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No study materials available</h3>
                            <p class="mt-1 text-sm text-gray-500">Check back later for new materials.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
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
        // User menu toggle
        document.getElementById('userMenuButton').addEventListener('click', function() {
            const userMenu = document.getElementById('userMenuDropdown');
            userMenu.classList.toggle('hidden');
        });
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenuDropdown');
            const userMenuButton = document.getElementById('userMenuButton');
            
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
