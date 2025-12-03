@php
    $user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - EdGuide</title>
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
                <div class="flex flex-col md:flex-row gap-8">
            <!-- Profile Card FIRST for mobile -->
            <div class="w-full md:w-80 bg-[#f7f7f7] rounded-xl flex flex-col items-center py-8 px-4 shadow-sm relative text-black mb-8 md:mb-0">
                <span class="absolute -top-4 left-1/2 -translate-x-1/2 bg-yellow-400 text-xs font-semibold px-4 py-1 rounded-full border-2 border-white shadow text-black">Profile</span>
                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://via.placeholder.com/120' }}" alt="Profile" class="rounded-full border-4 border-yellow-400 w-28 h-28 object-cover mb-3 mt-4" />
                <div class="text-lg font-bold text-black mt-2">{{ $user->name }}</div>
                <div class="flex items-center text-gray-700 mb-1 mt-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>{{ $user->phone ?? '+50 123 456 7890' }}</span>
                </div>
                <div class="flex items-center text-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ $user->email }}</span>
                </div>
            </div>
            <!-- Details -->
            <div class="flex-1 text-black bg-white rounded-xl shadow-md p-8">
                <h2 class="text-2xl font-bold mb-6 text-black">Personal Information</h2>
                <div class="mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="font-semibold">Full Name</div>
                            <div class="mt-1">{{ $user->name }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Father's Name</div>
                            <div class="mt-1">{{ $user->father_name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Mother's Name</div>
                            <div class="mt-1">{{ $user->mother_name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Date of Birth</div>
                            <div class="mt-1">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('M d, Y') : 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Age</div>
                            <div class="mt-1">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->age : 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Phone</div>
                            <div class="mt-1">{{ $user->phone ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Email</div>
                            <div class="mt-1">{{ $user->email }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Gender</div>
                            <div class="mt-1">{{ ucfirst($user->gender ?? 'N/A') }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Nationality</div>
                            <div class="mt-1">{{ $user->nationality ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Category</div>
                            <div class="mt-1">{{ ucfirst($user->category ?? 'N/A') }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Qualification</div>
                            <div class="mt-1">{{ ucwords(str_replace('_', ' ', $user->qualification ?? 'N/A')) }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Experience</div>
                            <div class="mt-1">{{ $user->experience_months ? $user->experience_months . ' months' : 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Passport Number</div>
                            <div class="mt-1">{{ $user->passport_number ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Father's WhatsApp</div>
                            <div class="mt-1">{{ $user->father_whatsapp ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="font-semibold">Mother's WhatsApp</div>
                            <div class="mt-1">{{ $user->mother_whatsapp ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div class="mb-6 mt-10">
                    <h2 class="text-2xl font-bold text-black mb-4">Address Information</h2>
                    <div class="p-6">
                        <div class="font-semibold mb-2">Full Address</div>
                        <div class="text-gray-700">{{ $user->address ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="mb-6 mt-10">
                    <h2 class="text-2xl font-bold text-black mb-4">Educational & Financial Details</h2>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-16 gap-y-4">
                            @if($user->verificationCode)
                                <div>Course: <span class="font-bold text-black">{{ $user->verificationCode->course->name }}</span></div>
                                <div>Subject: <span class="font-bold text-black">{{ $user->verificationCode->subject->name }}</span></div>
                            @else
                                <div>Batch: <span class="font-bold text-black">{{ $user->batch ? $user->batch->name : 'N/A' }}</span></div>
                            @endif
                            <div>Course Fee: <span class="font-medium text-black">₹{{ number_format($user->course_fee ?? 0, 2) }}</span></div>
                            <div>Fees Paid: <span class="font-medium text-green-600">₹{{ number_format($user->fees_paid ?? 0, 2) }}</span></div>
                            <div>Balance Due: <span class="font-medium text-red-600">₹{{ number_format($user->balance_fees_due ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                </div>
                @if($user->attachments && count($user->attachments) > 0)
                <div class="mb-6 mt-10">
                    <h2 class="text-2xl font-bold text-black mb-4">Uploaded Certificates</h2>
                    <div class="p-6">
                        <ul class="ml-4 list-disc">
                            @foreach($user->attachments as $attachment)
                                <li>
                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ basename($attachment) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div class="mb-6 mt-10">
                    <h2 class="text-2xl font-bold text-black mb-4">Progress Report</h2>
                    <div class="p-6">
                        @php
                            // Get user's assigned courses and days
                            $assignedCourseIds = $user->assignedCourses()->pluck('id')->toArray();
                            $assignedDayIds = $user->assignedDays()->pluck('id')->toArray();
                            
                            // Get answered question IDs for this user
                            $answeredQuestionIds = \App\Models\StudentAnswer::where('user_id', $user->id)->pluck('question_id')->toArray();
                            
                            // Get all subjects (limit to first 4 for display)
                            $subjects = \App\Models\Subject::take(4)->get();
                            
                            $subjectProgress = [];
                            
                            foreach($subjects as $subject) {
                                // Get all questions for this subject from user's assigned courses and days
                                $totalQuestions = \App\Models\Question::where('subject_id', $subject->id)
                                    ->whereIn('course_id', $assignedCourseIds)
                                    ->whereIn('day_id', $assignedDayIds)
                                    ->where('is_active', true)
                                    ->count();
                                
                                // Get answered questions for this subject
                                $answeredQuestions = \App\Models\Question::where('subject_id', $subject->id)
                                    ->whereIn('course_id', $assignedCourseIds)
                                    ->whereIn('day_id', $assignedDayIds)
                                    ->where('is_active', true)
                                    ->whereIn('id', $answeredQuestionIds)
                                    ->count();
                                
                                // Calculate percentage
                                $percentage = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
                                
                                $subjectProgress[] = [
                                    'name' => $subject->name,
                                    'percentage' => $percentage,
                                    'answered' => $answeredQuestions,
                                    'total' => $totalQuestions
                                ];
                            }
                        @endphp
                        
                        <div class="space-y-3">
                            @forelse($subjectProgress as $progress)
                                <div>
                                    <div class="flex justify-between text-sm mb-1 text-black">
                                        <span>{{ $progress['name'] }}</span>
                                        <span>{{ $progress['percentage'] }}% ({{ $progress['answered'] }}/{{ $progress['total'] }})</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        @php
                                            $color = 'bg-gray-400';
                                            if($progress['percentage'] >= 80) $color = 'bg-green-500';
                                            elseif($progress['percentage'] >= 60) $color = 'bg-yellow-400';
                                            elseif($progress['percentage'] >= 40) $color = 'bg-orange-400';
                                            else $color = 'bg-red-400';
                                        @endphp
                                        <div class="{{ $color }} h-2.5 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-4">
                                    <p>No subjects available yet.</p>
                                    <p class="text-sm">Progress will be shown once you start answering questions.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </main>
        
        <!-- Enhanced Footer -->
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 py-1 w-full"></div>
        <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-6 sm:py-8 w-full mt-auto">
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
    <!-- Security Watermark -->
    @include('components.student-watermark')

</body>
</html> 