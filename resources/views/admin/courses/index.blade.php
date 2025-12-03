@extends('layouts.admin')
@section('content')
<div class="modern-listing">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.courses.create') }}" class="modern-btn-primary flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add Course</a>
    </div>
    @if(session('success'))
        <div class="modern-alert-success">{{ session('success') }}</div>
    @endif
    @if($courses->isEmpty())
        <div class="modern-alert-info">No courses found. Click <b>Add Course</b> to create your first course.</div>
    @else
        <div class="modern-card-list">
            @foreach($courses as $course)
            <div class="modern-card mb-4">
                <div class="flex justify-between items-center border-b pb-2 mb-2">
                    <span class="font-semibold text-lg">{{ $course->name }}</span>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="modern-btn-secondary" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 10-4-4l-8 8v3z"></path></svg></a>
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button class="modern-btn-danger" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </form>
                    </div>
                </div>
                <div>
                    @if($course->days->isEmpty())
                        <div class="text-gray-500">No days assigned to this course.</div>
                    @else
                        <div class="modern-accordion">
                            @foreach($course->days as $day)
                            <div class="modern-accordion-item mb-2">
                                <div class="font-medium">Day {{ $day->day_number }}: {{ $day->title ?? 'Untitled' }}</div>
                                <div>
                                    @if($day->questions->isEmpty())
                                        <div class="text-gray-400">No questions for this day.</div>
                                    @else
                                        <ul class="modern-list">
                                            @foreach($day->questions as $question)
                                            <li class="flex justify-between items-center py-1">
                                                <span>{{ Str::limit($question->question_text, 60) }}</span>
                                                <a href="{{ route('admin.questions.edit', $question) }}" class="modern-btn-secondary">Edit</a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.querySelector('.toast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
</script>
@endpush
@push('styles')
<style>
.modern-listing { max-width: 900px; margin: 0 auto; }
.modern-btn-primary { background: #2563eb; color: #fff; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 500; transition: background 0.2s; }
.modern-btn-primary:hover { background: #1d4ed8; }
.modern-btn-secondary { background: #f3f4f6; color: #111; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.95rem; }
.modern-btn-secondary:hover { background: #e5e7eb; }
.modern-btn-danger { background: #fee2e2; color: #b91c1c; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.95rem; }
.modern-btn-danger:hover { background: #fecaca; }
.modern-card-list { display: flex; flex-direction: column; gap: 1.5rem; }
.modern-card { background: #000000; border-radius: 1rem; box-shadow: 0 2px 8px 0 #0001; padding: 1.5rem; }
.modern-alert-success { background: #dcfce7; color: #166534; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
.modern-alert-info { background: #e0e7ff; color: #3730a3; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
.modern-accordion { margin-top: 1rem; }
.modern-accordion-item { background: #f9fafb; border-radius: 0.5rem; padding: 0.75rem 1rem; }
.modern-list { list-style: none; padding: 0; margin: 0; }
</style>
@endpush 