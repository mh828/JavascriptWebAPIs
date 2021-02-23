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
    $publicKey = $details['key'];


    $pk = 'BH-03vhKF0zOCDogSUlIh_egzwRTdbybp3aqIqNKlQKFV-q8f5bCCecXvEzsDrDKeI1akgUY_84lPckYB8iazwQ';
    var_dump(openssl_dh_compute_key(base64_decode($pk),$privateKey));

}