<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<script>
    const ws = new WebSocket('ws://127.0.0.1:1223');
    ws.addEventListener('open', function () {
        console.log('server opened');
        setInterval(() => {
            console.log(ws.send("some message to server"));
        }, 1000)

    })
    ws.addEventListener('message', function (e) {
        console.log(e.data);
    })
</script>
</body>
</html>