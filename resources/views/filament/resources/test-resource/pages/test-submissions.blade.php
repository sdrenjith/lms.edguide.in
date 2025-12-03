<x-filament-panels::page>
    <div class="space-y-8 px-8">
        <!-- Test Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Test Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-2">
                    <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Test Name</span>
                    <p class="text-lg text-gray-900 font-medium">{{ $this->record->name }}</p>
                </div>
                <div class="space-y-2">
                    <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Course</span>
                    <p class="text-lg text-gray-900 font-medium">{{ $this->record->course->name ?? 'N/A' }}</p>
                </div>
                <div class="space-y-2">
                    <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Subject</span>
                    <p class="text-lg text-gray-900 font-medium">{{ $this->record->subject->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-8 py-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Student Submissions</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Student Name
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Score
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Result
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Submitted At
                            </th>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @php
                            $submissions = $this->getSubmissions();
                        @endphp
                        
                        @forelse($submissions as $submission)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $submission->user->name ?? 'Unknown' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">
                                        {{ $submission->user->email ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                        {{ $submission->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($submission->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-700') }}">
                                        {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $submission->score ?: 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if($submission->result)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                            {{ $submission->result === 'Pass' ? 'bg-green-100 text-green-800' : 
                                               ($submission->result === 'Fail' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $submission->result }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">
                                        {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i:s') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('filament.admin.resources.tests.submissions.answers', ['record' => $this->record->id, 'student' => $submission->user_id]) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Answers
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-12 text-center">
                                    <div class="text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No submissions found</h3>
                                        <p class="mt-1 text-sm text-gray-500">No students have submitted answers for this test yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page> 