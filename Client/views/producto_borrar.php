<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

require_once dirname(__DIR__, 2) . "/App/config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: productos_abm.php");
    exit();
}

$producto_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($producto_id <= 0) {
    header("Location: productos_abm.php?status=invalid");
    exit();
}

$usuario_id = $_SESSION['user_id'];
$mysqli->begin_transaction();

$checkStmt = $mysqli->prepare("SELECT id FROM productos WHERE id = ? AND usuario_id = ?");
$checkStmt->bind_param("ii", $producto_id, $usuario_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $mysqli->rollback();
    header("Location: productos_abm.php?status=notfound");
    exit();
}

$delImgs = $mysqli->prepare("DELETE FROM producto_imagenes WHERE producto_id = ?");
$delImgs->bind_param("i", $producto_id);
$delImgs->execute();

$delProd = $mysqli->prepare("DELETE FROM productos WHERE id = ? AND usuario_id = ?");
$delProd->bind_param("ii", $producto_id, $usuario_id);

if ($delProd->execute()) {
    $mysqli->commit();
    header("Location: productos_abm.php?status=deleted");
    exit();
}

$mysqli->rollback();
header("Location: productos_abm.php?status=error");
exit();
