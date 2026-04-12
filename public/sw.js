const CACHE_NAME = 'sitara-pwa-v1'
const OFFLINE_URL = './offline.html'
const FILES_TO_CACHE = [
    OFFLINE_URL
]

self.addEventListener('install', event => {
    console.log('[Service Worker] Sedang Menginstal...')
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then(cache => {
                console.log('[Service Worker] Menyimpan Cache Offline...')
                return cache.addAll(FILES_TO_CACHE)
            })
            .catch(error =>
                console.error(
                    '[Service Worker] Gagal menyimpan cache awal:',
                    error
                )
            )
    )
    self.skipWaiting()
})

self.addEventListener('activate', event => {
    console.log('[Service Worker] Diaktifkan.')
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) {
                        console.log(
                            '[Service Worker] Menghapus cache lama:',
                            key
                        )
                        return caches.delete(key)
                    }
                })
            )
        )
    )
    event.waitUntil(self.clients.claim())
})

self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting()
    }
})

self.addEventListener('fetch', event => {
    const request = event.request
    if (
        request.method !== 'GET' ||
        request.url.includes('/broadcasting/') ||
        request.url.includes('ws://') ||
        request.url.includes('wss://')
    ) {
        return
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => {
                console.log(
                    '[Service Worker] Internet terputus. Menampilkan halaman offline.'
                )
                return caches.match(OFFLINE_URL)
            })
        )
        return
    }

    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font'
    ) {
        event.respondWith(
            caches.match(request).then(cachedResponse => {
                return (
                    cachedResponse ||
                    fetch(request).then(networkResponse => {
                        if (
                            !networkResponse ||
                            networkResponse.status !== 200 ||
                            networkResponse.type !== 'basic'
                        ) {
                            return networkResponse
                        }

                        return caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, networkResponse.clone())
                            return networkResponse
                        })
                    })
                )
            })
        )
        return
    }

    event.respondWith(
        fetch(request)
            .then(networkResponse => {
                if (networkResponse && networkResponse.status === 200) {
                    const responseToCache = networkResponse.clone()
                    caches.open(CACHE_NAME).then(cache => {
                        if (
                            !request.url.includes('/api/') &&
                            !request.url.includes('?_token')
                        ) {
                            cache.put(request, responseToCache)
                        }
                    })
                }
                return networkResponse
            })
            .catch(async () => {
                console.log(
                    '[Service Worker] Gagal mengambil data, mencari di cache fallback...'
                )
                return caches.match(request)
            })
    )
})

self.addEventListener('sync', event => {
    if (event.tag === 'laravel-pwa-sync') {
        event.waitUntil(syncRequests())
    }
})

async function syncRequests () {
    try {
        const db = await openDB()
        const tx = db.transaction('offline-requests', 'readonly')
        const store = tx.objectStore('offline-requests')
        const requests = await getAllRequests(store)

        for (const req of requests) {
            try {
                const response = await fetch(req.url, {
                    method: req.method,
                    headers: req.headers,
                    body: req.body
                })

                if (response.ok) {
                    const deleteTx = db.transaction(
                        'offline-requests',
                        'readwrite'
                    )
                    deleteTx.objectStore('offline-requests').delete(req.id)
                    console.log(
                        '[Service Worker] Background Sync berhasil untuk:',
                        req.url
                    )
                }
            } catch (err) {
                console.error(
                    '[Service Worker] Background Sync gagal untuk:',
                    req.url,
                    err
                )
            }
        }
    } catch (e) {
        console.error('[Service Worker] Gagal membuka IndexedDB untuk Sync:', e)
    }
}

function openDB () {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('laravel-pwa-sync', 1)
        request.onsuccess = () => resolve(request.result)
        request.onerror = () => reject(request.error)
        request.onupgradeneeded = event => {
            const db = event.target.result
            if (!db.objectStoreNames.contains('offline-requests')) {
                db.createObjectStore('offline-requests', {
                    keyPath: 'id',
                    autoIncrement: true
                })
            }
        }
    })
}

function getAllRequests (store) {
    return new Promise((resolve, reject) => {
        const request = store.getAll()
        request.onsuccess = () => resolve(request.result)
        request.onerror = () => reject(request.error)
    })
}
