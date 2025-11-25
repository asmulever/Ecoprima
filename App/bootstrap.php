<?php

declare(strict_types=1);

define('APP_BASE_PATH', __DIR__);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = APP_BASE_PATH . '/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Infrastructure\Config\Config;

Config::load(dirname(APP_BASE_PATH) . '/.env');
