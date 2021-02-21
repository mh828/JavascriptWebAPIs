<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>

<input type="button" value="loadPushManager" onclick="loadPushManager()"/>

<script>
    if ('serviceWorker' in navigator) {
        window.navigator.serviceWorker.register('/serviceWorker.js').then(function (r) {


        });

        navigator.serviceWorker.addEventListener('message', function (message) {
            console.log(message.data)
        })


        navigator.serviceWorker.ready.then(r => {
            console.log(r)
            r.active.postMessage('my message' + Date.now());
            /*setInterval(function () {
                r.active.postMessage('my message' + Date.now());
            }, 1000)*/

            //
        });
    }


    function loadPushManager() {
        navigator.serviceWorker.getRegistration().then(r => {
            r.pushManager.subscribe({userVisibleOnly: true}).then(pr => {
                console.log(pr);
                console.log(pr.toJSON());
                const xs = new XMLHttpRequest();
                xs.open('post','/server.php');
                xs.send(JSON.stringify(pr.toJSON()));


                /*const x = new XMLHttpRequest();

                x.open('post', pr.endpoint);
                //x.setRequestHeader('Content-Type', 'application/json; charset=UTF-8')
                //x.setRequestHeader('Content-Encoding', 'aes128gcm')
                x.setRequestHeader('TTL', '60');
                x.setRequestHeader('Content-Length', '0');
                x.send();*/


            })
        })
    }
</script>
</body>
</html>