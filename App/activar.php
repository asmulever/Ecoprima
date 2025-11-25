<?php
require_once __DIR__ . "/../config/db.php";

// Mostrar errores en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("❌ Error: no se recibió un token válido.");
}

$token = $_GET['token'];

// Buscar usuario con ese token
$stmt = $mysqli->prepare("SELECT id, estado FROM usuarios WHERE token = ?");
if (!$stmt) {
    die("Error en prepare: " . $mysqli->error);
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Token inválido o ya utilizado.");
}

$row = $result->fetch_assoc();

// Si ya está activo
if ($row['estado'] === 'activo') {
    echo "✅ Tu cuenta ya estaba activa.";
    exit;
}

// Activar usuario
$update = $mysqli->prepare("UPDATE usuarios SET estado='activo', token=NULL WHERE id=?");
if (!$update) {
    die("Error en prepare UPDATE: " . $mysqli->error);
}
$update->bind_param("i", $row['id']);

if ($update->execute()) {
    echo "🎉 Cuenta activada con éxito. Ya podés <a href='index.php'>iniciar sesión</a>.";
} else {
    echo "❌ Error al activar: " . $update->error;
}
?>
