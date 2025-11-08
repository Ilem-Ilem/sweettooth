// Service Worker for Sweet Tooth POS
const CACHE_VERSION = 'v1.0.0';
const CACHE_NAME = `sweettooth-${CACHE_VERSION}`;
const OFFLINE_SALES_QUEUE = 'offline-sales-queue';

// URLs to cache for offline access
const urlsToCache = [
    '/',
    '/manifest.json',
    '/css/app.css',
    '/js/app.js',
];

// POS routes that should work offline
const posRoutes = [
    /^\/branch-dashboard\/sales-dashboard\/pos/,
    /^\/branch-dashboard\/sales-dashboard\/stock-opening/,
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Caching app shell');
                return cache.addAll(urlsToCache);
            })
            .then(() => {
                console.log('[Service Worker] Installed successfully');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('[Service Worker] Installation failed:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            console.log('[Service Worker] Activated successfully');
            return self.clients.claim();
        })
    );
});

// Fetch event - serve from cache when offline
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Only handle same-origin requests
    if (url.origin !== location.origin) {
        return;
    }

    // Check if this is a POS route
    const isPosRoute = posRoutes.some(pattern => pattern.test(url.pathname));

    // Handle API requests differently
    if (url.pathname.startsWith('/api/') || url.pathname.includes('/livewire/')) {
        // For POST requests (like sales), queue them if offline
        if (request.method === 'POST') {
            event.respondWith(
                fetch(request.clone())
                    .then(response => {
                        return response;
                    })
                    .catch(() => {
                        // If offline, queue the request
                        console.log('[Service Worker] Queueing offline request:', url.pathname);
                        return queueOfflineRequest(request.clone())
                            .then(() => {
                                return new Response(
                                    JSON.stringify({
                                        success: true,
                                        message: 'Sale queued for sync when online',
                                        offline: true
                                    }),
                                    {
                                        status: 202,
                                        headers: { 'Content-Type': 'application/json' }
                                    }
                                );
                            });
                    })
            );
            return;
        }

        // For GET requests, try network first, then cache
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Cache successful responses
                    if (response.ok) {
                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    return caches.match(request);
                })
        );
        return;
    }

    // For POS pages and static assets, use cache-first strategy
    if (isPosRoute || request.destination === 'document' || request.destination === 'script' || request.destination === 'style') {
        event.respondWith(
            caches.match(request)
                .then((cachedResponse) => {
                    if (cachedResponse) {
                        // Return cached version and update cache in background
                        fetch(request).then((response) => {
                            if (response.ok) {
                                caches.open(CACHE_NAME).then((cache) => {
                                    cache.put(request, response);
                                });
                            }
                        }).catch(() => {});
                        return cachedResponse;
                    }

                    // Not in cache, fetch from network
                    return fetch(request)
                        .then((response) => {
                            // Cache successful responses
                            if (response.ok) {
                                const responseToCache = response.clone();
                                caches.open(CACHE_NAME).then((cache) => {
                                    cache.put(request, responseToCache);
                                });
                            }
                            return response;
                        })
                        .catch(() => {
                            // Offline and not cached - return offline page
                            return caches.match('/offline.html').then(response => {
                                return response || new Response('Offline - Please check your connection', {
                                    status: 503,
                                    statusText: 'Service Unavailable'
                                });
                            });
                        });
                })
        );
        return;
    }

    // Default: network first, then cache
    event.respondWith(
        fetch(request)
            .then(response => {
                if (response.ok) {
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(request, responseToCache);
                    });
                }
                return response;
            })
            .catch(() => {
                return caches.match(request);
            })
    );
});

// Queue offline requests
async function queueOfflineRequest(request) {
    const db = await openDatabase();
    const requestData = {
        url: request.url,
        method: request.method,
        headers: [...request.headers.entries()],
        body: await request.text(),
        timestamp: Date.now()
    };

    const transaction = db.transaction([OFFLINE_SALES_QUEUE], 'readwrite');
    const store = transaction.objectStore(OFFLINE_SALES_QUEUE);
    await store.add(requestData);
}

// Open IndexedDB for offline queue
function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('SweetToothOfflineDB', 1);

        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(OFFLINE_SALES_QUEUE)) {
                db.createObjectStore(OFFLINE_SALES_QUEUE, { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}

// Background Sync - sync queued sales when back online
self.addEventListener('sync', (event) => {
    console.log('[Service Worker] Background sync triggered:', event.tag);
    if (event.tag === 'sync-offline-sales') {
        event.waitUntil(syncOfflineSales());
    }
});

// Sync offline sales
async function syncOfflineSales() {
    console.log('[Service Worker] Syncing offline sales...');
    try {
        const db = await openDatabase();
        const transaction = db.transaction([OFFLINE_SALES_QUEUE], 'readonly');
        const store = transaction.objectStore(OFFLINE_SALES_QUEUE);
        const allRequests = await store.getAll();

        if (allRequests.length === 0) {
            console.log('[Service Worker] No offline sales to sync');
            return;
        }

        console.log(`[Service Worker] Syncing ${allRequests.length} offline sales`);

        for (const requestData of allRequests) {
            try {
                const headers = new Headers();
                requestData.headers.forEach(([key, value]) => {
                    headers.append(key, value);
                });

                const response = await fetch(requestData.url, {
                    method: requestData.method,
                    headers: headers,
                    body: requestData.body
                });

                if (response.ok) {
                    // Successfully synced, remove from queue
                    const deleteTransaction = db.transaction([OFFLINE_SALES_QUEUE], 'readwrite');
                    const deleteStore = deleteTransaction.objectStore(OFFLINE_SALES_QUEUE);
                    await deleteStore.delete(requestData.id);
                    console.log('[Service Worker] Successfully synced sale:', requestData.id);

                    // Notify clients
                    const clients = await self.clients.matchAll();
                    clients.forEach(client => {
                        client.postMessage({
                            type: 'SALE_SYNCED',
                            saleId: requestData.id
                        });
                    });
                }
            } catch (error) {
                console.error('[Service Worker] Failed to sync sale:', error);
            }
        }

        console.log('[Service Worker] Offline sales sync completed');
    } catch (error) {
        console.error('[Service Worker] Sync error:', error);
        throw error;
    }
}

// Message handler
self.addEventListener('message', (event) => {
    console.log('[Service Worker] Message received:', event.data);

    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }

    if (event.data && event.data.type === 'GET_OFFLINE_QUEUE_COUNT') {
        openDatabase().then(db => {
            const transaction = db.transaction([OFFLINE_SALES_QUEUE], 'readonly');
            const store = transaction.objectStore(OFFLINE_SALES_QUEUE);
            store.count().onsuccess = (e) => {
                event.ports[0].postMessage({ count: e.target.result });
            };
        });
    }
});

// Push notification handler (for future use)
self.addEventListener('push', (event) => {
    console.log('[Service Worker] Push received:', event);
    const data = event.data ? event.data.json() : {};

    const options = {
        body: data.body || 'New notification from Sweet Tooth POS',
        icon: '/images/icon-192x192.png',
        badge: '/images/icon-72x72.png',
        vibrate: [200, 100, 200],
        data: data
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Sweet Tooth POS', options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    console.log('[Service Worker] Notification clicked:', event);
    event.notification.close();

    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});

console.log('[Service Worker] Loaded');
