self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then(function(response) {
            if (event.request.url.includes('libs/jquery')) {
                const url = event.request.url.replace('libs/jquery', 'themes/flatkit/libs/jquery');
                event.request.url = url;
                return fetch(url)
            }
            return response || fetch(event.request);
        })
    );
});
