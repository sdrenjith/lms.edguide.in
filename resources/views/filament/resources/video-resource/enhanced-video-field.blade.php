<div x-data="{ 
    uploadType: '{{ isset($record) && $record->youtube_url ? 'youtube' : 'file' }}',
    showFileUpload: {{ isset($record) && $record->youtube_url ? 'false' : 'true' }},
    showYoutubeInput: {{ isset($record) && $record->youtube_url ? 'true' : 'false' }}
}" style="display: flex; flex-direction: column; gap: 1rem;">
    
    <!-- Upload Type Toggle -->
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
        <label style="font-weight: 600; color: #374151;">Video Source:</label>
        <div style="display: flex; background: #f3f4f6; border-radius: 8px; padding: 2px;">
            <button 
                type="button" 
                @click="uploadType = 'file'; showFileUpload = true; showYoutubeInput = false;"
                :class="uploadType === 'file' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-800'"
                style="padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                File Upload
            </button>
            <button 
                type="button" 
                @click="uploadType = 'youtube'; showFileUpload = false; showYoutubeInput = true;"
                :class="uploadType === 'youtube' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-600 hover:text-gray-800'"
                style="padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
                YouTube Link
            </button>
        </div>
    </div>

    <!-- File Upload Section -->
    <div x-show="showFileUpload" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <label for="replace_video" style="font-weight: 600; color: #374151;">Upload Video File</label>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
            <input 
                type="file" 
                name="replace_video" 
                id="replace_video" 
                accept="video/mp4,video/quicktime,video/x-msvideo" 
                style="display: block; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; width: 100%;" 
                onchange="document.getElementById('clear_video_btn').style.display = this.value ? 'inline-block' : 'none';" />
            <button 
                type="button" 
                id="clear_video_btn" 
                onclick="document.getElementById('replace_video').value = ''; this.style.display='none';" 
                style="padding: 0.5rem 1rem; background: #e53e3e; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; display: none; white-space: nowrap;">
                Clear
            </button>
        </div>
        <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem;">
            Supported formats: MP4, MOV, AVI | Max size: 100MB
        </div>
        
        <!-- Current Video Preview -->
        @if(isset($record) && $record->video_path && !$record->youtube_url)
            <div style="margin-top: 1rem; padding: 1rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span style="font-weight: 500; color: #374151;">Current Video</span>
                </div>
                <video controls style="max-width: 400px; width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <source src="{{ Storage::url($record->video_path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @endif
    </div>

    <!-- YouTube Link Section -->
    <div x-show="showYoutubeInput" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <label for="youtube_url" style="font-weight: 600; color: #374151;">YouTube Video URL</label>
        <div style="margin-top: 0.5rem;">
            <input 
                type="url" 
                name="youtube_url" 
                id="youtube_url" 
                value="{{ isset($record) ? $record->youtube_url : '' }}"
                placeholder="https://www.youtube.com/watch?v=..." 
                style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.875rem;"
                onchange="updateYoutubePreview(this.value)">
        </div>
        <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem;">
            Paste the full YouTube video URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID)
        </div>
        
        <!-- YouTube Preview -->
        <div id="youtube_preview" style="margin-top: 1rem; display: none;">
            <div style="padding: 1rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <svg style="width: 1rem; height: 1rem; color: #dc2626;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                    <span style="font-weight: 500; color: #374151;">YouTube Preview</span>
                </div>
                <div id="youtube_embed" style="position: relative; width: 100%; max-width: 400px; height: 225px; border-radius: 8px; overflow: hidden;">
                    <!-- YouTube embed will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Current YouTube Video Preview -->
    @if(isset($record) && $record->youtube_url)
        <div style="margin-top: 1rem; padding: 1rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <svg style="width: 1rem; height: 1rem; color: #dc2626;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
                <span style="font-weight: 500; color: #374151;">Current YouTube Video</span>
            </div>
            <div style="position: relative; width: 100%; max-width: 400px; height: 225px; border-radius: 8px; overflow: hidden;">
                <iframe 
                    src="https://www.youtube.com/embed/{{ getYoutubeVideoId($record->youtube_url) }}?rel=0" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    style="width: 100%; height: 100%;">
                </iframe>
            </div>
        </div>
    @endif
</div>

<script>
function getYoutubeVideoId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

function updateYoutubePreview(url) {
    const preview = document.getElementById('youtube_preview');
    const embed = document.getElementById('youtube_embed');
    
    if (!url) {
        preview.style.display = 'none';
        return;
    }
    
    const videoId = getYoutubeVideoId(url);
    if (videoId) {
        embed.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width: 100%; height: 100%;"></iframe>`;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

// Initialize preview if YouTube URL exists on page load
document.addEventListener('DOMContentLoaded', function() {
    const youtubeUrl = document.getElementById('youtube_url');
    if (youtubeUrl && youtubeUrl.value) {
        updateYoutubePreview(youtubeUrl.value);
    }
});
</script> 