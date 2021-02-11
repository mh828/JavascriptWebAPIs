self.addEventListener('install', (e) => {

});
self.addEventListener('fetch', (event) => {
    /*event.respondWith(
        new Response(event.request.url + ' value 2')
    );
*/
    if (event.clientId) {
        self.clients.get(event.clientId).then(r => {
            r.postMessage("Hi I'm A message from service worker")
        })
    }
});

self.addEventListener('message', function (message) {
    console.log(message.data)
    message.source.postMessage("I recive your message " + message.data)
});