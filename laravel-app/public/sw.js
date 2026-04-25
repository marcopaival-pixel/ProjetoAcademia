const CACHE_NAME = 'nexshape-v3';
const ASSETS_TO_CACHE = [
    '/css/app.css',
    '/css/modern-layout.css',
    '/js/app.js',
    '/js/sidebar-toggle.js',
    '/images/logo_Academia.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const req = event.request;

    // Apenas interceptar solicitações GET. 
    // POST, PUT, DELETE (como formulários de login) devem ir diretamente para a rede.
    if (req.method !== 'GET') {
        return;
    }

    // Navegações de página (HTML/Laravel): 
    // Deixamos o navegador gerenciar diretamente para evitar problemas com redirecionamentos, auth e CRSF.
    if (req.mode === 'navigate') {
        return;
    }

    // Para outros recursos (CSS, JS, Imagens): tenta rede, se falhar, tenta cache.
    event.respondWith(
        fetch(req).catch(async () => {
            const cached = await caches.match(req);
            if (cached) {
                return cached;
            }
            
            // Fallback amigável em vez de erro de rede puro
            return new Response('Sem conexão e recurso não encontrado no cache.', {
                status: 503,
                statusText: 'Service Unavailable',
                headers: { 'Content-Type': 'text/plain; charset=utf-8' },
            });
        })
    );
});

