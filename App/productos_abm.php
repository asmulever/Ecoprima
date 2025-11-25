<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once __DIR__ . "/config/db.php";

$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("
    SELECT p.id, p.nombre, p.precio, p.ubicacion,
           (SELECT id FROM producto_imagenes WHERE producto_id=p.id ORDER BY orden ASC LIMIT 1) AS img_id
    FROM productos p
    WHERE p.usuario_id=?
    ORDER BY p.fecha_creacion DESC
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
            <img src="imagen.php?id=<?= $row['img_id'] ?>" class="thumb">
          <?php else: ?>
            <img src="/htdocs/images/placeholders/product_placeholder.png" class="thumb" alt="Sin imagen">
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['nombre']) ?></td>
        <td>$<?= number_format($row['precio'],2) ?></td>
        <td><?= htmlspecialchars($row['ubicacion']) ?></td>
        <td>
          <a href="producto_editar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
          <form method="POST" action="producto_borrar.php" class="d-inline" onsubmit="return confirm('¿Seguro que deseas borrar este producto?');">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
