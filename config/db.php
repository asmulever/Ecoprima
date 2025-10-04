<?php
// Configuración de conexión
$host = "sql208.infinityfree.com";
$user = "if0_39913066";
$pass = "Asmulever25";
$db   = "if0_39913066_ecoprima_marketplace";

// Mostrar errores en desarrollo (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión
$mysqli = new mysqli($host, $user, $pass, $db);

// Verificar errores de conexión
if ($mysqli->connect_errno) {
    die("Error de conexión MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

// Forzar charset a UTF-8
if (!$mysqli->set_charset("utf8mb4")) {
    die("Error configurando charset: " . $mysqli->error);
}
?>
