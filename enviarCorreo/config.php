<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar .env desde la raíz
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

return [
    'smtp_host' => $_ENV['SMTP_HOST'],
    'smtp_port' => $_ENV['SMTP_PORT'],
    'smtp_user' => $_ENV['SMTP_USER'],
    'smtp_pass' => $_ENV['SMTP_PASS'],
];
