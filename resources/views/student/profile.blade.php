<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Profile - EdGuide</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/idle-detection.js') }}"></script>
    <style>
        .fi-header { display: none !important; }
        body { background-color: white !important; }
        html { background-color: white !important; }
        .bg-black, .bg-gray-900, .bg-gray-800, .bg-gray-700 { background-color: white !important; }
        .text-white { color: #14b8a6 !important; }
        .text-gray-900 { color: #1f2937 !important; }
        .text-gray-600 { color: #4b5563 !important; }
    </style>
</head>
<body class="student-page">
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
                                <form method="POST" action="{{ route('student.logout') }}">
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
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
                <div class="px-4 py-3 space-y-2">
                    <a href="{{ route('filament.student.pages.profile') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.profile') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Profile</a>
                    <a href="{{ route('filament.student.pages.courses') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.courses') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Courses</a>
                    <a href="{{ route('filament.student.pages.study-materials') }}" class="block px-4 py-3 text-base font-medium {{ request()->routeIs('filament.student.pages.study-materials') ? 'text-cyan-600 bg-cyan-50 border-l-4 border-cyan-500' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }} rounded-lg transition-all duration-200" onclick="closeMobileMenu()">Study Materials</a>
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
                    <form method="POST" action="{{ route('student.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-base font-medium text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200">Logout</button>
                    </form>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="flex-1 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Profile Header -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full border-4 border-gray-200" />
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            <p class="text-sm text-gray-500 mt-1">Student at EdGuide</p>
                        </div>
                        <div class="flex-shrink-0">
                            <button class="bg-[#14b8a6] text-white px-4 py-2 rounded-lg hover:bg-[#0f9488] transition-colors duration-200" style="color: white !important;">
                                Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Profile Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('M d, Y') : 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Course Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Current Course</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->course ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Batch</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($batchInfo && isset($batchInfo['name']))
                                        {{ $batchInfo['name'] }}
                                    @else
                                        Not assigned
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Enrollment Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'Not available' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->address ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Emergency Contact</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->emergency_contact ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Login</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('M d, Y H:i') : 'Never' }}</p>
                        </div>
                    </div>
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
        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        }
        
        function closeMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.add('hidden');
        }
        
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
