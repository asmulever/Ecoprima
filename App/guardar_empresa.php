<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: empresa_form.php");
    exit();
}

require_once __DIR__ . "/config/db.php";

$user_id    = $_SESSION['user_id'];
$nombre     = trim($_POST['nombre'] ?? '');
$cuit       = trim($_POST['cuit'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telefono   = trim($_POST['telefono'] ?? '');
$direccion  = trim($_POST['direccion'] ?? '');
$rubro      = trim($_POST['rubro'] ?? '');
$descripcion= trim($_POST['descripcion'] ?? '');
$sitio_web  = trim($_POST['sitio_web'] ?? '');
$cuenta_bancaria = trim($_POST['cuenta_bancaria'] ?? '');

$logo_data = null;
$logo_tipo = null;
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['logo']['tmp_name'];
    $logo_data = file_get_contents($tmpPath);
    $logo_tipo = mime_content_type($tmpPath) ?: $_FILES['logo']['type'];
}

$empresaStmt = $mysqli->prepare("SELECT id FROM empresas WHERE usuario_id = ?");
$empresaStmt->bind_param("i", $user_id);
$empresaStmt->execute();
$empresaExiste = $empresaStmt->get_result()->fetch_assoc();

if ($empresaExiste) {
    if ($logo_data !== null) {
        $sql = "UPDATE empresas SET nombre=?, cuit=?, email=?, telefono=?, direccion=?, rubro=?, descripcion=?, sitio_web=?, cuenta_bancaria=?, logo=?, logo_tipo=? WHERE usuario_id=?";
        $params = [$nombre, $cuit, $email, $telefono, $direccion, $rubro, $descripcion, $sitio_web, $cuenta_bancaria, $logo_data, $logo_tipo, $user_id];
    } else {
        $sql = "UPDATE empresas SET nombre=?, cuit=?, email=?, telefono=?, direccion=?, rubro=?, descripcion=?, sitio_web=?, cuenta_bancaria=? WHERE usuario_id=?";
        $params = [$nombre, $cuit, $email, $telefono, $direccion, $rubro, $descripcion, $sitio_web, $cuenta_bancaria, $user_id];
    }
    $types = str_repeat('s', count($params) - 1) . 'i';
} else {
    $sql = "INSERT INTO empresas (usuario_id, nombre, cuit, email, telefono, direccion, rubro, descripcion, sitio_web, cuenta_bancaria, logo, logo_tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [$user_id, $nombre, $cuit, $email, $telefono, $direccion, $rubro, $descripcion, $sitio_web, $cuenta_bancaria, $logo_data, $logo_tipo];
    $types = "i" . str_repeat('s', count($params) - 1);
}

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo "<script>alert('Datos de empresa guardados correctamente'); window.location.href='dashboard.php';</script>";
    exit();
}

echo "Error: " . $mysqli->error;
?>
