/**
 * Idle Detection and Auto-Logout System
 * Automatically logs out users after 30 minutes of inactivity
 */

class IdleDetector {
    constructor(options = {}) {
        console.log('IdleDetector constructor called with options:', options);
        this.idleTime = options.idleTime || 6 * 60 * 1000; // 6 minutes for testing
        this.warningTime = options.warningTime || 1 * 60 * 1000; // 1 minute warning for testing
        this.checkInterval = options.checkInterval || 10 * 1000; // Check every 10 seconds for testing
        this.warningShown = false;
        this.lastActivity = Date.now();
        this.lastTabActivity = Date.now(); // Track activity when tab is visible
        this.timer = null;
        this.warningTimer = null;
        this.isTabVisible = true; // Track if tab is currently visible
        this.tabHiddenTime = null; // When tab was hidden
        
        console.log('IdleDetector initialized with idleTime:', this.idleTime, 'ms');
        this.init();
    }

    init() {
        // Track user activity
        this.trackActivity();
        
        // Track tab visibility changes
        this.trackTabVisibility();
        
        // Track page focus/blur events
        this.trackPageFocus();
        
        // Create status indicator
        this.createStatusIndicator();
        
        // Start the idle detection timer
        this.startTimer();
        
        // Show warning before logout
        this.startWarningTimer();
    }

    trackActivity() {
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.lastActivity = Date.now();
                // Only update tab activity if tab is visible
                if (this.isTabVisible) {
                    this.lastTabActivity = Date.now();
                }
                this.warningShown = false;
                this.hideWarning();
            }, true);
        });
    }

    trackTabVisibility() {
        // Handle tab visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Tab became hidden
                this.isTabVisible = false;
                this.tabHiddenTime = Date.now();
                this.updateStatusIndicator();
                console.log('Tab became hidden - stopping activity tracking');
            } else {
                // Tab became visible
                this.isTabVisible = true;
                this.updateStatusIndicator();
                if (this.tabHiddenTime) {
                    const hiddenDuration = Date.now() - this.tabHiddenTime;
                    console.log(`Tab was hidden for ${Math.round(hiddenDuration / 1000)} seconds`);
                    
                    // Check if tab was hidden for too long
                    if (hiddenDuration >= this.idleTime) {
                        console.log('Tab was hidden for too long - logging out');
                        this.logout();
                        return;
                    }
                    
                    // Update last activity to current time when tab becomes visible
                    this.lastActivity = Date.now();
                    this.lastTabActivity = Date.now();
                    this.tabHiddenTime = null;
                }
            }
        });
    }

    trackPageFocus() {
        // Handle window focus/blur events
        window.addEventListener('focus', () => {
            if (this.isTabVisible) {
                this.lastActivity = Date.now();
                this.lastTabActivity = Date.now();
                console.log('Window focused - activity updated');
            }
        });

        window.addEventListener('blur', () => {
            console.log('Window blurred - activity tracking continues');
        });
    }

    createStatusIndicator() {
        // Create a small status indicator in the top-right corner
        console.log('Creating status indicator...');
        const indicator = document.createElement('div');
        indicator.id = 'tab-status-indicator';
        indicator.innerHTML = `
            <div class="fixed top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium shadow-lg z-40 transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
                    <span>Tab Active</span>
                </div>
            </div>
        `;
        
        document.body.appendChild(indicator);
        console.log('Status indicator created and added to DOM');
    }

    updateStatusIndicator() {
        const indicator = document.getElementById('tab-status-indicator');
        if (indicator) {
            if (this.isTabVisible) {
                indicator.className = 'fixed top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium shadow-lg z-40 transition-all duration-300';
                indicator.innerHTML = `
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
                        <span>Tab Active</span>
                    </div>
                `;
            } else {
                indicator.className = 'fixed top-4 right-4 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-medium shadow-lg z-40 transition-all duration-300';
                indicator.innerHTML = `
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-white rounded-full mr-2"></div>
                        <span>Tab Inactive</span>
                    </div>
                `;
            }
        }
    }

    startTimer() {
        this.timer = setInterval(() => {
            const now = Date.now();
            let timeSinceActivity;
            
            // If tab is visible, use regular activity tracking
            if (this.isTabVisible) {
                timeSinceActivity = now - this.lastActivity;
            } else {
                // If tab is hidden, use tab hidden time
                timeSinceActivity = now - this.lastTabActivity;
            }
            
            if (timeSinceActivity >= this.idleTime) {
                this.logout();
            }
        }, this.checkInterval);
    }

    startWarningTimer() {
        this.warningTimer = setInterval(() => {
            const now = Date.now();
            let timeSinceActivity;
            
            // If tab is visible, use regular activity tracking
            if (this.isTabVisible) {
                timeSinceActivity = now - this.lastActivity;
            } else {
                // If tab is hidden, use tab hidden time
                timeSinceActivity = now - this.lastTabActivity;
            }
            
            const timeUntilLogout = this.idleTime - timeSinceActivity;
            
            // Only show warning if tab is visible and warning conditions are met
            if (this.isTabVisible && timeUntilLogout <= this.warningTime && timeUntilLogout > 0 && !this.warningShown) {
                this.showWarning(timeUntilLogout);
            }
        }, this.checkInterval);
    }

    showWarning(timeUntilLogout) {
        this.warningShown = true;
        
        // Create warning modal
        const warningModal = document.createElement('div');
        warningModal.id = 'idle-warning-modal';
        warningModal.innerHTML = `
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md mx-4">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Session Timeout Warning</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        You will be automatically logged out in <span id="countdown-timer" class="font-semibold text-red-600"></span> due to inactivity.
                        <br><span class="text-sm text-gray-500 mt-2 block">Note: Activity is tracked only when this tab is active. Switching to other tabs or applications will continue the countdown.</span>
                        <br><span class="text-xs text-yellow-600 mt-1 block">⚠️ Testing Mode: 6-minute timeout (will be changed to 30 minutes later)</span>
                    </p>
                    <div class="flex space-x-3">
                        <button id="stay-logged-in" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Stay Logged In
                        </button>
                        <button id="logout-now" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Logout Now
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(warningModal);
        
        // Update countdown timer
        const countdownElement = document.getElementById('countdown-timer');
        const updateCountdown = () => {
            const minutes = Math.floor(timeUntilLogout / 60000);
            const seconds = Math.floor((timeUntilLogout % 60000) / 1000);
            countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeUntilLogout > 0) {
                timeUntilLogout -= 1000;
                setTimeout(updateCountdown, 1000);
            }
        };
        updateCountdown();
        
        // Handle stay logged in button
        document.getElementById('stay-logged-in').addEventListener('click', () => {
            this.lastActivity = Date.now();
            this.warningShown = false;
            this.hideWarning();
        });
        
        // Handle logout now button
        document.getElementById('logout-now').addEventListener('click', () => {
            this.logout();
        });
    }

    hideWarning() {
        const warningModal = document.getElementById('idle-warning-modal');
        if (warningModal) {
            warningModal.remove();
        }
    }

    logout() {
        // Clear timers
        if (this.timer) clearInterval(this.timer);
        if (this.warningTimer) clearInterval(this.warningTimer);
        
        // Hide warning if shown
        this.hideWarning();
        
        // Show logout notification
        this.showLogoutNotification();
        
        // Logout after a short delay
        setTimeout(() => {
            // Send logout request to server
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            }).then(() => {
                // Redirect to login page
                window.location.href = '/login';
            }).catch(() => {
                // Fallback redirect even if request fails
                window.location.href = '/login';
            });
        }, 2000);
    }

    showLogoutNotification() {
        const notification = document.createElement('div');
        notification.innerHTML = `
            <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 max-w-md">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <div>
                        <div class="font-medium">Session Timeout</div>
                        <div class="text-sm mt-1">You have been logged out due to inactivity. Redirecting to login...</div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 4 seconds
        setTimeout(() => {
            notification.remove();
        }, 4000);
    }

    destroy() {
        if (this.timer) clearInterval(this.timer);
        if (this.warningTimer) clearInterval(this.warningTimer);
        this.hideWarning();
        
        // Remove status indicator
        const indicator = document.getElementById('tab-status-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
}

// Initialize idle detector when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking for student page...');
    console.log('Body classes:', document.body.className);
    console.log('Current path:', window.location.pathname);
    
    // Only initialize for student pages
    if (document.body.classList.contains('student-page') || 
        window.location.pathname.includes('/student/')) {
        console.log('Initializing idle detector for student page...');
        window.idleDetector = new IdleDetector({
            idleTime: 6 * 60 * 1000, // 6 minutes for testing
            warningTime: 1 * 60 * 1000, // 1 minute warning for testing
            checkInterval: 10 * 1000 // Check every 10 seconds for testing
        });
        console.log('Idle detector initialized successfully');
    } else {
        console.log('Not a student page, skipping idle detector initialization');
    }
});

// Also try to initialize if DOM is already loaded
if (document.readyState === 'loading') {
    console.log('DOM is still loading, waiting for DOMContentLoaded...');
} else {
    console.log('DOM already loaded, checking for student page...');
    if (document.body.classList.contains('student-page') || 
        window.location.pathname.includes('/student/')) {
        console.log('DOM already loaded, initializing idle detector...');
        window.idleDetector = new IdleDetector({
            idleTime: 6 * 60 * 1000, // 6 minutes for testing
            warningTime: 1 * 60 * 1000, // 1 minute warning for testing
            checkInterval: 10 * 1000 // Check every 10 seconds for testing
        });
        console.log('Idle detector initialized successfully');
    }
}
