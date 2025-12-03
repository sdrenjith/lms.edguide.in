// Copy to clipboard functionality for Filament admin panel

function copyToClipboard(element) {
    const text = element.getAttribute('data-copy-text');
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success notification
            if (window.Livewire) {
                window.Livewire.dispatch('notify', {
                    type: 'success',
                    message: 'Code copied to clipboard!'
                });
            }
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            // Fallback for older browsers
            fallbackCopyTextToClipboard(text);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            if (window.Livewire) {
                window.Livewire.dispatch('notify', {
                    type: 'success',
                    message: 'Code copied to clipboard!'
                });
            }
        } else {
            console.error('Fallback: Copying text command was unsuccessful');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
    }
    
    document.body.removeChild(textArea);
}
