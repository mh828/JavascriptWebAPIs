<?php
include 'functions.php';

if (file_exists('endpoint.json')) {
    //header('Content-Type: application/json; charset=utf-8');
    $content = json_decode(file_get_contents('endpoint.json'));


    $key = $content->keys->auth;
    $cipher_algo = "aes-128-gcm";
    $payload = "some data must send to client";
    $sk = \functions\getKeys();
    openssl_private_encrypt($payload, $dataEcrypted, $sk->private_key);

    echo  base64_decode($content->keys->p256dh);
    exit();
    $jk = \functions\base64url_encode(openssl_random_pseudo_bytes(12));
    $jwt = \functions\generateJWT(
        json_encode(['alg' => 'HS256', 'typ' => 'JWT']),
        json_encode([
            'aud' => 'http://localhost:8083',
            'exp' => time() + (12 * 60 * 60),
            'sub' => "mailto:poiesh@ymail.com"
        ]), $jk);


    $c = curl_init($content->endpoint);
    curl_setopt_array($c, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => 1,
        CURLOPT_HTTPHEADER => [
            'TTL: 60',
            'Conent-Encoding: aes128gcm',
            'Content-Length: ' . strlen($dataEcrypted),
            "Authorization: vapid t={$jwt},k=$jk"
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