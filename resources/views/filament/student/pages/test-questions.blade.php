@php
    $user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Questions | {{ $user->name }} | EdGuide</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 flex flex-col">
        <main class="flex-1 w-full">
            <div class="max-w-6xl w-full mx-auto px-2 sm:px-8 py-6 sm:py-10">
                <h1 class="text-3xl font-normal mb-8 tracking-tight text-gray-900 text-center mx-auto">Test Questions</h1>
                
                @if(count($testStats) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($testStats as $testStat)
                            @php
                                $test = $testStat['test'];
                                $status = $testStat['status'];
                            @endphp
                            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                                <div class="p-6">
                                    <h2 class="text-xl font-bold text-gray-800 mb-4">{{ $test->name }}</h2>
                                    
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Total Questions:</span>
                                            <span class="font-semibold">{{ $testStat['total_questions'] }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Answered:</span>
                                            <span class="font-semibold">{{ $testStat['answered'] }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Status:</span>
                                            <span class="{{ 
                                                $status === 'Completed' ? 'text-green-600 bg-green-50 px-2 py-1 rounded-full' : 
                                                ($status === 'In Progress' ? 'text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full' : 
                                                'text-gray-600 bg-gray-50 px-2 py-1 rounded-full')
                                            }}">
                                                {{ $status }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-6">
                                        <a href="{{ route('filament.student.pages.tests.show', $test) }}" 
                                           class="w-full block text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            View Test Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center bg-white rounded-xl shadow-md p-8">
                        <p class="text-gray-600">No active tests available at the moment.</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html> 