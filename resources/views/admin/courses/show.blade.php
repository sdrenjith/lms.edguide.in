@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Course: {{ $course->name }}</h2>
    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-animated"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Assigned Days</h5>
        @if($course->days->isEmpty())
            <div class="text-muted">No days assigned to this course.</div>
        @else
            <div class="accordion" id="daysAccordionShow">
                @foreach($course->days as $day)
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="headingDayShow{{ $day->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDayShow{{ $day->id }}" aria-expanded="false" aria-controls="collapseDayShow{{ $day->id }}">
                            Day {{ $day->day_number }}: {{ $day->title ?? 'Untitled' }}
                        </button>
                    </h2>
                    <div id="collapseDayShow{{ $day->id }}" class="accordion-collapse collapse" aria-labelledby="headingDayShow{{ $day->id }}" data-bs-parent="#daysAccordionShow">
                        <div class="accordion-body">
                            @if($day->questions->isEmpty())
                                <div class="text-muted">No questions for this day.</div>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($day->questions as $question)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span>{{ Str::limit($question->question_text, 60) }}</span>
                                            <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-sm btn-outline-secondary btn-animated"><i class="bi bi-pencil"></i> Edit</a>
                                        </div>
                                        @if($question->question_type === 'mcq' && $question->options)
                                            <div class="ms-3">
                                                <small class="text-muted">Options:</small>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach(json_decode($question->options, true) as $index => $option)
                                                        <li class="small">
                                                            <span class="badge bg-light text-dark me-1">{{ chr(65 + $index) }}</span>
                                                            {{ $option }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
<a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary btn-animated me-2"><i class="bi bi-pencil"></i> Edit Course</a>
<form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?');">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-animated"><i class="bi bi-trash"></i> Delete Course</button>
</form>
@endsection 