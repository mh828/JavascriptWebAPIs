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
            console.log(r);
            return navigator.serviceWorker.ready;

        });
    }
</script>
</body>
</html>