<?php

$config = array(
    "digest_alg" => "sha512",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);
/*
$config = array(
    "digest_alg" => "sha512",
    "curve_name" => prime256v1,
    "private_key_type" => OPENSSL_KEYTYPE_EC,
);*/

// Create the keypair
$res = openssl_pkey_new($config);

// Get private key
openssl_pkey_export($res, $privkey, getenv("LITBEE_KEY_PWD"));

// Get public key
$pubkey = openssl_pkey_get_details($res);
$pubkey = $pubkey["key"];
var_dump($privkey);
var_dump($pubkey);
?>