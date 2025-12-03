@php
    $user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Doubt Clearance - EdGuide</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/idle-detection.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .fi-header { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Custom gradient backgrounds */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Enhanced animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Pulse animation for badges */
        .pulse-badge {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body class="bg-gray-50 student-page">

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 flex flex-col">
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
                    {{-- <a href="{{ route('filament.student.pages.tests') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.tests') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Test</a> --}}
                    <a href="{{ route('filament.student.pages.study-materials') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Study Materials</a>
                    <a href="{{ route('filament.student.pages.profile') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Profile</a>
                    {{-- <a href="{{ route('filament.student.pages.opinion-verification') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.opinion-verification') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Opinion Verification</a> --}}
                    <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.speaking-sessions') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Live Classes</a>
                    <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Doubt Clearance</a>
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
                <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Doubt Clearance</a>
                
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

    <!-- Enhanced Main Content -->
    <main class="flex-1 w-full">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8 sm:px-6 lg:px-8">
            <!-- Enhanced Header Section -->
            <div class="mb-8 sm:mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                        Doubt Clearance
                    </h1>
                    <p class="text-gray-600 text-base sm:text-lg" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Ask your questions and get answers from our team</p>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ isset($doubts) ? $doubts->count() : '0' }} doubt{{ (isset($doubts) ? $doubts->count() : 0) !== 1 ? 's' : '' }} submitted</span>
                </div>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Chat Messages -->
            <div class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50" id="chat-messages">
                @forelse($doubts as $doubt)
                    <!-- Student Message (Right side) -->
                    <div class="flex justify-end">
                        <div class="flex items-end space-x-2 max-w-xs lg:max-w-md">
                            <div class="bg-blue-500 text-white rounded-lg px-4 py-2 shadow">
                                <p class="text-sm">{{ $doubt->message }}</p>
                                <p class="text-xs opacity-75 mt-1">{{ \Carbon\Carbon::parse($doubt->created_at)->format('M j, Y g:i A') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Reply (Left side) -->
                    @if($doubt->reply)
                        <div class="flex justify-start">
                            <div class="flex items-end space-x-2 max-w-xs lg:max-w-md">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="bg-green-500 text-white rounded-lg px-4 py-2 shadow">
                                    <!-- Reply Preview (WhatsApp style) -->
                                    <div class="bg-green-600 bg-opacity-50 rounded px-2 py-1 mb-2 border-l-2 border-green-300 border-t border-green-400 shadow-sm">
                                        <div class="flex items-center space-x-1 mb-1">
                                            <svg class="w-3 h-3 text-green-200" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-xs text-green-200 font-medium">You</span>
                                        </div>
                                        <p class="text-xs text-green-100 italic">{{ \Illuminate\Support\Str::limit($doubt->message, 50) }}</p>
                                    </div>
                                    
                                    <!-- Admin Reply -->
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-xs font-semibold">Admin</span>
                                    </div>
                                    <p class="text-sm">{{ $doubt->reply }}</p>
                                    <p class="text-xs opacity-75 mt-1">{{ $doubt->replied_at ? \Carbon\Carbon::parse($doubt->replied_at)->format('M j, Y g:i A') : '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No messages yet</h3>
                        <p class="text-gray-500">Start a conversation by asking your first question below.</p>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="border-t bg-white p-4">
                <form method="POST" action="{{ route('student.doubt.submit') }}" class="flex space-x-4">
                    @csrf
                    <div class="flex-1">
                        <textarea 
                            name="message" 
                            rows="2" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                            placeholder="Type your question here..."
                            required
                        ></textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button 
                        type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2 self-end"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Send</span>
                    </button>
                </form>
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
        // Enhanced user menu functionality
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

        // Auto-scroll to bottom of chat when new messages are added
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>
</body>
</html>