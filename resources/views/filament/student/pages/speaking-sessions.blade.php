@php
    $user = auth()->user();
    use App\Models\SpeakingSession;
    $sessions = SpeakingSession::orderByDesc('session_date')->orderByDesc('session_time')->get();
@endphp

<x-filament-panels::page>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .fi-header { display: none !important; }
        body { background-color: white !important; }
        html { background-color: white !important; }
        .bg-black, .bg-gray-900, .bg-gray-800, .bg-gray-700 { background-color: white !important; }
        .text-white { color: #14b8a6 !important; }
        .text-gray-900 { color: #1f2937 !important; }
        .text-gray-600 { color: #4b5563 !important; }
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
    
    <div class="bg-white">

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
                    {{-- <a href="{{ route('filament.student.pages.dashboard') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.dashboard') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Dashboard</a> --}}
                    <a href="{{ route('filament.student.pages.courses') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Courses</a>
                    {{-- <a href="{{ route('filament.student.pages.tests') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.tests') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Test</a> --}}
                    <a href="{{ route('filament.student.pages.study-materials') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Study Materials</a>
                    <a href="{{ route('filament.student.pages.profile') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Profile</a>
                    {{-- <a href="{{ route('filament.student.pages.opinion-verification') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.opinion-verification') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Opinion Verification</a> --}}
                    <a href="{{ route('filament.student.pages.speaking-sessions') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.speaking-sessions') ? 'text-[#14b8a6] border-b-2 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900' }} px-3 py-2 transition-colors duration-200">Live Classes</a>
                    {{-- <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-cyan-600 border-b-2 border-cyan-500' : 'text-gray-500 hover:text-gray-700' }} px-3 py-2 transition-colors duration-200">Doubt Clearance</a> --}}
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
                {{-- <a href="{{ route('filament.student.pages.doubt-clearance') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.doubt-clearance') ? 'text-[#14b8a6] bg-teal-50 border-l-4 border-[#14b8a6]' : 'text-[#14b8a6] hover:text-gray-900 hover:bg-gray-100' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Doubt Clearance</a> --}}
                
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
        <div class="w-full px-4 py-6 sm:py-8 sm:px-6 lg:px-8">
            <!-- Enhanced Header Section -->
            <div class="mb-8 sm:mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                        Live Classes
                    </h1>
                    <p class="text-gray-600 text-base sm:text-lg" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Join live conversations to practice your  skills</p>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $sessions->count() }} session{{ $sessions->count() !== 1 ? 's' : '' }} available</span>
                </div>
            </div>
        </div>

        <!-- Enhanced Sessions List -->
        <div class="space-y-4 sm:space-y-6">
            @forelse($sessions as $index => $session)
                <div class="card-hover bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                            <!-- Session Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                                    <!-- Date and Time -->
                                    <div class="flex-shrink-0">
                                        <div class="inline-flex items-center px-3 py-2 bg-blue-500 rounded-lg">
                                            <svg class="w-4 h-4 text-gray-900 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div class="text-sm font-medium text-gray-900" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                                <div>{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</div>
                                                <div class="text-xs text-blue-100 mt-0.5">{{ \Carbon\Carbon::createFromFormat('H:i:s', $session->session_time)->format('h:i A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Session Description -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base sm:text-lg font-medium text-gray-900 leading-tight" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                            {{ $session->description }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Button -->
                            <div class="flex-shrink-0">
                                <a href="{{ $session->gmeet_link }}" target="_blank" 
                                   class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-gray-900 font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 group" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Join Meeting
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Enhanced Empty State -->
                <div class="text-center py-16 sm:py-20">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">No Live Classes Available</h3>
                        <p class="text-gray-600 mb-6" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Check back soon for new speaking practice opportunities!</p>
                        <button class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-gray-900 font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh Page
                        </button>
                    </div>
                </div>
            @endforelse
            </div>
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
    <div class="watermark-wrapper">
        @include('components.student-watermark')
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

        // Add smooth scroll behavior for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading states for external links
        document.querySelectorAll('a[target="_blank"]').forEach(link => {
            link.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = originalText.replace('Join Meeting', 'Opening...');
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
        });
    });
</script>
    </div>
</x-filament-panels::page>