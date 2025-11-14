// PWA Initialization and Management
(function() {
    'use strict';

    let deferredPrompt;
    let swRegistration = null;

    // Initialize PWA features
    function initPWA() {
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            registerServiceWorker();
        }

        // Setup install prompt
        setupInstallPrompt();

        // Setup online/offline detection
        setupOnlineDetection();

        // Setup update notifications
        setupUpdateNotifications();

        // Check for offline queue on load
        checkOfflineQueue();
    }

    // Register Service Worker
    async function registerServiceWorker() {
        try {
            swRegistration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });

            console.log('[PWA] Service Worker registered successfully:', swRegistration.scope);

            // Check for updates
            swRegistration.addEventListener('updatefound', () => {
                const newWorker = swRegistration.installing;
                console.log('[PWA] Service Worker update found');

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New service worker is installed, show update notification
                        showUpdateNotification();
                    }
                });
            });

            // Listen for messages from service worker
            navigator.serviceWorker.addEventListener('message', handleServiceWorkerMessage);

            // Check for updates every 30 minutes
            setInterval(() => {
                swRegistration.update();
            }, 30 * 60 * 1000);

        } catch (error) {
            console.error('[PWA] Service Worker registration failed:', error);
        }
    }

    // Handle messages from service worker
    function handleServiceWorkerMessage(event) {
        const { type, data } = event.data;

        switch (type) {
            case 'SALE_SYNCED':
                console.log('[PWA] Sale synced:', data);
                showNotification('Sale synced successfully', 'Your offline sale has been synchronized.', 'success');
                checkOfflineQueue();
                break;

            case 'SYNC_FAILED':
                console.log('[PWA] Sync failed:', data);
                showNotification('Sync failed', 'Failed to sync offline sale. Will retry later.', 'error');
                break;
        }
    }

    // Setup install prompt
    function setupInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;

            // Show install button/banner
            showInstallPrompt();
        });

        // Track install
        window.addEventListener('appinstalled', () => {
            console.log('[PWA] App installed successfully');
            deferredPrompt = null;
            hideInstallPrompt();
            showNotification('App Installed', 'Sweet Tooth POS has been installed successfully!', 'success');
        });
    }

    // Show install prompt UI
    function showInstallPrompt() {
        // Check if already dismissed
        if (localStorage.getItem('pwa-install-dismissed') === 'true') {
            return;
        }

        const installBanner = document.createElement('div');
        installBanner.id = 'pwa-install-banner';
        installBanner.className = 'fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:max-w-sm bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4 z-50 animate-slide-up';
        installBanner.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Install Sweet Tooth POS</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">Install our app for faster access and offline support</p>
                    <div class="flex gap-2">
                        <button id="pwa-install-btn" class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors">
                            Install Now
                        </button>
                        <button id="pwa-dismiss-btn" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Maybe Later
                        </button>
                    </div>
                </div>
                <button id="pwa-close-btn" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(installBanner);

        // Install button click
        document.getElementById('pwa-install-btn').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log('[PWA] Install prompt outcome:', outcome);
                deferredPrompt = null;
            }
            hideInstallPrompt();
        });

        // Dismiss button click
        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
            hideInstallPrompt();
        });

        // Close button click
        document.getElementById('pwa-close-btn').addEventListener('click', () => {
            localStorage.setItem('pwa-install-dismissed', 'true');
            hideInstallPrompt();
        });
    }

    // Hide install prompt
    function hideInstallPrompt() {
        const banner = document.getElementById('pwa-install-banner');
        if (banner) {
            banner.remove();
        }
    }

    // Setup online/offline detection
    function setupOnlineDetection() {
        updateOnlineStatus();

        window.addEventListener('online', () => {
            console.log('[PWA] Connection restored');
            updateOnlineStatus();
            showNotification('Back Online', 'Connection restored. Syncing offline data...', 'success');

            // Trigger background sync if available
            if ('serviceWorker' in navigator && 'sync' in swRegistration) {
                swRegistration.sync.register('sync-offline-sales');
            }
        });

        window.addEventListener('offline', () => {
            console.log('[PWA] Connection lost');
            updateOnlineStatus();
            showNotification('Offline Mode', 'You are currently offline. Sales will be queued for sync.', 'warning');
        });
    }

    // Update online status indicator
    function updateOnlineStatus() {
        const isOnline = navigator.onLine;
        let indicator = document.getElementById('pwa-online-indicator');

        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'pwa-online-indicator';
            indicator.className = 'fixed top-4 right-4 z-50';
            document.body.appendChild(indicator);
        }

        indicator.innerHTML = `
            <div class="flex items-center gap-2 px-3 py-2 rounded-lg shadow-lg ${isOnline ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'}">
                <div class="w-2 h-2 rounded-full ${isOnline ? 'bg-green-600' : 'bg-red-600'} animate-pulse"></div>
                <span class="text-xs font-medium ${isOnline ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'}">
                    ${isOnline ? 'Online' : 'Offline'}
                </span>
            </div>
        `;

        // Hide online indicator after 3 seconds if online
        if (isOnline) {
            setTimeout(() => {
                if (indicator && navigator.onLine) {
                    indicator.style.opacity = '0';
                    indicator.style.transition = 'opacity 0.3s';
                    setTimeout(() => indicator.remove(), 300);
                }
            }, 3000);
        }
    }

    // Setup update notifications
    function setupUpdateNotifications() {
        // This will be called when a new service worker is available
    }

    // Show update notification
    function showUpdateNotification() {
        const updateBanner = document.createElement('div');
        updateBanner.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg shadow-lg p-4 z-50 max-w-md';
        updateBanner.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Update Available</p>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">A new version is available.</p>
                </div>
                <button id="pwa-update-btn" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700">
                    Reload
                </button>
            </div>
        `;

        document.body.appendChild(updateBanner);

        document.getElementById('pwa-update-btn').addEventListener('click', () => {
            if (swRegistration && swRegistration.waiting) {
                swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
                window.location.reload();
            }
        });
    }

    // Check offline queue count
    async function checkOfflineQueue() {
        if (!navigator.serviceWorker.controller) return;

        try {
            const messageChannel = new MessageChannel();
            navigator.serviceWorker.controller.postMessage(
                { type: 'GET_OFFLINE_QUEUE_COUNT' },
                [messageChannel.port2]
            );

            messageChannel.port1.onmessage = (event) => {
                const count = event.data.count;
                if (count > 0) {
                    showOfflineQueueBadge(count);
                } else {
                    hideOfflineQueueBadge();
                }
            };
        } catch (error) {
            console.error('[PWA] Error checking offline queue:', error);
        }
    }

    // Show offline queue badge
    function showOfflineQueueBadge(count) {
        let badge = document.getElementById('pwa-offline-badge');

        if (!badge) {
            badge = document.createElement('div');
            badge.id = 'pwa-offline-badge';
            badge.className = 'fixed bottom-4 right-4 z-50';
            document.body.appendChild(badge);
        }

        badge.innerHTML = `
            <div class="bg-orange-500 text-white px-3 py-2 rounded-full shadow-lg flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>${count} sale${count > 1 ? 's' : ''} pending sync</span>
            </div>
        `;
    }

    // Hide offline queue badge
    function hideOfflineQueueBadge() {
        const badge = document.getElementById('pwa-offline-badge');
        if (badge) {
            badge.remove();
        }
    }

    // Show notification
    function showNotification(title, message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm bg-white dark:bg-gray-800 rounded-lg shadow-lg border ${
            type === 'success' ? 'border-green-200 dark:border-green-800' :
            type === 'error' ? 'border-red-200 dark:border-red-800' :
            type === 'warning' ? 'border-orange-200 dark:border-orange-800' :
            'border-blue-200 dark:border-blue-800'
        } p-4 animate-slide-in`;

        const iconColors = {
            success: 'text-green-600 dark:text-green-400',
            error: 'text-red-600 dark:text-red-400',
            warning: 'text-orange-600 dark:text-orange-400',
            info: 'text-blue-600 dark:text-blue-400'
        };

        const icons = {
            success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
            info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
        };

        notification.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 ${iconColors[type]} flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    ${icons[type]}
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${title}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">${message}</p>
                </div>
                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Initialize on DOM load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPWA);
    } else {
        initPWA();
    }
})();
