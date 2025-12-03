<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Report - {{ $record->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    @php
        // Get student's assigned courses and days
        $assignedCourseIds = $record->assignedCourses()->pluck('id')->toArray();
        $assignedDayIds = $record->assignedDays()->pluck('id')->toArray();
        
        // Get answered question IDs for this student
        $answeredQuestionIds = \App\Models\StudentAnswer::where('user_id', $record->id)->pluck('question_id')->toArray();
        
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

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="/admin/students" class="text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Progress Report</h1>
                            <p class="text-gray-600">{{ $record->name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Generated on</div>
                        <div class="font-medium">{{ now()->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Info -->
        <div class="bg-white shadow-sm border-b" style="margin-left: 3% !important;">
            <div class="px-4 sm:px-6 lg:px-8 py-6" style="width: 97%;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-600">Student:</span>
                        <div class="font-semibold text-gray-900">{{ $record->name }}</div>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Batch:</span>
                        <div class="font-semibold text-gray-900">{{ $record->batch ? $record->batch->name : 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Email:</span>
                        <div class="font-semibold text-gray-900">{{ $record->email }}</div>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Phone:</span>
                        <div class="font-semibold text-gray-900">{{ $record->phone }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Progress -->
        <div class="bg-blue-50 shadow-sm border-b" style="margin-left: 3% !important;">
            <div class="px-4 sm:px-6 lg:px-8 py-6" style="width: 97%;">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Overall Progress</h3>
                <div class="flex items-center justify-between text-sm mb-3">
                    <span class="font-medium text-blue-800">Total Progress</span>
                    <span class="font-bold text-blue-900">{{ $overallPercentage }}% ({{ $totalAnsweredOverall }}/{{ $totalQuestionsOverall }})</span>
                </div>
                <div class="w-full bg-blue-200 rounded-full h-4">
                    @php
                        $overallColor = 'bg-gray-400';
                        if($overallPercentage >= 80) $overallColor = 'bg-green-500';
                        elseif($overallPercentage >= 60) $overallColor = 'bg-yellow-400';
                        elseif($overallPercentage >= 40) $overallColor = 'bg-orange-400';
                        else $overallColor = 'bg-red-400';
                    @endphp
                    <div class="{{ $overallColor }} h-4 rounded-full transition-all duration-500" style="width: {{ $overallPercentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Subject-wise Progress -->
        <div class="bg-white shadow-sm" style="margin-left: 3% !important;">
            <div class="px-4 sm:px-6 lg:px-8 py-6" style="width: 97%;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject-wise Progress</h3>
                    
                    @forelse($subjectProgress as $progress)
                        <div class="bg-gray-800 border border-gray-600 rounded-lg p-4 mb-4 last:mb-0">
                            <div class="flex justify-between items-center text-sm mb-2">
                                <span class="font-medium text-white">{{ $progress['name'] }}</span>
                                <span class="font-bold text-white">{{ $progress['percentage'] }}% ({{ $progress['answered'] }}/{{ $progress['total'] }})</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                @php
                                    $color = 'bg-gray-400';
                                    if($progress['percentage'] >= 80) $color = 'bg-green-500';
                                    elseif($progress['percentage'] >= 60) $color = 'bg-yellow-400';
                                    elseif($progress['percentage'] >= 40) $color = 'bg-orange-400';
                                    else $color = 'bg-red-400';
                                @endphp
                                <div class="{{ $color }} h-3 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                            </div>

                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-12 bg-gray-50 rounded-lg">
                            <i class="fas fa-chart-bar text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Progress Data</h3>
                            <p class="text-gray-500">This student hasn't been assigned to any courses or subjects yet.</p>
                        </div>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>