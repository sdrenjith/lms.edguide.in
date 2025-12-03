<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
        selectedValues: @entangle($getStatePath()).defer,
        teachers: @js($getTeachers()),
        toggleSelection(teacherId) {
            if (!this.selectedValues) {
                this.selectedValues = [];
            }
            
            const index = this.selectedValues.indexOf(teacherId);
            if (index > -1) {
                this.selectedValues.splice(index, 1);
            } else {
                this.selectedValues.push(teacherId);
            }
            
            // Debug: Log the current selection
            console.log('Selected teachers:', this.selectedValues);
            
            // Force update the Livewire component
            $wire.set('{{ $getStatePath() }}', this.selectedValues);
        },
        isSelected(teacherId) {
            return this.selectedValues && this.selectedValues.includes(teacherId);
        },
        getSelectedTeacherNames() {
            if (!this.selectedValues || this.selectedValues.length === 0) {
                return 'No teachers selected';
            }
            
            const selectedTeachers = this.teachers.filter(teacher => 
                this.selectedValues.includes(teacher.id)
            );
            
            return selectedTeachers.map(teacher => teacher.name).join(', ');
        }
    }" class="space-y-2">
        
        <div class="relative">
            <div 
                class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 shadow-sm focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500 overflow-hidden"
                style="height: {{ min($getTeachers()->count(), $getVisibleOptions()) * 40 }}px;"
            >
                <div class="overflow-y-auto h-full">
                    @foreach($getTeachers() as $teacher)
                        <div 
                            x-on:click="toggleSelection({{ $teacher->id }})"
                            x-bind:class="{
                                'selected-teacher': isSelected({{ $teacher->id }}),
                                'unselected-teacher': !isSelected({{ $teacher->id }})
                            }"
                            class="px-3 py-2 cursor-pointer transition-all duration-200 ease-in-out border-b last:border-b-0 select-none"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium">{{ $teacher->name }}</span>
                                <div x-show="isSelected({{ $teacher->id }})" class="text-white">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>Click to select/deselect teachers</span>
            <span x-show="selectedValues && selectedValues.length > 0" x-text="`${selectedValues.length} selected`"></span>
        </div>
        
        <!-- Selected teachers summary -->
        <div x-show="selectedValues && selectedValues.length > 0" class="mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selected Teachers:</div>
            <div class="text-sm text-gray-600 dark:text-gray-400" x-text="getSelectedTeacherNames()"></div>
        </div>
        
        <!-- Debug info -->
        <div x-show="false" class="text-xs text-gray-400">
            <p>State Path: {{ $getStatePath() }}</p>
            <p>Selected Values: <span x-text="JSON.stringify(selectedValues)"></span></p>
        </div>
        
    </div>
</x-dynamic-component>

<style>
/* Selected teacher styling - Blue background */
.selected-teacher {
    background-color: #2563eb !important; /* Blue-600 */
    color: white !important;
    border-bottom-color: #1d4ed8 !important; /* Blue-700 */
    box-shadow: 0 1px 3px 0 rgba(37, 99, 235, 0.1), 0 1px 2px 0 rgba(37, 99, 235, 0.06) !important;
}

.selected-teacher:hover {
    background-color: #1d4ed8 !important; /* Blue-700 */
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1), 0 2px 4px -1px rgba(37, 99, 235, 0.06) !important;
}

/* Unselected teacher styling */
.unselected-teacher {
    background-color: white !important;
    color: #374151 !important;
    border-bottom-color: #e5e7eb !important;
}

.unselected-teacher:hover {
    background-color: #f9fafb !important;
    transform: translateY(-1px);
}

/* Dark mode overrides */
.dark .unselected-teacher {
    background-color: #1f2937 !important;
    color: #f9fafb !important;
    border-bottom-color: #374151 !important;
}

.dark .unselected-teacher:hover {
    background-color: #374151 !important;
}

/* Custom scrollbar styling */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #4b5563;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Focus ring animation */
.focus-within\:ring-2:focus-within {
    animation: focusRing 0.2s ease-out;
}

@keyframes focusRing {
    0% {
        transform: scale(0.95);
        opacity: 0.5;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Ensure no extra spacing */
.space-y-2 > * + * {
    margin-top: 0.5rem;
}

/* Remove any default margins from the container */
.relative {
    margin: 0;
    padding: 0;
}

/* Smooth transitions for all interactive elements */
.px-3.py-2 {
    transition: all 0.2s ease-in-out;
}

/* Enhanced visual feedback */
.selected-teacher .text-sm {
    font-weight: 600;
}

.unselected-teacher .text-sm {
    font-weight: 500;
}
</style>

