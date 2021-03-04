function makeConnection() {
    const configuration = {
        iceServers: [
            {
                urls: 'stun:127.0.0.1:3478'
            }
        ]
    };


    const server = new RTCPeerConnection(configuration);
    const client = new RTCPeerConnection(configuration);

    const channel = server.createDataChannel('chat');

    server.addEventListener('icecandidate', e => {
        client.addIceCandidate(e.candidate).then(r => {
            console.log(r)
        })
    })

    client.addEventListener('icecandidate', e => {
        server.addIceCandidate(e.candidate).then(r => {
            console.log(r)
        })
    })

    server.addEventListener('connectionstatechange', e => {
        if (client.connectionState === 'connected')
            console.log('connected')
    })

    client.addEventListener('connectionstatechange', e => {
        if (client.connectionState === 'connected')
            console.log('connected')
    })

    server.createOffer().then(serverOffer => server.setLocalDescription(serverOffer))
        .then(() => client.setRemoteDescription(server.localDescription))
        .then(() => client.createAnswer())
        .then(answer => client.setLocalDescription(answer))
        .then(() => server.setRemoteDescription(client.localDescription))
        .then(() => {
            console.log('all down', server.iceConnectionState, client.iceConnectionState)
        })
        .catch(() => {
            console.error('crashed')
        })


}