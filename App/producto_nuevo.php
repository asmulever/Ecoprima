<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once __DIR__ . "/config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $ubicacion = $_POST['ubicacion'];
    $usuario_id = $_SESSION['user_id'];

    $stmt = $mysqli->prepare("INSERT INTO productos (usuario_id, nombre, precio, ubicacion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $usuario_id, $nombre, $precio, $ubicacion);
    $stmt->execute();
    $producto_id = $stmt->insert_id;

    // Insertar imágenes en la DB
    if (!empty($_FILES['imagenes']['name'][0])) {
        $total = count($_FILES['imagenes']['name']);
        for ($i=0; $i<$total; $i++) {
            $tmp = $_FILES['imagenes']['tmp_name'][$i];
            if (is_uploaded_file($tmp)) {
                $imgData = file_get_contents($tmp);
                $mime = mime_content_type($tmp);
                $orden = $i+1;

                $imgStmt = $mysqli->prepare("INSERT INTO producto_imagenes (producto_id, imagen, mime_type, orden) VALUES (?, ?, ?, ?)");
                $null = NULL;
                $imgStmt->bind_param("ibsi", $producto_id, $null, $mime, $orden);
                $imgStmt->send_long_data(1, $imgData);
                $imgStmt->execute();
            }
        }
    }

    header("Location: productos_abm.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo Producto</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2>Nuevo Producto</h2>
  <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3"><label class="form-label">Nombre</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3"><label class="form-label">Precio</label>
      <input type="number" step="0.01" name="precio" class="form-control" required>
    </div>
    <div class="mb-3"><label class="form-label">Ubicación</label>
      <input type="text" name="ubicacion" class="form-control" required>
    </div>
    <div class="mb-3"><label class="form-label">Imágenes (máx. 3)</label>
      <input type="file" name="imagenes[]" class="form-control" multiple accept="image/*">
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="productos_abm.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
