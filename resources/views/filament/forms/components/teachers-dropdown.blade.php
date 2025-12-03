@php
    $teachers = \App\Models\User::where('role', 'teacher')->orderBy('name')->get();
    
    // Get selected teachers from the form data
    $selectedTeachers = [];
    
    // For edit mode, get the current subject's teachers
    if (isset($getRecord) && $getRecord()) {
        $selectedTeachers = $getRecord()->teachers->pluck('id')->toArray();
    } else {
        // For create mode or if no record, try to get from request
        $selectedTeachers = request()->input('teachers', []);
    }
    
    // Ensure selectedTeachers is always an array
    if (!is_array($selectedTeachers)) {
        $selectedTeachers = [];
    }
@endphp

<div class="teachers-dropdown-container">
    <label class="form-label" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">
        Assign Teachers *
    </label>
    <div class="teachers-list" style="border: 2px solid #000; border-radius: 4px; background: white; max-height: 200px; overflow-y: auto;">
        @foreach($teachers as $teacher)
            <div class="teacher-option" 
                 data-teacher-id="{{ $teacher->id }}" 
                 style="padding: 10px; border-bottom: 1px solid #e5e7eb; cursor: pointer; transition: background-color 0.2s; {{ in_array($teacher->id, $selectedTeachers) ? 'background-color: #3b82f6; color: white;' : 'background-color: white; color: black;' }}"
                 onclick="toggleTeacher(this, {{ $teacher->id }})">
                <input type="checkbox" 
                       name="teachers[]" 
                       value="{{ $teacher->id }}" 
                       {{ in_array($teacher->id, $selectedTeachers) ? 'checked' : '' }} 
                       style="margin-right: 10px; display: none;">
                {{ $teacher->name }}
            </div>
        @endforeach
    </div>
    <p class="text-sm text-gray-600 mt-2">Click on a teacher to select/deselect</p>
</div>

<script>
function toggleTeacher(element, teacherId) {
    const checkbox = element.querySelector('input[type="checkbox"]');
    const isSelected = checkbox.checked;
    
    if (isSelected) {
        // Deselect
        checkbox.checked = false;
        element.style.backgroundColor = 'white';
        element.style.color = 'black';
    } else {
        // Select
        checkbox.checked = true;
        element.style.backgroundColor = '#3b82f6';
        element.style.color = 'white';
    }
}

// Initialize the display on page load
document.addEventListener('DOMContentLoaded', function() {
    const teacherOptions = document.querySelectorAll('.teacher-option');
    teacherOptions.forEach(option => {
        const checkbox = option.querySelector('input[type="checkbox"]');
        if (checkbox.checked) {
            option.style.backgroundColor = '#3b82f6';
            option.style.color = 'white';
        }
    });
});
</script>

<style>
.teachers-dropdown-container {
    margin-top: 8px;
}

.form-label {
    font-family: inherit;
    line-height: 1.5;
}

.teacher-option:hover {
    background-color: #f3f4f6 !important;
    color: black !important;
}

.teacher-option:last-child {
    border-bottom: none !important;
}

.teachers-list::-webkit-scrollbar {
    width: 8px;
}

.teachers-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.teachers-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.teachers-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
