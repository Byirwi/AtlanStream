// Service worker pour améliorer les performances mobiles

const CACHE_NAME = 'atlanstream-cache-v1';
const ASSETS_TO_CACHE = [
  '/assets/css/style.css',
  '/assets/css/mobile.css',
  '/assets/js/theme.js',
  '/assets/js/mobile-menu.js',
  '/public/images/default.jpg'
];

// Installation du service worker
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        return cache.addAll(ASSETS_TO_CACHE);
      })
  );
});

// Activation et nettoyage des caches obsolètes
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    })
  );
});

// Stratégie de cache : network first, puis cache
self.addEventListener('fetch', (event) => {
  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // Mise en cache des ressources statiques
        if (event.request.method === 'GET' && 
            (event.request.url.includes('/assets/') || 
             event.request.url.includes('/public/'))) {
          const responseClone = response.clone();
          caches.open(CACHE_NAME)
            .then((cache) => {
              cache.put(event.request, responseClone);
            });
        }
        return response;
      })
      .catch(() => {
        // En cas d'échec, on essaie de récupérer depuis le cache
        return caches.match(event.request);
      })
  );
});
