self.addEventListener('install', (e) => {

});
self.addEventListener('fetch', (event) => {
    /*event.respondWith(
        new Response(event.request.url + ' value 2')
    );
*/
    if (event.clientId) {
        self.clients.get(event.clientId).then(r => {
            r.postMessage(event.request.url)
        })
    }
});

self.addEventListener('message', function (message) {
    console.log(message.data)
    message.source.postMessage("I recive your message " + message.data)
});

self.addEventListener('push', function (event) {

    const textData = event.data.text();
    self.clients.matchAll().then(r => {
        r.forEach((cl) => {
            cl.postMessage(textData)
        })
    })
    self.registration.showNotification(textData);
});