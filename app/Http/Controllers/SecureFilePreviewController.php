<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;
use App\Models\Video;

class SecureFilePreviewController extends Controller
{
    public function previewFile(Request $request, $type, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Get user's assigned courses
        $batch = $user->batch;
        if (!$batch) {
            abort(403, 'No batch assigned');
        }
        
        $assignedCourseIds = $batch->courses->pluck('id')->toArray();
        
        // Validate file access based on type
        if ($type === 'note') {
            $note = Note::where('id', $id)
                ->whereIn('course_id', $assignedCourseIds)
                ->first();
                
            if (!$note) {
                abort(404, 'File not found or access denied');
            }
            
            $filePath = $note->pdf_path;
            $fileName = $note->title . '.pdf';
            
        } elseif ($type === 'video') {
            $video = Video::where('id', $id)
                ->whereIn('course_id', $assignedCourseIds)
                ->first();
                
            if (!$video) {
                abort(404, 'File not found or access denied');
            }
            
            // Check if it's a YouTube video
            if ($video->youtube_url) {
                // For YouTube videos, we'll handle them differently in the view
                $filePath = null;
                $fileName = $video->title;
            } else {
                $filePath = $video->video_path;
                $fileName = $video->title . '.mp4';
            }
            
        } else {
            abort(400, 'Invalid file type');
        }
        
        // Check if file exists in storage (skip for YouTube videos)
        if ($filePath && !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found: ' . $filePath);
        }
        
        $fullPath = $filePath ? Storage::disk('public')->path($filePath) : null;
        
        // Get mime type, with fallback for PDFs
        try {
            $mimeType = Storage::disk('public')->mimeType($filePath);
        } catch (\Exception $e) {
            // Fallback based on file extension
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeType = match($extension) {
                'pdf' => 'application/pdf',
                'mp4' => 'video/mp4',
                'avi' => 'video/avi',
                'mov' => 'video/quicktime',
                'wmv' => 'video/x-ms-wmv',
                default => 'application/octet-stream'
            };
        }
        
        // Security headers to prevent downloading and caching
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Cache-Control' => 'no-cache, no-store, must-revalidate, private',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet',
            'Accept-Ranges' => 'bytes',
        ];
        
        // For PDFs, ensure proper content type and headers
        if ($type === 'note' && strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'pdf') {
            $headers['Content-Type'] = 'application/pdf';
            $headers['Content-Disposition'] = 'inline; filename="' . $fileName . '"';
            $headers['X-Frame-Options'] = 'SAMEORIGIN';
            $headers['Content-Transfer-Encoding'] = 'binary';
            $headers['Accept-Ranges'] = 'bytes';
            unset($headers['Cache-Control']); // Remove strict cache control for PDFs
            $headers['Cache-Control'] = 'private, no-cache, no-store, must-revalidate';
        }
        
        // For videos, add additional security headers
        if ($type === 'video') {
            $headers['X-Content-Security-Policy'] = 'default-src \'self\'; script-src \'none\'; object-src \'none\';';
            $headers['Content-Security-Policy'] = 'default-src \'self\'; script-src \'none\'; object-src \'none\';';
        }
        
        // For PDFs, try streaming the content with proper headers
        if ($type === 'note' && strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'pdf') {
            $fileContent = file_get_contents($fullPath);
            
            return response($fileContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $fileName . '"')
                ->header('Content-Length', strlen($fileContent))
                ->header('Accept-Ranges', 'bytes')
                ->header('X-Frame-Options', 'SAMEORIGIN')
                ->header('Cache-Control', 'private, no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0')
                ->header('X-Content-Type-Options', 'nosniff')
                ->header('X-Download-Options', 'noopen')
                ->header('X-Permitted-Cross-Domain-Policies', 'none')
                ->header('Referrer-Policy', 'no-referrer')
                ->header('Content-Security-Policy', "default-src 'none'; object-src 'self'; plugin-types application/pdf; frame-ancestors 'self'; form-action 'none'; base-uri 'self';")
                ->header('X-XSS-Protection', '1; mode=block')
                ->header('Feature-Policy', "camera 'none'; microphone 'none'; geolocation 'none'; payment 'none';")
                ->header('Permissions-Policy', "camera=(), microphone=(), geolocation=(), payment=()");
        }
        
        // For YouTube videos, redirect to modal view
        if ($type === 'video' && !$filePath) {
            return redirect()->route('secure-modal', ['type' => $type, 'id' => $id]);
        }
        
        return response()->file($fullPath, $headers);
    }
    
    public function previewModal(Request $request, $type, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Get user's assigned courses
        $batch = $user->batch;
        if (!$batch) {
            abort(403, 'No batch assigned');
        }
        
        $assignedCourseIds = $batch->courses->pluck('id')->toArray();
        
        // Validate file access based on type
        if ($type === 'note') {
            $item = Note::where('id', $id)
                ->whereIn('course_id', $assignedCourseIds)
                ->with(['course', 'subject'])
                ->first();
                
            if (!$item) {
                abort(404, 'File not found or access denied');
            }
            
        } elseif ($type === 'video') {
            $item = Video::where('id', $id)
                ->whereIn('course_id', $assignedCourseIds)
                ->with(['course', 'subject'])
                ->first();
                
            if (!$item) {
                abort(404, 'File not found or access denied');
            }
            
        } else {
            abort(400, 'Invalid file type');
        }
        
        return view('components.secure-file-preview', [
            'item' => $item,
            'type' => $type,
            'previewUrl' => route('secure-file-preview', ['type' => $type, 'id' => $id])
        ]);
    }
} 