self.addEventListener('install', (e) => {

});
self.addEventListener('fetch', (event) => {
    event.respondWith(
        new Response(event.request.url + ' value 2')
    );

    console.log(event)
});