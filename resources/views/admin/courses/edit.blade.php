@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Edit Course</h2>
    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-animated"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form action="{{ route('admin.courses.update', $course) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="name" class="form-label">Course Name</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $course->name) }}" required autofocus>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Assign Days <small class="text-muted">(Drag to reorder)</small></label>
        <select class="form-select" id="days" name="days[]" multiple size="6">
            @foreach($days as $day)
                <option value="{{ $day->id }}" {{ in_array($day->id, $selectedDays) ? 'selected' : '' }}>Day {{ $day->day_number }}: {{ $day->title ?? 'Untitled' }}</option>
            @endforeach
        </select>
        @error('days')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <input type="hidden" name="order[]" id="daysOrderInput">
    <button type="submit" class="btn btn-primary btn-animated"><i class="bi bi-check-circle"></i> Update Course</button>
</form>
@endsection
@push('scripts')
<script>
// Simple drag-to-reorder for multi-select
const select = document.getElementById('days');
let startIdx;
select.addEventListener('dragstart', function(e) {
    startIdx = e.target.index;
});
select.addEventListener('dragover', function(e) {
    e.preventDefault();
});
select.addEventListener('drop', function(e) {
    e.preventDefault();
    const endIdx = Array.from(select.options).indexOf(e.target);
    if (startIdx !== undefined && endIdx !== -1) {
        const options = Array.from(select.options);
        const moved = options.splice(startIdx, 1)[0];
        options.splice(endIdx, 0, moved);
        select.innerHTML = '';
        options.forEach(opt => select.appendChild(opt));
    }
});
document.querySelector('form').addEventListener('submit', function() {
    const order = Array.from(select.selectedOptions).map(opt => opt.value);
    document.getElementById('daysOrderInput').value = order.join(',');
});
</script>
@endpush 