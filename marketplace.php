<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include("config/db.php");

// Updated query to fetch only active products and their first image
$stmt = $mysqli->prepare("
    SELECT
        p.id, p.nombre, p.precio, p.ubicacion, u.email, p.estado,
        (SELECT id FROM producto_imagenes WHERE producto_id = p.id ORDER BY orden ASC LIMIT 1) AS img_id
    FROM productos p
    JOIN usuarios u ON u.id = p.usuario_id
    WHERE p.estado = 'activo'
    ORDER BY p.fecha_creacion DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Marketplace - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/theme.css">
  <style>
    .product-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 0;
        border-radius: var(--border-radius);
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--box-shadow);
    }
    .product-img {
        height: 200px;
        object-fit: cover;
        border-top-left-radius: var(--border-radius);
        border-top-right-radius: var(--border-radius);
    }
  </style>
</head>
<body class="bg-marketplace">

<?php include("toolbar.php"); ?>

<div class="container py-5">
  <header class="mb-5 text-center text-white" style="position: relative; z-index: 2;">
      <h1 class="display-4">Marketplace</h1>
      <p class="lead">Encuentra materias primas secundarias y dale una nueva vida a los recursos.</p>
  </header>

  <?php if (isset($_GET['status'])): ?>
    <div class="alert alert-<?= $_GET['status'] == 'success' ? 'success' : 'danger' ?> mx-auto col-md-8" style="position: relative; z-index: 2;">
      <?= htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>

  <div class="row g-4" style="position: relative; z-index: 2;">
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card product-card h-100">
          <?php if ($row['img_id']): ?>
            <img src="imagen.php?id=<?= (int)$row['img_id'] ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?>">
          <?php else: ?>
            <img src="assets/img/placeholder.png" class="card-img-top product-img" alt="Producto sin imagen">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></h5>
            <p class="card-text text-muted flex-grow-1">
              <i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($row['ubicacion'], ENT_QUOTES, 'UTF-8') ?><br>
              <i class="fas fa-tag me-2"></i><strong>$<?= htmlspecialchars(number_format((float)$row['precio'], 2), ENT_QUOTES, 'UTF-8') ?></strong>
            </p>
            <div class="mt-auto">
              <a href="producto_detalle.php?id=<?= (int)$row['id'] ?>" class="btn btn-secondary w-100 mb-2">Ver Detalles</a>
              <a href="generar_orden.php?id=<?= (int)$row['id'] ?>" class="btn btn-primary w-100">
                <i class="fas fa-shopping-cart me-2"></i>Generar Orden de Compra
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
    <?php if($result->num_rows === 0): ?>
        <div class="col-12">
            <div class="alert alert-info text-center">No hay productos disponibles en este momento.</div>
        </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>