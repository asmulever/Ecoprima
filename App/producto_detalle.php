<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once __DIR__ . "/config/db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $mysqli->prepare("
  SELECT p.id, p.nombre, p.precio, p.ubicacion, u.email
  FROM productos p
  JOIN usuarios u ON u.id = p.usuario_id
  WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

// Traer imágenes
$imgStmt = $mysqli->prepare("SELECT id FROM producto_imagenes WHERE producto_id=? ORDER BY orden ASC");
$imgStmt->bind_param("i", $id);
$imgStmt->execute();
$imagenes = $imgStmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($producto['nombre'] ?? 'Producto no encontrado') ?> - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("toolbar.php"); ?>

<div class="container py-4">
  <?php if ($producto): ?>
    <div class="row">
      <div class="col-md-6">
        <?php if ($imagenes->num_rows > 0): ?>
          <?php $i=0; ?>
          <div id="carouselProd" class="carousel slide">
            <div class="carousel-inner">
              <?php while ($img=$imagenes->fetch_assoc()): ?>
                <div class="carousel-item <?= $i==0 ? 'active':'' ?>">
                  <img src="imagen.php?id=<?= $img['id'] ?>" class="d-block w-100" style="max-height:400px;object-fit:contain;">
                </div>
                <?php $i++; ?>
              <?php endwhile; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselProd" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselProd" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>
        <?php else: ?>
          <img src="/htdocs/images/placeholders/product_placeholder.png" class="img-fluid" alt="Sin imagen">
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
        <p><strong>Precio:</strong> $<?= number_format($producto['precio'],2) ?></p>
        <p><strong>Ubicación:</strong> <?= htmlspecialchars($producto['ubicacion']) ?></p>
        <p><strong>Contacto:</strong> <?= htmlspecialchars($producto['email']) ?></p>
        <a href="marketplace.php" class="btn btn-secondary">← Volver</a>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-danger">Producto no encontrado.</div>
    <a href="marketplace.php" class="btn btn-secondary">← Volver</a>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
