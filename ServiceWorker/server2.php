<?php


if (file_exists('endpoint.json')) {
    header('Content-Type: application/json; charset=utf-8');
    $content = json_decode(file_get_contents('endpoint.json'));


    $key = $content->keys->p256dh;
    $cipher_algo = "aes-128-gcm";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_algo));
    $tag = "";
    $val = openssl_encrypt("some data must be enctrypted", $cipher_algo, $key, 0, $iv, $tag, "tag");

    $dval = (openssl_decrypt($val, $cipher_algo, $key, 0, $iv, $tag, 'tag'));


    $c = curl_init($content->endpoint);
    curl_setopt_array($c, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => 1,
        CURLOPT_HTTPHEADER => [
            'TTL: 60',
            'Content-Encoding: aes128gcm',
            'Content-Length: ' . strlen($val)
        ],
        CURLOPT_POSTFIELDS => $val
    ]);

    $result = curl_exec($c);
    $info = curl_getinfo($c);
    curl_close($c);

    $info["result"] = $result;

    echo json_encode($info);
}