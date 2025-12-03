<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item->title }} - Secure Preview</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f8fafc;
            height: 100vh;
            overflow: hidden;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        .preview-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: relative;
        }
        
        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .preview-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            flex: 1;
        }
        
        .preview-meta {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-top: 0.25rem;
        }
        
        .close-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            margin-left: 1rem;
            z-index: 1001;
            position: relative;
        }
        
        .close-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .content-wrapper {
            flex: 1;
            position: relative;
            display: flex;
        }
        
        .preview-content {
            flex: 1;
            overflow: auto !important;
            -webkit-overflow-scrolling: touch;
            height: calc(100vh - 80px);
            position: relative;
        }
        
        .preview-iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        
        .preview-iframe.video-frame {
            background: #000;
        }
        
        /* Custom scroll controls */
        .scroll-controls {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .scroll-btn {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .scroll-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .scroll-btn:active {
            transform: scale(0.95);
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 3rem;
            color: rgba(0,0,0,0.05);
            font-weight: bold;
            z-index: 500;
            pointer-events: none;
            white-space: nowrap;
            font-family: Arial, sans-serif;
        }
        
        .no-print {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        @media print {
            body {
                display: none !important;
            }
        }
        
        /* Disable text selection */
        ::selection {
            background: transparent;
        }
        
        ::-moz-selection {
            background: transparent;
        }
        
        /* Style scrollbars */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 6px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Hide video download options */
        video::-webkit-media-controls-download-button {
            display: none !important;
        }
        
        video::-webkit-media-controls-fullscreen-button {
            display: none !important;
        }
        
        video::-webkit-media-controls-picture-in-picture-button {
            display: none !important;
        }
        
        video::-internal-media-controls-download-button {
            display: none !important;
        }
        
        /* Prevent video element selection but allow controls */
        video {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* PDF styling - block interactions but allow scrolling through container */
        object[type="application/pdf"], 
        iframe {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
            -webkit-user-drag: none !important;
        }
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #667eea;
            font-size: 1.1rem;
            z-index: 10;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Ensure PDF container is scrollable */
        .pdf-container {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        /* Smart security overlay - positioned over PDF but allows scroll */
        .pdf-security-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            z-index: 200;
            pointer-events: auto;
            cursor: default;
        }
        
        /* Mobile responsive scroll controls */
        @media (max-width: 768px) {
            .scroll-controls {
                right: 10px;
                gap: 6px;
            }
            
            .scroll-btn {
                width: 40px;
                height: 40px;
            }
        }
        
        @media (max-width: 480px) {
            .scroll-controls {
                right: 5px;
                gap: 4px;
            }
            
            .scroll-btn {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>
<body class="no-print">
    <div class="preview-container">
        <!-- Watermark -->
        <div class="watermark">{{ auth()->user()->name }} - CONFIDENTIAL</div>
        
        <!-- Header -->
        <div class="preview-header">
            <div>
                <h1 class="preview-title">{{ $item->title }}</h1>
                <div class="preview-meta">
                    {{ $item->course->name ?? 'N/A' }} - {{ $item->subject->name ?? 'N/A' }}
                    @if($item->topic)
                        | <span style="color: #fbbf24; font-weight: 500;">📚 {{ $item->topic }}</span>
                    @endif
                    @if($item->description)
                        | {{ $item->description }}
                    @endif
                </div>
            </div>
            <button class="close-btn" onclick="closePreview()" title="Close Preview">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Main Content -->
            <div class="preview-content" id="previewContent">
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    Loading {{ $type === 'note' ? 'document' : 'video' }}...
                </div>
                
                @if($type === 'video')
                    @if($item->youtube_url)
                        <div class="video-wrapper" style="width: 100%; height: 100%; position: relative; background: #000;">
                            <iframe 
                                src="https://www.youtube.com/embed/{{ \App\Helpers\VideoHelper::getYoutubeVideoId($item->youtube_url) }}?rel=0" 
                                class="preview-iframe video-frame" 
                                id="previewFrame"
                                onload="hideLoading()"
                                onerror="showPrivateVideoFallback()"
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                style="width: 100%; height: 100%; object-fit: contain;">
                            </iframe>
                            
                            <!-- Fallback for private videos -->
                            <div id="privateVideoFallback" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #1a1a1a; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; text-align: center; padding: 2rem;">
                                <div style="font-size: 4rem; margin-bottom: 1rem;">🔒</div>
                                <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: #ff6b6b;">This video is private</h3>
                                <p style="margin-bottom: 2rem; color: #ccc; max-width: 500px;">
                                    This YouTube video is set to private and cannot be embedded. You can watch it directly on YouTube.
                                </p>
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
                                    <a href="{{ $item->youtube_url }}" target="_blank" style="background: #ff0000; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        Watch on YouTube
                                    </a>
                                    <button onclick="closePreview()" style="background: #6b7280; color: white; padding: 12px 24px; border-radius: 8px; border: none; font-weight: 500; cursor: pointer;">
                                        Close
                                    </button>
                                </div>
                                <div style="margin-top: 2rem; padding: 1rem; background: rgba(255,255,255,0.1); border-radius: 8px; max-width: 600px;">
                                    <p style="font-size: 0.9rem; color: #e5e7eb; margin: 0;">
                                        <strong>Note:</strong> To embed private videos, change their visibility to "Unlisted" in YouTube Studio. 
                                        Unlisted videos can be embedded but are not publicly searchable.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="video-wrapper" style="width: 100%; height: 100%; position: relative; background: #000;">
                            <video 
                                src="{{ $previewUrl }}" 
                                class="preview-iframe video-frame" 
                                id="previewFrame"
                                onloadeddata="hideLoading()"
                                controls
                                controlsList="nodownload nofullscreen noremoteplayback"
                                disablePictureInPicture
                                preload="metadata"
                                style="width: 100%; height: 100%; object-fit: contain;">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @endif
                @else
                    <div class="pdf-container">
                        <!-- PDF viewer with maximum security parameters -->
                        <object 
                            data="{{ $previewUrl }}#view=FitV&scrollbar=0&toolbar=0&navpanes=0&statusbar=0&messages=0&zoom=page-width" 
                            type="application/pdf"
                            class="preview-iframe" 
                            id="previewFrame"
                            onload="hideLoading();"
                            style="width: 100%; height: 200vh; min-height: 1200px;">
                            
                            <!-- Fallback to iframe if object fails -->
                            <iframe 
                                src="{{ $previewUrl }}#view=FitV&scrollbar=0&toolbar=0&navpanes=0&statusbar=0&messages=0" 
                                class="preview-iframe" 
                                onload="hideLoading();"
                                sandbox="allow-same-origin"
                                style="width: 100%; height: 200vh; min-height: 1200px; border: none;">
                                
                                <!-- Final fallback -->
                                <div style="padding: 40px; text-align: center; background: #f8f9fa;">
                                    <h3 style="color: #667eea; margin-bottom: 20px;">PDF Document</h3>
                                    <p style="color: #666; margin-bottom: 20px;">{{ $item->title }}</p>
                                    <p style="color: #999; font-size: 14px;">Your browser doesn't support PDF viewing. Please ensure you have a PDF plugin installed.</p>
                                </div>
                            </iframe>
                        </object>
                        
                        <!-- Smart security overlay - blocks clicks but allows scroll -->
                        <div class="pdf-security-overlay" id="pdfSecurityOverlay"></div>
                    </div>
                @endif
            </div>
            
            <!-- Custom Scroll Controls -->
            <div class="scroll-controls" id="scrollControls">
                <button class="scroll-btn" onclick="scrollToTop()" title="Go to Top">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="18,15 12,9 6,15"></polyline>
                        <polyline points="18,11 12,5 6,11"></polyline>
                    </svg>
                </button>
                <button class="scroll-btn" onclick="scrollUp()" title="Page Up">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="18,15 12,9 6,15"></polyline>
                    </svg>
                </button>
                <button class="scroll-btn" onclick="scrollDown()" title="Page Down">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                <button class="scroll-btn" onclick="scrollToBottom()" title="Go to Bottom">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                        <polyline points="6,13 12,19 18,13"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Security measures - NO overlays that block scroll
        (function() {
            'use strict';
            
            // Disable right-click context menu globally
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }, true);
            
            // Disable text selection globally
            document.addEventListener('selectstart', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }, true);
            
            // Disable drag and drop globally
            document.addEventListener('dragstart', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }, true);
            
            // Block dangerous mouse events but allow scroll
            document.addEventListener('mousedown', function(e) {
                // Allow clicks on close button and scroll controls
                if (e.target.closest('.close-btn') || e.target.closest('.scroll-controls')) {
                    return true;
                }
                // Block right-click everywhere
                if (e.button === 2) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }, true);
            
            // Block all dangerous keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Block: F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, Ctrl+S, Ctrl+P, Ctrl+C, F5, Ctrl+R, Ctrl+A
                if (e.keyCode === 123 || // F12
                    e.keyCode === 116 || // F5
                    (e.ctrlKey && e.keyCode === 82) || // Ctrl+R
                    (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) || // Ctrl+Shift+I/J
                    (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 83 || e.keyCode === 80 || e.keyCode === 67 || e.keyCode === 65))) { // Ctrl+U/S/P/C/A
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }, true);
            
            // Disable print
            window.addEventListener('beforeprint', function(e) {
                e.preventDefault();
                return false;
            });
            
            // Developer tools detection (less aggressive for videos)
            let devtools = false;
            const threshold = 160;
            let isVideo = document.querySelector('video') !== null || document.querySelector('iframe[src*="youtube.com"]') !== null;
            
            function checkDevTools() {
                if (window.outerHeight - window.innerHeight > threshold || 
                    window.outerWidth - window.innerWidth > threshold) {
                    if (!devtools) {
                        devtools = true;
                        // For videos, show a warning instead of immediately redirecting
                        if (isVideo) {
                            alert('Please close developer tools to continue watching the video.');
                            devtools = false; // Reset to allow retry
                        } else {
                            window.location.href = '/student/study-materials';
                        }
                    }
                }
            }
            
            // Check less frequently for videos to avoid interruption
            setInterval(checkDevTools, isVideo ? 2000 : 500);
            
            // Auto-close on prolonged inactivity (disabled for videos to prevent interruption)
            let blurTimeout;
            
            if (!isVideo) {
                // Only apply blur timeout for PDFs, not videos
                window.addEventListener('blur', function() {
                    blurTimeout = setTimeout(function() {
                        closePreview();
                    }, 10000);
                });
                
                window.addEventListener('focus', function() {
                    if (blurTimeout) {
                        clearTimeout(blurTimeout);
                    }
                });
            } else {
                // For videos, add user activity tracking to prevent any accidental closures
                let lastActivity = Date.now();
                let activityTimeout;
                
                function resetActivity() {
                    lastActivity = Date.now();
                    if (activityTimeout) {
                        clearTimeout(activityTimeout);
                    }
                }
                
                // Track user activity
                ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(function(name) {
                    document.addEventListener(name, resetActivity, true);
                });
                
                // Track video interaction
                const videoElement = document.querySelector('video');
                if (videoElement) {
                    videoElement.addEventListener('play', resetActivity);
                    videoElement.addEventListener('pause', resetActivity);
                    videoElement.addEventListener('seeked', resetActivity);
                }
                
                // Track YouTube iframe interaction
                const youtubeIframe = document.querySelector('iframe[src*="youtube.com"]');
                if (youtubeIframe) {
                    // YouTube iframe events
                    youtubeIframe.addEventListener('load', resetActivity);
                    // Track clicks on YouTube iframe
                    youtubeIframe.addEventListener('click', resetActivity);
                }
            }
            
        })();
        
        // Scroll functions for custom controls - Moderate scroll distances (about 5cm)
        function scrollUp() {
            const container = document.getElementById('previewContent');
            if (container) {
                container.scrollBy({
                    top: -150, // About 5cm scroll distance
                    behavior: 'smooth'
                });
            }
        }
        
        function scrollDown() {
            const container = document.getElementById('previewContent');
            if (container) {
                container.scrollBy({
                    top: 150, // About 5cm scroll distance
                    behavior: 'smooth'
                });
            }
        }
        
        function scrollToTop() {
            const container = document.getElementById('previewContent');
            if (container) {
                container.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }
        
        function scrollToBottom() {
            const container = document.getElementById('previewContent');
            if (container) {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
            }
        }
        
        // Keyboard scroll navigation - Moderate distances
        document.addEventListener('keydown', function(e) {
            const container = document.getElementById('previewContent');
            if (container) {
                switch(e.keyCode) {
                    case 38: // Up arrow
                        container.scrollBy({ top: -100, behavior: 'smooth' }); // Small scroll
                        e.preventDefault();
                        break;
                    case 40: // Down arrow
                        container.scrollBy({ top: 100, behavior: 'smooth' }); // Small scroll
                        e.preventDefault();
                        break;
                    case 33: // Page Up
                        container.scrollBy({ top: -400, behavior: 'smooth' }); // Medium scroll
                        e.preventDefault();
                        break;
                    case 34: // Page Down
                        container.scrollBy({ top: 400, behavior: 'smooth' }); // Medium scroll
                        e.preventDefault();
                        break;
                }
            }
        });
        
        function hideLoading() {
            const loading = document.getElementById('loading');
            if (loading) {
                loading.style.display = 'none';
            }
            
            // Check if iframe loaded successfully (for YouTube videos)
            const iframe = document.getElementById('previewFrame');
            if (iframe && iframe.src.includes('youtube.com')) {
                // Set a timeout to check if the iframe content loaded properly
                setTimeout(function() {
                    try {
                        // Try to access iframe content - if it fails, it's likely private
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        if (!iframeDoc || iframeDoc.body.innerHTML.includes('private') || iframeDoc.body.innerHTML.includes('This video is private')) {
                            showPrivateVideoFallback();
                        }
                    } catch (e) {
                        // Cross-origin error is expected for YouTube, but we can check other indicators
                        // If we can't access the content, it might be private
                        console.log('Cannot access iframe content - video might be private');
                    }
                }, 3000); // Wait 3 seconds for iframe to load
            }
            
            // Security for video elements
            const videoElement = document.querySelector('video');
            if (videoElement) {
                videoElement.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }, true);
                
                // Remove download attributes
                videoElement.removeAttribute('download');
                if ('pictureInPictureEnabled' in document) {
                    videoElement.disablePictureInPicture = true;
                }
            }
            
            // Ensure scrolling works perfectly
            const previewContent = document.getElementById('previewContent');
            if (previewContent) {
                previewContent.style.overflow = 'auto';
                previewContent.style.overflowY = 'auto';
                previewContent.style.scrollBehavior = 'smooth';
            }
            
            // Setup smart security overlay for PDF
            const pdfSecurityOverlay = document.getElementById('pdfSecurityOverlay');
            if (pdfSecurityOverlay) {
                // Block all dangerous interactions
                pdfSecurityOverlay.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
                
                pdfSecurityOverlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
                
                pdfSecurityOverlay.addEventListener('dblclick', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
                
                pdfSecurityOverlay.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
                
                pdfSecurityOverlay.addEventListener('mouseup', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }, true);
                
                pdfSecurityOverlay.addEventListener('selectstart', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }, true);
                
                pdfSecurityOverlay.addEventListener('dragstart', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }, true);
                
                // CRITICAL: Handle scroll events properly with moderate speed
                pdfSecurityOverlay.addEventListener('wheel', function(e) {
                    // Prevent default to stop the overlay from interfering
                    e.preventDefault();
                    
                    // Get the main content container
                    const mainContainer = document.getElementById('previewContent');
                    if (mainContainer) {
                        // Apply moderate scroll speed (about 5cm equivalent)
                        mainContainer.scrollTop += e.deltaY * 0.5; // Reduced from direct deltaY
                        mainContainer.scrollLeft += e.deltaX * 0.5;
                    }
                }, { passive: false });
                
                // Handle touch events for mobile scrolling with moderate speed
                let touchStartY = 0;
                pdfSecurityOverlay.addEventListener('touchstart', function(e) {
                    touchStartY = e.touches[0].clientY;
                }, { passive: true });
                
                pdfSecurityOverlay.addEventListener('touchmove', function(e) {
                    e.preventDefault();
                    const touchY = e.touches[0].clientY;
                    const deltaY = (touchStartY - touchY) * 0.8; // Moderate touch scroll speed
                    
                    const mainContainer = document.getElementById('previewContent');
                    if (mainContainer) {
                        mainContainer.scrollTop += deltaY;
                    }
                    
                    touchStartY = touchY;
                }, { passive: false });
            }
            
            // Show scroll controls only for PDFs, hide for videos
            const scrollControls = document.getElementById('scrollControls');
            if (scrollControls) {
                // Check if this is a PDF preview
                const isPdf = document.querySelector('object[type="application/pdf"], iframe');
                // Check if this is a video preview (including YouTube embeds)
                const isVideo = document.querySelector('video') || document.querySelector('iframe[src*="youtube.com"]');
                
                if (isPdf && !isVideo) {
                    // Show scroll controls for PDFs
                    scrollControls.style.display = 'flex';
                } else {
                    // Hide scroll controls for videos
                    scrollControls.style.display = 'none';
                }
            }
        }
        
        function closePreview() {
            if (window.opener) {
                window.close();
            } else {
                window.location.href = '/student/study-materials';
            }
        }
        
        function showPrivateVideoFallback() {
            const iframe = document.getElementById('previewFrame');
            const fallback = document.getElementById('privateVideoFallback');
            const loading = document.getElementById('loading');
            
            if (iframe && fallback && loading) {
                iframe.style.display = 'none';
                loading.style.display = 'none';
                fallback.style.display = 'flex';
            }
        }
        
        // Prevent back button
        history.pushState(null, null, location.href);
        window.addEventListener('popstate', function(event) {
            history.pushState(null, null, location.href);
        });
        
        // Auto-close after 30 minutes
        setTimeout(function() {
            closePreview();
        }, 30 * 60 * 1000);
        
        // Additional PDF security - block interactions on PDF elements directly
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                // Block dangerous interactions on PDF elements but keep them functional for scrolling
                // Exclude YouTube iframes from PDF security measures
                const pdfElements = document.querySelectorAll('object[type="application/pdf"], iframe:not([src*="youtube.com"])');
                pdfElements.forEach(function(element) {
                    // Block context menu and selection
                    element.addEventListener('contextmenu', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        return false;
                    }, true);
                    
                    element.addEventListener('selectstart', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }, true);
                    
                    element.addEventListener('dragstart', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }, true);
                    
                    // Block clicks but don't use pointer-events: none to keep scrolling
                    element.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }, true);
                    
                    element.addEventListener('dblclick', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }, true);
                    
                    element.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }, true);
                    
                    // Set attributes to prevent interactions
                    element.setAttribute('oncontextmenu', 'return false;');
                    element.setAttribute('onselectstart', 'return false;');
                    element.setAttribute('ondragstart', 'return false;');
                    element.setAttribute('onclick', 'return false;');
                });
            }, 1000);
        });
        
    </script>
</body>
</html>