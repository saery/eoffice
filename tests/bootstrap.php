<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    /** @psalm-suppress MissingFile */
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    /** @psalm-suppress MissingFile */
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}