<!-- Security Watermark Component - Single Diagonal Direction -->
<div class="watermark-wrapper">
    <div id="watermark-container" class="fixed inset-0 pointer-events-none z-[9999] overflow-hidden">
        <!-- Single direction diagonal lines from top-right to bottom-left -->
        @for($i = 0; $i < 20; $i++)
        <div class="watermark-marquee-line" style="top: {{ -40 + ($i * 12) }}vh; transform: rotate(-45deg);">
            <div class="marquee-content" style="animation-delay: -{{ $i * 2 }}s;">
                @for($j = 0; $j < 10; $j++)
                <span class="watermark-text">{{ $username ?? auth()->user()->name ?? 'Student' }}</span>
                @endfor
            </div>
        </div>
        @endfor
    </div>

    <style>
        .watermark-wrapper {
            position: relative;
            z-index: 0;
        }
        
        #watermark-container {
            pointer-events: none;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            width: 100vw;
            height: 100vh;
        }
        
        .watermark-marquee-line {
            position: absolute;
            width: 400vw;
            height: 8vh;
            left: -150vw;
            pointer-events: none;
            user-select: none;
            overflow: hidden;
            white-space: nowrap;
            display: flex;
            align-items: center;
        }
        
        .marquee-content {
            display: inline-block;
            animation: marqueeScrollDiagonal 75s linear infinite;
            white-space: nowrap;
        }
        
        .watermark-text {
            display: inline-block;
            font-size: clamp(1.2rem, 2vw, 2.5rem);
            font-weight: 500;
            color: #9CA3AF;
            opacity: 0.06;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            margin-right: 20vw;
            pointer-events: none;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            font-family: 'Georgia', 'Times New Roman', serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1.2;
            vertical-align: middle;
        }
        
        @keyframes marqueeScrollDiagonal {
            0% {
                transform: translateX(100vw);
            }
            100% {
                transform: translateX(-100vw);
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .watermark-text {
                font-size: clamp(1rem, 1.8vw, 2.2rem);
                margin-right: 16vw;
            }
        }
        
        @media (max-width: 768px) {
            .watermark-text {
                font-size: clamp(0.9rem, 1.6vw, 1.8rem);
                margin-right: 14vw;
            }
            
            .watermark-marquee-line {
                height: 6vh;
            }
        }
        
        @media (max-width: 480px) {
            .watermark-text {
                font-size: clamp(0.8rem, 1.4vw, 1.5rem);
                margin-right: 12vw;
            }
            
            .watermark-marquee-line {
                height: 5vh;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .watermark-text {
                color: #6B7280;
                opacity: 0.08;
            }
        }
        
        /* Print protection */
        @media print {
            .watermark-text {
                opacity: 0.25;
                color: #9CA3AF !important;
            }
        }
        
        /* High contrast mode */
        @media (prefers-contrast: high) {
            .watermark-text {
                opacity: 0.04;
            }
        }
    </style>

    <script>
        // Enhanced anti-tampering protection
        document.addEventListener('DOMContentLoaded', function() {
            const watermarkContainer = document.getElementById('watermark-container');
            
            if (watermarkContainer) {
                // Monitor for removal attempts
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList') {
                            mutation.removedNodes.forEach(function(node) {
                                if (node.id === 'watermark-container' || 
                                    node.classList?.contains('watermark-marquee-line')) {
                                    // Restore watermark immediately
                                    if (node.id === 'watermark-container') {
                                        document.body.appendChild(node);
                                    }
                                }
                            });
                        }
                        
                        // Prevent style tampering
                        if (mutation.type === 'attributes' && 
                            (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                            const target = mutation.target;
                            if (target.id === 'watermark-container' || 
                                target.classList?.contains('watermark-marquee-line')) {
                                
                                // Reset unauthorized changes
                                if (target.style.display === 'none' || 
                                    target.style.visibility === 'hidden' ||
                                    target.style.opacity === '0' ||
                                    target.style.zIndex < 9999) {
                                    target.style.display = '';
                                    target.style.visibility = '';
                                    target.style.opacity = '';
                                    target.style.zIndex = '9999';
                                }
                            }
                        }
                    });
                });
                
                observer.observe(document.documentElement, { 
                    childList: true, 
                    subtree: true, 
                    attributes: true,
                    attributeFilter: ['style', 'class', 'id']
                });
                
                // Periodic integrity check
                setInterval(function() {
                    if (!document.contains(watermarkContainer) || 
                        watermarkContainer.style.display === 'none' ||
                        watermarkContainer.style.visibility === 'hidden' ||
                        watermarkContainer.style.opacity === '0') {
                        
                        // Restore watermark
                        if (!document.contains(watermarkContainer)) {
                            document.body.appendChild(watermarkContainer);
                        }
                        watermarkContainer.style.display = '';
                        watermarkContainer.style.visibility = '';
                        watermarkContainer.style.opacity = '';
                        watermarkContainer.style.zIndex = '9999';
                    }
                }, 2000);
            }
        });
        
        // Prevent common tampering methods
        document.addEventListener('keydown', function(e) {
            // Disable F12, Ctrl+Shift+I, Ctrl+U (view source)
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    </script>
</div>