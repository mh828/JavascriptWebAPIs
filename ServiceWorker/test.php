<?php
if (file_exists('endpoint.json')) {
    //header('Content-Type: application/json; charset=utf-8');
    $content = json_decode(file_get_contents('endpoint.json'));


    //$salt = openssl_random_pseudo_bytes(16);
    $config = array(
        "config" => __DIR__ . '/openssl.cnf',
        "digest_alg" => "SHA256"
    );
    $salt = openssl_random_pseudo_bytes(16);

    $privateKey = openssl_pkey_new($config);
    $details = openssl_pkey_get_details($privateKey);
    $pk = '';
    openssl_pkey_export($privateKey, $pk, null, $config);
    $publicKey = $details['key'];

    $value = '';
    openssl_private_encrypt("data to encrypt", $value, $privateKey);
    echo base64_encode($value);

    echo "<br /> <br /> <hr />";
    openssl_public_decrypt($value, $value, $publicKey);
    echo($value);

}