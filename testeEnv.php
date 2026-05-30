<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo $_ENV['EMAIL_HOST'];
echo "<br>";
echo $_ENV['EMAIL_USER'];
echo "<br>";
echo $_ENV['EMAIL_PASS'];