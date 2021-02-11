<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>


<script>
    if ('serviceWorker' in navigator) {
        window.navigator.serviceWorker.register('/serviceWorker.js').then(function (r) {


        });

        navigator.serviceWorker.addEventListener('message', function (message) {
            console.log(message.data)
        })


        navigator.serviceWorker.ready.then(r => {
            console.log(r)
            setInterval(function () {
                r.active.postMessage('my message' + Date.now());
            }, 1000)
        });
    }
</script>
</body>
</html>