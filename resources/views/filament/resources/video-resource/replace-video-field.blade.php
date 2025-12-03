<div style="display: flex; align-items: flex-start; gap: 2rem;">
    <div>
        <label for="replace_video" style="font-weight: 600;">Replace Video (optional)</label>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="file" name="replace_video" id="replace_video" accept="video/mp4,video/quicktime,video/x-msvideo" style="display: block; margin-top: 0.5rem;" onchange="document.getElementById('clear_video_btn').style.display = this.value ? 'inline-block' : 'none';" />
            <button type="button" id="clear_video_btn" onclick="document.getElementById('replace_video').value = ''; this.style.display='none';" style="margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: #e53e3e; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 0.95em; display: none;">Clear</button>
        </div>
        <div style="font-size: 0.95em; color: #2563eb; margin-top: 0.25rem;">Upload a new video to replace the existing one. Leave empty to keep the current video.</div>
        @if(isset($record) && $record->video_path)
            <div style="margin-top: 1rem;">
                <video controls style="max-width: 400px; width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <source src="{{ Storage::url($record->video_path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @endif
    </div>
</div> 