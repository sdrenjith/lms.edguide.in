<?php

namespace App\Http\Controllers;

use App\Models\QuestionMedia;
use Illuminate\Http\Request;

class QuestionMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'media_file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp3,wav,ogg',
        ]);

        $file = $request->file('media_file');
        $mediaType = $file->getMimeType();
        $type = str_contains($mediaType, 'audio') ? 'audio' : 'image';
        $path = $file->store('question_media', 'public');

        $media = \App\Models\QuestionMedia::create([
            'question_id' => $request->question_id,
            'file_path' => $path,
            'media_type' => $type,
        ]);

        return response()->json(['success' => true, 'media' => $media]);
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionMedia $questionMedia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuestionMedia $questionMedia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuestionMedia $questionMedia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuestionMedia $questionMedia)
    {
        //
    }
}
