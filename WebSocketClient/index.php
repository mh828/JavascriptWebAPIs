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

<input type="file" onchange="changedFile(this.files)"/>

<script>
    const ws = new WebSocket('ws://127.0.0.1:1223');
    ws.addEventListener('open', function () {
        console.log('server opened');

        const message = "some message to server adf asf asf af af asdsa dfsad fsdf sdf sfsaf sad sd sd sdf sd sad sf" +
            "sd fsd sdf sdf sdf sadf saf" +
            " sdfsdf safs affafad fasfd" + "\n End of Message";
        console.log(message.length)
        ws.send(message);

    })
    ws.addEventListener('message', function (e) {
        console.log(e.data);
    })

    function changedFile(files) {
        if (files.length === 0)
            return;

        const fd = new FileReader();
        fd.addEventListener('load', () => {
            console.log(fd.result.length)
            ws.send(fd.result);
        })
        fd.readAsText(files[0]);
    }
</script>
</body>
</html>