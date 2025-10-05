<?php
// Función para cargar variables de entorno desde un archivo .env
function load_env($path)
{
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"");
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Cargar el archivo .env desde el directorio raíz
load_env(__DIR__ . '/../.env');

// Configuración de conexión usando getenv
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'test';

// Mostrar errores en desarrollo (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión
$mysqli = new mysqli($host, $user, $pass, $db);

// Verificar errores de conexión
if ($mysqli->connect_errno) {
    // En un entorno de producción, esto debería registrarse en lugar de mostrarse
    error_log("Error de conexión MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
    die("Error de conexión a la base de datos. Por favor, intente más tarde.");
}

// Forzar charset a UTF-8
if (!$mysqli->set_charset("utf8mb4")) {
    error_log("Error configurando charset: " . $mysqli->error);
    die("Error de configuración de la base de datos.");
}
?>