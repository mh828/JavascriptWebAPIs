<?php
if (file_exists('endpoint.json')) {
    //header('Content-Type: application/json; charset=utf-8');
    $content = json_decode(file_get_contents('endpoint.json'));


    //$salt = openssl_random_pseudo_bytes(16);
    $config = array(
        "config" => __DIR__ . '/openssl.cnf'
    );
    $privateKey = openssl_pkey_new($config);
    $details = openssl_pkey_get_details($privateKey);
    $pk = '';
    openssl_pkey_export($privateKey, $pk, null, $config);
    var_dump($pk);
    $publicKey = $details['key'];
}