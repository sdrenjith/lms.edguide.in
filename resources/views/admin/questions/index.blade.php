@extends('layouts.admin')
@section('content')
<div class="modern-listing">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('admin.questions.create') }}" class="modern-btn-primary flex items-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add Question</a>
    </div>
    @if(session('success'))
        <div class="modern-alert-success">{{ session('success') }}</div>
    @endif
    @if($questions->isEmpty())
        <div class="modern-alert-info">No questions found. Click <b>Add Question</b> to create your first question.</div>
    @else
        <div class="modern-card-list">
            @foreach($questions as $question)
            <div class="modern-card mb-4 flex flex-col md:flex-row md:items-start md:space-x-6" style="padding: 1rem;">
                <div style="min-width:220px; max-width:320px; word-break:break-word; flex-shrink:0;">
                    <span class="font-semibold text-lg block" style="word-break:break-word;">{{ Str::limit($question->question_text, 120) }}</span>
                </div>
                <div class="flex-1 mt-2 md:mt-0">
                    <div class="mb-1"><strong>Course:</strong> {{ $question->day && $question->day->course ? $question->day->course->name : ($question->course->name ?? 'N/A') }}</div>
                    <div class="mb-1"><strong>Type:</strong> {{ ucfirst($question->question_type) }}</div>
                    @if($question->day)
                        <div class="mb-1"><strong>Day:</strong> {{ $question->day->title ?? 'Untitled' }}</div>
                    @endif
                    @if($question->question_type === 'mcq' && $question->options)
                        <div class="mt-2">
                            <small class="text-gray-500">Options:</small>
                            <ul class="modern-list mt-1">
                                @foreach(json_decode($question->options, true) as $index => $option)
                                    <li class="flex items-center text-sm"><span class="bg-gray-100 text-gray-700 px-2 py-1 rounded mr-2">{{ chr(65 + $index) }}</span>{{ $option }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="flex items-center space-x-2 mt-3">
                        <a href="{{ route('admin.questions.edit', $question) }}" class="modern-btn-secondary" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13h3l8-8a2.828 2.828 0 10-4-4l-8 8v3z"></path></svg></a>
                        <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button class="modern-btn-danger" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </form>
                    </div>
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
.modern-listing { max-width: 1100px; margin: 0 auto; }
.modern-btn-primary { background: #2563eb; color: #fff; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 500; transition: background 0.2s; }
.modern-btn-primary:hover { background: #1d4ed8; }
.modern-btn-secondary { background: #f3f4f6; color: #111; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.95rem; }
.modern-btn-secondary:hover { background: #e5e7eb; }
.modern-btn-danger { background: #fee2e2; color: #b91c1c; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.95rem; }
.modern-btn-danger:hover { background: #fecaca; }
.modern-card-list { display: flex; flex-direction: column; gap: 1rem; }
.modern-card { background: #000000; border-radius: 1rem; box-shadow: 0 2px 8px 0 #0001; padding: 1rem; }
.modern-card span.font-semibold { max-width: 320px; display: block; word-break: break-word; }
.modern-alert-success { background: #dcfce7; color: #166534; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
.modern-alert-info { background: #e0e7ff; color: #3730a3; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
.modern-list { list-style: none; padding: 0; margin: 0; }
@media (max-width: 768px) {
  .modern-card { flex-direction: column !important; }
}
</style>
@endpush 