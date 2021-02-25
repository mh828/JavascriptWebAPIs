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

    self.clients.matchAll().then(r => {
        r.forEach((cl) => {
            if (cl.type === 'window') {
                //cl.postMessage(textData)
                cl.postMessage('message notification received')
            }
        })
    })
    console.log(event, 'message received');
    const textData = event.data.text();
    self.registration.showNotification("Notification received : \t" + textData);
});