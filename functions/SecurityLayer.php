<?php
// SecurityLayer.php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));

$dotenv -> load();

function encryptToken($data) {
    $key = $_ENV['SECRETE_KEY']; // Must be exactly 16 chars (128-bit)
    $cipher = $_ENV['SECRETE_CIPHER'];
    return bin2hex(openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA));
}

function decryptToken($hex) {
    $key = $_ENV['SECRETE_KEY'];
    if (!ctype_xdigit($hex) || strlen($hex) % 2 !== 0) {
        return false;
    }
    $cipher = $_ENV['SECRETE_CIPHER'];
    $raw = hex2bin($hex);
    $decrypted = openssl_decrypt($raw, $cipher, $key, OPENSSL_RAW_DATA);
    return $decrypted ?: false;
}
