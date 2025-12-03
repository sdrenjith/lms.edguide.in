<x-filament-panels::page class="fi-dashboard-page">
    <div class="py-8 px-4">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-800" style="font-family: 'Segoe UI', Arial, sans-serif;">Dashboard</h1>
        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="rounded-2xl shadow-lg p-8 bg-gradient-to-br from-blue-400 to-blue-600 text-white flex flex-col justify-between min-h-[160px]">
                <div class="text-5xl font-black mb-2">{{ App\Models\Course::count() }}</div>
                <div class="text-lg font-semibold tracking-wide">Courses</div>
                <a href="{{ route('admin.courses.index') }}" class="mt-4 text-white text-sm font-bold underline hover:text-blue-200">More info</a>
            </div>
            <div class="rounded-2xl shadow-lg p-8 bg-gradient-to-br from-green-400 to-green-600 text-white flex flex-col justify-between min-h-[160px]">
                <div class="text-5xl font-black mb-2">{{ App\Models\Day::count() }}</div>
                <div class="text-lg font-semibold tracking-wide">Days</div>
                <a href="{{ route('admin.days.index') }}" class="mt-4 text-white text-sm font-bold underline hover:text-green-200">More info</a>
            </div>
            <div class="rounded-2xl shadow-lg p-8 bg-gradient-to-br from-orange-400 to-orange-600 text-white flex flex-col justify-between min-h-[160px]">
                <div class="text-5xl font-black mb-2">{{ App\Models\Question::count() }}</div>
                <div class="text-lg font-semibold tracking-wide">Questions</div>
                <a href="{{ route('admin.questions.index') }}" class="mt-4 text-white text-sm font-bold underline hover:text-orange-200">More info</a>
            </div>
            <div class="rounded-2xl shadow-lg p-8 bg-gradient-to-br from-red-400 to-red-600 text-white flex flex-col justify-between min-h-[160px]">
                <div class="text-5xl font-black mb-2">0</div>
                <div class="text-lg font-semibold tracking-wide">Finance</div>
                <a href="#" class="mt-4 text-white text-sm font-bold underline hover:text-red-200">More info</a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
