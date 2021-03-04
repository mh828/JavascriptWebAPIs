const configuration = {
    iceServers: [
        {urls: 'stun:stun.l.google.com:19302'}
    ]
};


const server = new RTCPeerConnection(configuration);
const client = new RTCPeerConnection(configuration);


server.createOffer().then(serverOffer => {
    return server.setLocalDescription(serverOffer);
}).then(() => {
    //send for remote client
    client.setRemoteDescription(server.localDescription).then(() => {
        client.createAnswer().then(clientAnswer => {
            client.setLocalDescription(clientAnswer).then(() => {
                server.setRemoteDescription(client.localDescription).then(() => {

                })
            });

        })
    })


})

server.addEventListener('icecandidate', e => {
    console.log(e)
})

client.addEventListener('icecandidate', e => {
    console.log(e)
})

server.addEventListener('connectionstatechange', e => {
    if (client.connectionState === 'connected')
        console.log('connected')
})

client.addEventListener('connectionstatechange', e => {
    if (client.connectionState === 'connected')
        console.log('connected')
})