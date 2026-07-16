const CACHE = 'nitido-v1';
const APP_SHELL = ['/offline.html', '/icons/icon-192.png', '/icons/icon-512.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(APP_SHELL))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((key) => key !== CACHE).map((key) => caches.delete(key))
        ))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    // Navegación (páginas HTML): red primero, y si falla, offline.html.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
        return;
    }

    // Estáticos (build de Vite, iconos): cache primero, red de refresco.
    const url = new URL(request.url);
    if (url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/')) {
        event.respondWith(
            caches.match(request).then((cached) => {
                const fetchPromise = fetch(request).then((response) => {
                    caches.open(CACHE).then((cache) => cache.put(request, response.clone()));
                    return response;
                }).catch(() => cached);

                return cached || fetchPromise;
            })
        );
    }
});
