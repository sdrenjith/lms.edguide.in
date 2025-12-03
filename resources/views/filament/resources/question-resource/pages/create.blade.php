@extends('filament::page')

@section('content')
<div class="filament-page">
    <div class="filament-page-content">
        <div class="max-w-4xl py-6">
            <form id="question-create-form" method="POST" action="{{ url()->current() }}" x-data="questionForm()" @submit.prevent="submitForm">
                @csrf
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Create New Question</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Day -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Day</label>
                            <select name="day_id" x-model="day_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Day</option>
                                @foreach(\App\Models\Day::all() as $day)
                                    <option value="{{ $day->id }}">{{ $day->title }}</option>
                                @endforeach
                            </select>
                            <template x-if="errors.day_id"><p class="text-red-500 text-xs mt-1" x-text="errors.day_id"></p></template>
                        </div>
                        <!-- Level -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                            <select name="level_id" x-model="level_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Level</option>
                                @foreach(\App\Models\Level::all() as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            <template x-if="errors.level_id"><p class="text-red-500 text-xs mt-1" x-text="errors.level_id"></p></template>
                        </div>
                        <!-- Subject -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <select name="subject_id" x-model="subject_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Subject</option>
                                @foreach(\App\Models\Subject::all() as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            <template x-if="errors.subject_id"><p class="text-red-500 text-xs mt-1" x-text="errors.subject_id"></p></template>
                        </div>
                        <!-- Question Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question Type</label>
                            <select name="question_type_id" x-model="question_type_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Type</option>
                                @foreach(\App\Models\QuestionType::all() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <template x-if="errors.question_type_id"><p class="text-red-500 text-xs mt-1" x-text="errors.question_type_id"></p></template>
                        </div>
                        <!-- Points -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                            <input type="number" name="points" x-model="points" min="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="1">
                            <template x-if="errors.points"><p class="text-red-500 text-xs mt-1" x-text="errors.points"></p></template>
                        </div>
                    </div>
                    <!-- Instruction -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instruction</label>
                        <textarea name="instruction" x-model="instruction" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        <template x-if="errors.instruction"><p class="text-red-500 text-xs mt-1" x-text="errors.instruction"></p></template>
                    </div>
                    <!-- Explanation -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Explanation <span class="text-gray-400 text-xs">(optional)</span></label>
                        <textarea name="explanation" x-model="explanation" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <!-- Question Options Section -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-blue-700">Question Options</h3>
                        <button type="button" @click="addOption" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-all duration-150">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add Option
                        </button>
                    </div>
                    <template x-for="(option, idx) in options" :key="idx">
                        <div class="flex items-center mb-3 transition-all duration-200" :class="{'opacity-80': removingOption === idx}">
                            <span class="w-20 text-gray-500 font-medium">Option <span x-text="idx+1"></span></span>
                            <input type="text" :name="'question_options['+idx+']'" x-model="option.text" class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 mr-2" placeholder="Enter option text...">
                            <button type="button" @click="removeOption(idx)" class="ml-2 text-red-500 hover:text-red-700 transition-all duration-150" x-show="options.length > 1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <template x-if="errors.options"><p class="text-red-500 text-xs mt-1" x-text="errors.options"></p></template>
                </div>

                <!-- Correct Answer Indices Section -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-blue-700">Correct Answer Indices</h3>
                        <button type="button" @click="addIndex" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-all duration-150">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add Index
                        </button>
                    </div>
                    <template x-for="(index, idx) in indices" :key="idx">
                        <div class="flex items-center mb-3 transition-all duration-200" :class="{'opacity-80': removingIndex === idx}">
                            <span class="w-20 text-gray-500 font-medium">Index <span x-text="idx+1"></span></span>
                            <input type="number" min="0" :name="'correct_indices['+idx+']'" x-model="index.value" class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 mr-2" placeholder="e.g. 0">
                            <button type="button" @click="removeIndex(idx)" class="ml-2 text-red-500 hover:text-red-700 transition-all duration-150" x-show="indices.length > 1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <template x-if="errors.indices"><p class="text-red-500 text-xs mt-1" x-text="errors.indices"></p></template>
                </div>

                <!-- Hidden JSON fields -->
                <input type="hidden" name="question_data" :value="questionDataJson">
                <input type="hidden" name="answer_data" :value="answerDataJson">

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-700 text-white font-semibold rounded shadow hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-150" :disabled="loading">
                        <svg x-show="loading" class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                        <span x-text="loading ? 'Saving...' : 'Create Question'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function questionForm() {
    return {
        day_id: '',
        level_id: '',
        subject_id: '',
        question_type_id: '',
        points: 1,
        instruction: '',
        explanation: '',
        options: [{ text: '' }],
        indices: [{ value: 0 }],
        errors: {},
        loading: false,
        removingOption: null,
        removingIndex: null,
        get questionDataJson() {
            return JSON.stringify({
                question: this.instruction,
                options: this.options.map(o => o.text)
            });
        },
        get answerDataJson() {
            return JSON.stringify({
                correct_indices: this.indices.map(i => parseInt(i.value) || 0)
            });
        },
        addOption() {
            this.options.push({ text: '' });
        },
        removeOption(idx) {
            this.removingOption = idx;
            setTimeout(() => {
                this.options.splice(idx, 1);
                this.removingOption = null;
            }, 150);
        },
        addIndex() {
            this.indices.push({ value: 0 });
        },
        removeIndex(idx) {
            this.removingIndex = idx;
            setTimeout(() => {
                this.indices.splice(idx, 1);
                this.removingIndex = null;
            }, 150);
        },
        async submitForm() {
            this.errors = {};
            this.loading = true;
            // Simple client-side validation
            if (!this.day_id) this.errors.day_id = 'Day is required.';
            if (!this.level_id) this.errors.level_id = 'Level is required.';
            if (!this.subject_id) this.errors.subject_id = 'Subject is required.';
            if (!this.question_type_id) this.errors.question_type_id = 'Question type is required.';
            if (!this.instruction) this.errors.instruction = 'Instruction is required.';
            if (this.options.length < 1 || this.options.some(o => !o.text)) this.errors.options = 'All options are required.';
            if (this.indices.length < 1 || this.indices.some(i => i.value === '' || isNaN(i.value))) this.errors.indices = 'All indices are required.';
            if (Object.keys(this.errors).length > 0) {
                this.loading = false;
                return;
            }
            // Submit via POST
            const form = document.getElementById('question-create-form');
            form.submit();
        }
    }
}
</script>
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection