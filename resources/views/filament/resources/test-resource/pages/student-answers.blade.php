<x-filament-panels::page>
    <div class="space-y-6 px-8">
        <!-- Student and Test Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Student Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Name:</span> {{ $this->student->name }}</p>
                        <p><span class="font-medium">Email:</span> {{ $this->student->email }}</p>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Test Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Test:</span> {{ $this->test->name }}</p>
                        <p><span class="font-medium">Course:</span> {{ $this->test->course->name }}</p>
                        <p><span class="font-medium">Subject:</span> {{ $this->test->subject->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Results Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Results Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $this->testResults['total_questions'] }}</div>
                    <div class="text-sm text-gray-600">Total Questions</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $this->testResults['answered_questions'] }}</div>
                    <div class="text-sm text-gray-600">Answered</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $this->testResults['earned_points'] }}/{{ $this->testResults['total_points'] }}</div>
                    <div class="text-sm text-gray-600">Points</div>
                </div>
            </div>
            
            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $this->testResults['status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
                           ($this->testResults['status'] === 'In Progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ $this->testResults['status'] }}
                    </span>
                </div>
                
                @if($this->testResults['result'])
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700">Result:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $this->testResults['result'] === 'Pass' ? 'bg-green-100 text-green-800' : 
                               ($this->testResults['result'] === 'Fail' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $this->testResults['result'] }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Individual Question Answers -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Question Answers</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @php
                    $questions = $this->test->questions()->where('is_active', true)->orderBy('id')->get();
                @endphp
                
                @foreach($questions as $index => $question)
                    @php
                        $studentAnswer = $this->studentAnswers->get($question->id);
                        $isAnswered = $studentAnswer !== null;
                        $isCorrect = $isAnswered ? ($studentAnswer->is_correct ?? false) : false;
                        
                        if ($question->questionType && $question->questionType->name === 'opinion') {
                            $isCorrect = $isAnswered ? ($studentAnswer->verification_status === 'verified_correct') : false;
                        }
                    @endphp
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                    {{ $isAnswered ? ($isCorrect ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') : 'bg-gray-100 text-gray-600' }}">
                                    @if($isAnswered)
                                        @if($isCorrect)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        @endif
                                    @else
                                        <span class="text-sm font-medium">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">Question {{ $index + 1 }}</h4>
                                    <p class="text-sm text-gray-500">{{ $question->questionType->name ?? 'Unknown Type' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $question->points ?? 1 }} point{{ ($question->points ?? 1) != 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                        
                        @if($question->instruction)
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-800">{{ $question->instruction }}</p>
                            </div>
                        @endif
                        
                        @if($question->question_text)
                            <div class="mb-4">
                                <p class="text-gray-700">{{ $question->question_text }}</p>
                            </div>
                        @endif
                        
                        @if($isAnswered)
                            <div class="space-y-3">
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Student's Answer:</h5>
                                    <div class="text-sm text-gray-900">
                                        @php
                                            $answerData = json_decode($studentAnswer->answer_data, true);
                                        @endphp
                                        
                                        @if(is_array($answerData))
                                            @if(isset($answerData['text']))
                                                <p>{{ $answerData['text'] }}</p>
                                            @elseif(isset($answerData['selected_options']))
                                                <ul class="list-disc list-inside">
                                                    @foreach($answerData['selected_options'] as $option)
                                                        <li>{{ $option }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <pre class="text-xs">{{ json_encode($answerData, JSON_PRETTY_PRINT) }}</pre>
                                            @endif
                                        @else
                                            <p>{{ $studentAnswer->answer_data }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm font-medium text-gray-700">Result:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                                    </span>
                                    
                                    @if($question->questionType && $question->questionType->name === 'opinion')
                                        <span class="text-sm text-gray-500">
                                            ({{ ucfirst(str_replace('_', ' ', $studentAnswer->verification_status ?? 'pending')) }})
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="text-sm text-gray-500">
                                    Submitted: {{ $studentAnswer->submitted_at ? $studentAnswer->submitted_at->format('M d, Y H:i:s') : 'N/A' }}
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800">Not answered</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page> 