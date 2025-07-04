<?php
// SecurityLayer.php

function encryptToken($data) {
    $key = "ITBienvenu123@"; // Must be exactly 16 chars (128-bit)
    $cipher = "AES-128-ECB";
    return bin2hex(openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA));
}

function decryptToken($hex) {
    $key = "ITBienvenu123@";
    if (!ctype_xdigit($hex) || strlen($hex) % 2 !== 0) {
        return false;
    }
    $cipher = "AES-128-ECB";
    $raw = hex2bin($hex);
    $decrypted = openssl_decrypt($raw, $cipher, $key, OPENSSL_RAW_DATA);
    return $decrypted ?: false;
}
