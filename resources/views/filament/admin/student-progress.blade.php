@php
    // Get student's assigned courses and days
    $assignedCourseIds = $student->assignedCourses()->pluck('id')->toArray();
    $assignedDayIds = $student->assignedDays()->pluck('id')->toArray();
    
    // Get answered question IDs for this student
    $answeredQuestionIds = \App\Models\StudentAnswer::where('user_id', $student->id)->pluck('question_id')->toArray();
    
    // Get all subjects
    $subjects = \App\Models\Subject::all();
    
    $subjectProgress = [];
    
    foreach($subjects as $subject) {
        // Get all questions for this subject from student's assigned courses and days
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
        
        if ($totalQuestions > 0) { // Only include subjects with questions
            $subjectProgress[] = [
                'name' => $subject->name,
                'percentage' => $percentage,
                'answered' => $answeredQuestions,
                'total' => $totalQuestions
            ];
        }
    }
    
    // Calculate overall progress
    $totalQuestionsOverall = array_sum(array_column($subjectProgress, 'total'));
    $totalAnsweredOverall = array_sum(array_column($subjectProgress, 'answered'));
    $overallPercentage = $totalQuestionsOverall > 0 ? round(($totalAnsweredOverall / $totalQuestionsOverall) * 100) : 0;
@endphp

<div class="space-y-6 px-4 sm:px-6 lg:px-8 py-8" style="width: 97%;">
    <!-- Student Info -->
    <div class="bg-gray-50 rounded-lg p-6" style="margin-left: 3% !important;">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-600">Student:</span>
                <div class="font-semibold">{{ $student->name }}</div>
            </div>
            <div>
                <span class="font-medium text-gray-600">Batch:</span>
                <div class="font-semibold">{{ $student->batch ? $student->batch->name : 'N/A' }}</div>
            </div>
            <div>
                <span class="font-medium text-gray-600">Email:</span>
                <div class="font-semibold">{{ $student->email }}</div>
            </div>
            <div>
                <span class="font-medium text-gray-600">Phone:</span>
                <div class="font-semibold">{{ $student->phone }}</div>
            </div>
        </div>
    </div>

    <!-- Overall Progress -->
    <div class="bg-blue-50 rounded-lg p-6" style="margin-left: 3% !important;">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">Overall Progress</h3>
        <div class="flex items-center justify-between text-sm mb-2">
            <span class="font-medium text-blue-800">Total Progress</span>
            <span class="font-bold text-blue-900">{{ $overallPercentage }}% ({{ $totalAnsweredOverall }}/{{ $totalQuestionsOverall }})</span>
        </div>
        <div class="w-full bg-blue-200 rounded-full h-3">
            @php
                $overallColor = 'bg-gray-400';
                if($overallPercentage >= 80) $overallColor = 'bg-green-500';
                elseif($overallPercentage >= 60) $overallColor = 'bg-yellow-400';
                elseif($overallPercentage >= 40) $overallColor = 'bg-orange-400';
                else $overallColor = 'bg-red-400';
            @endphp
            <div class="{{ $overallColor }} h-3 rounded-full transition-all duration-500" style="width: {{ $overallPercentage }}%"></div>
        </div>
    </div>

    <!-- Subject-wise Progress -->
    <div style="margin-left: 3% !important;">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject-wise Progress</h3>
        
        @forelse($subjectProgress as $progress)
            <div class="bg-gray-800 border border-gray-600 rounded-lg p-4 mb-3">
                <div class="flex justify-between items-center text-sm mb-2">
                    <span class="font-medium text-white">{{ $progress['name'] }}</span>
                    <span class="font-bold text-white">{{ $progress['percentage'] }}% ({{ $progress['answered'] }}/{{ $progress['total'] }})</span>
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
            <div class="text-center text-gray-500 py-8 bg-gray-50 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Progress Data</h3>
                <p class="mt-1 text-sm text-gray-500">This student hasn't been assigned to any courses or subjects yet.</p>
            </div>
        @endforelse
    </div>
</div>