@php
    $courses = \App\Models\Course::all();
    $selectedCourses = $getRecord() ? $getRecord()->courses->pluck('id')->toArray() : [];
@endphp

<div class="space-y-3">
    @foreach($courses as $course)
        <label class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer">
            <input 
                type="checkbox" 
                name="courses[]" 
                value="{{ $course->id }}"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                {{ in_array($course->id, $selectedCourses) ? 'checked' : '' }}
                wire:model="data.courses"
            >
            <span class="text-sm font-medium text-gray-900">{{ $course->name }}</span>
        </label>
    @endforeach
</div>

<style>
    input[type="checkbox"] {
        appearance: auto !important;
        -webkit-appearance: checkbox !important;
        -moz-appearance: checkbox !important;
    }
</style> 