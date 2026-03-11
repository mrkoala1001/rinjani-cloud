const CACHE_NAME = 'hotpot-v1';
const urlsToCache = [
    '/'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    // Hanya intercept navigasi atau aset statis jika diperlukan
    // Untuk Laravel, kita biasanya biarkan network yang utama
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});
