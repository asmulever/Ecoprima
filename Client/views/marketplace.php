<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: /index.php"); exit(); }
require_once dirname(__DIR__, 2) . "/App/config/db.php";

$stmt = $mysqli->prepare("
    SELECT p.id, p.nombre, p.precio, p.ubicacion, u.email,
           (SELECT id FROM producto_imagenes WHERE producto_id=p.id ORDER BY orden ASC LIMIT 1) AS img_id
    FROM productos p
    JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY p.fecha_creacion DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Marketplace - EcoPrima</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    .product-card { min-height: 420px; }
    .product-img { height: 180px; object-fit: cover; }
  </style>
</head>
<body class="bg-light">

<?php include __DIR__ . '/toolbar.php'; ?>

<div class="container py-4">
  <h2 class="mb-4">🌱 Marketplace de Subproductos</h2>

  <div class="row g-4">
    <?php while($row=$result->fetch_assoc()): ?>
      <div class="col-md-4">
        <div class="card shadow-sm product-card">
          <?php if ($row['img_id']): ?>
            <img src="imagen.php?id=<?= $row['img_id'] ?>" class="card-img-top product-img">
          <?php else: ?>
            <img src="/Client/images/placeholders/product_placeholder.png" class="card-img-top product-img" alt="Sin imagen">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['nombre']) ?></h5>
            <p class="card-text">
              <strong>Precio:</strong> $<?= number_format($row['precio'],2) ?><br>
              <strong>Ubicación:</strong> <?= htmlspecialchars($row['ubicacion']) ?><br>
              <strong>Contacto:</strong> <?= htmlspecialchars($row['email']) ?>
            </p>
            <a href="producto_detalle.php?id=<?= $row['id'] ?>" class="btn btn-success w-100">Ver Detalles</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

</body>
</html>
