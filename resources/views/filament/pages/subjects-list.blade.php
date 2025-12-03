@extends('filament-panels::page')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="text-4xl mb-2">🎧</div>
            <div class="text-xl font-bold">Listening</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="text-4xl mb-2">✍️</div>
            <div class="text-xl font-bold">Writing</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="text-4xl mb-2">📖</div>
            <div class="text-xl font-bold">Reading</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="text-4xl mb-2">🗣️</div>
            <div class="text-xl font-bold">Speaking</div>
        </div>
    </div>
@endsection 