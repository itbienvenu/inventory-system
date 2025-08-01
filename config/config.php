<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));

$dotenv -> load();

$conn = mysqli_connect($_ENV['HOST_NAME'],$_ENV['USERNAME'],$_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
if (mysqli_connect_errno()) {
    printf("", mysqli_connect_error());
    exit(1);
}

?>