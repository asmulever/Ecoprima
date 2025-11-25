<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Infrastructure\Config\Config;
use mysqli;
use RuntimeException;

class ConnectionFactory
{
    private static ?mysqli $connection = null;

    public static function get(): mysqli
    {
        if (self::$connection instanceof mysqli) {
            return self::$connection;
        }

        $host = Config::get('DB_HOST');
        $user = Config::get('DB_USERNAME');
        $pass = Config::get('DB_PASSWORD');
        $db = Config::get('DB_DATABASE');
        $port = (int) (Config::get('DB_PORT') ?? 3306);

        if (!$host || !$user || !$db) {
            throw new RuntimeException('Faltan variables de base de datos en .env');
        }

        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        $mysqli = new mysqli($host, $user, $pass, $db, $port);

        if ($mysqli->connect_errno) {
            throw new RuntimeException(
                sprintf('Error de conexión MySQL (%s): %s', $mysqli->connect_errno, $mysqli->connect_error)
            );
        }

        if (!$mysqli->set_charset('utf8mb4')) {
            throw new RuntimeException('No se pudo configurar UTF-8: ' . $mysqli->error);
        }

        return self::$connection = $mysqli;
    }

    public static function reset(): void
    {
        if (self::$connection instanceof mysqli) {
            self::$connection->close();
        }
        self::$connection = null;
    }
}
