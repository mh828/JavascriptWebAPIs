<?php
include 'functions.php';

if (file_exists('endpoint.json')) {
    header('Content-Type: application/json; charset=utf-8');
    $content = json_decode(file_get_contents('endpoint.json'));


    $key = $content->keys->p256dh;
    $cipher_algo = "aes-128-gcm";
    $payload = "some data must send to client";
    $sk = \functions\getKeys();
    openssl_private_encrypt($payload, $dataEcrypted, $sk->private_key);


    $c = curl_init($content->endpoint);
    curl_setopt_array($c, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => 1,
        CURLOPT_HTTPHEADER => [
            'TTL: 60',
            'Content-Encoding: aes128gcm',
            'Content-Length: ' . strlen($dataEcrypted),
            //'Crypto-Key: ' . \functions\base64url_encode($sk->public_key)
        ],
        CURLOPT_POSTFIELDS => $dataEcrypted
    ]);

    $result = curl_exec($c);
    $info = curl_getinfo($c);
    curl_close($c);

    $info["result"] = $result;

    echo json_encode($info);
}