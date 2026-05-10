const CACHE_NAME = 'nexshape-patient-v1';
const OFFLINE_URL = '/offline.html';

const ASSETS_TO_CACHE = [
    '/',
    '/offline.html',
    '/images/logo.webp'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // Tentamos adicionar cada asset individualmente para que falha em um 
            // não bloqueie o cache dos outros (importante para assets do Vite)
            return Promise.allSettled(
                ASSETS_TO_CACHE.map(url => cache.add(url))
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    // Ignorar requisições de onboarding para evitar conflitos com o wizard
    if (event.request.url.includes('/onboarding-premium')) {
        return;
    }

    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => {
                return caches.match(OFFLINE_URL);
            })
        );
    } else {
        event.respondWith(
            caches.match(event.request).then((response) => {
                return response || fetch(event.request).catch(() => {
                    // Retorna um erro amigável em vez de quebrar a promessa
                    return new Response('Network error occurred', {
                        status: 408,
                        statusText: 'Network error occurred'
                    });
                });
            })
        );
    }
});
