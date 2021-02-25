<?php

$jwt = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
echo base64url_encode($jwt) . "<hr />";

$data = json_encode(['name' => 'Mahdi Hasanpour']);
echo base64url_encode($data) . "<hr />";

$public_key = $private_key = "";
if (file_exists('keys/private') && file_exists('keys/public')) {
    $public_key = file_get_contents('keys/public');
    $private_key = file_get_contents('keys/private');
} else {
    $config = [
        'config' => 'openssl.cnf',
        //'digest_alg' => 'RSA-SHA256'
    ];
    $pk = openssl_pkey_new($config);

    $d = openssl_pkey_get_details($pk);
    $public_key = $d['key'];
    openssl_pkey_export($pk, $private_key, null, $config);
    file_put_contents('keys/public',$public_key);
    file_put_contents('keys/private',$private_key);
}


echo "private:<br />" . $private_key . "<hr />";
echo "public:<br />" . $public_key . "<hr />";


$jd = base64url_encode($jwt) . '.' . base64url_encode($data);
openssl_sign(base64url_encode($jwt) . '.' . base64url_encode($data), $auth, $private_key, 'RSA-SHA256');
echo base64url_encode($auth) . "<hr />";

echo base64url_encode($jwt) . '.' . base64url_encode($data) . '.' . base64url_encode($auth);

/**
 * Encode data to Base64URL
 * @param string $data
 * @return boolean|string
 */
function base64url_encode($data)
{
    // First of all you should encode $data to Base64 string
    $b64 = base64_encode($data);

    // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
    if ($b64 === false) {
        return false;
    }

    // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
    $url = strtr($b64, '+/', '-_');

    // Remove padding character from the end of line and return the Base64URL result
    return rtrim($url, '=');
}

/**
 * Decode data from Base64URL
 * @param string $data
 * @param boolean $strict
 * @return boolean|string
 */
function base64url_decode($data, $strict = false)
{
    // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
    $b64 = strtr($data, '-_', '+/');

    // Decode Base64 string and return the original data
    return base64_decode($b64, $strict);
}