<div style="text-align:center">
    @if($record->youtube_url)
        <div style="position: relative; width: 100%; max-width: 800px; margin: 0 auto;">
            <!-- Spinner overlay -->
            <div id="yt-spinner" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.7); z-index: 2;">
                <svg style="width: 3rem; height: 3rem; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="#3b82f6" stroke-width="4" opacity="0.25" />
                    <path d="M4 12a8 8 0 018-8" stroke="#3b82f6" stroke-width="4" stroke-linecap="round" />
                </svg>
            </div>
            <iframe 
                id="yt-iframe"
                src="https://www.youtube.com/embed/{{ \App\Helpers\VideoHelper::getYoutubeVideoId($record->youtube_url) }}?rel=0" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                style="width: 100%; height: 450px; border-radius: 8px; z-index: 1;"
                onload="document.getElementById('yt-spinner').style.display='none';"
            ></iframe>
        </div>
        <style>
        @keyframes spin { 100% { transform: rotate(360deg); } }
        </style>
    @elseif($record->video_path)
        <video width="100%" height="auto" controls style="max-width: 800px; border-radius: 8px;">
            <source src="{{ Storage::url($record->video_path) }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @else
        <div style="padding: 2rem; color: #6b7280;">
            <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <p>No video available</p>
        </div>
    @endif
</div> 