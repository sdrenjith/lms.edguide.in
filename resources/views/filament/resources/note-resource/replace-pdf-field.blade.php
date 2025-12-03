<div style="display: flex; align-items: flex-start; gap: 2rem;">
    <div>
        <label for="replace_pdf" style="font-weight: 600;">Replace Note PDF (optional)</label>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="file" name="replace_pdf" id="replace_pdf" accept="application/pdf" style="display: block; margin-top: 0.5rem;" onchange="document.getElementById('clear_pdf_btn').style.display = this.value ? 'inline-block' : 'none';" />
            <button type="button" id="clear_pdf_btn" onclick="document.getElementById('replace_pdf').value = ''; this.style.display='none';" style="margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: #e53e3e; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 0.95em; display: none;">Clear</button>
        </div>
        <div style="font-size: 0.95em; color: #2563eb; margin-top: 0.25rem;">Upload a new PDF to replace the existing one. Leave empty to keep the current note file.</div>
        @if(isset($record) && $record->pdf_path)
            <div style="margin-top: 1rem;">
                <iframe src="{{ Storage::url($record->pdf_path) }}" style="width: 100%; max-width: 500px; height: 400px; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);"></iframe>
                <div style="margin-top: 0.5rem;">
                    <a href="{{ Storage::url($record->pdf_path) }}" target="_blank" style="color: #2563eb; text-decoration: underline; font-size: 0.95em;">Open PDF in new tab</a>
                </div>
            </div>
        @endif
    </div>
</div> 