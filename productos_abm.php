<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include("config/db.php");

$user_id = (int)$_SESSION['user_id'];
$stmt = $mysqli->prepare("
    SELECT
        p.id,
        p.nombre,
        p.precio,
        p.ubicacion,
        ranked_pi.id as img_id
    FROM
        productos p
    LEFT JOIN
        (
            SELECT
                id,
                producto_id,
                ROW_NUMBER() OVER (PARTITION BY producto_id ORDER BY orden ASC, id ASC) as rn
            FROM
                producto_imagenes
        ) as ranked_pi ON p.id = ranked_pi.producto_id AND ranked_pi.rn = 1
    WHERE
        p.usuario_id = ?
    ORDER BY
        p.fecha_creacion DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Productos - EcoPrima</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>.thumb{width:80px;height:80px;object-fit:cover;}</style>
</head>
<body class="bg-light">

<?php include("toolbar.php"); ?>

<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h2>Mis Productos</h2>
    <a href="producto_nuevo.php" class="btn btn-success">➕ Agregar Producto</a>
  </div>
  <table class="table table-bordered bg-white shadow-sm">
    <thead class="table-success"><tr><th>Imagen</th><th>Nombre</th><th>Precio</th><th>Ubicación</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php while($row=$result->fetch_assoc()): ?>
      <tr>
        <td>
          <?php if($row['img_id']): ?>
            <img src="imagen.php?id=<?= (int)$row['img_id'] ?>" class="thumb">
          <?php else: ?>
            <img src="assets/img/placeholder.png" class="thumb">
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
        <td>$<?= htmlspecialchars(number_format((float)$row['precio'], 2), ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($row['ubicacion'], ENT_QUOTES, 'UTF-8') ?></td>
        <td>
          <a href="producto_editar.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
          <a href="producto_borrar.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('¿Seguro que deseas borrar este producto?')">Borrar</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>