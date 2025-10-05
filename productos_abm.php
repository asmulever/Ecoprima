<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
include("config/db.php");

$user_id = (int)$_SESSION['user_id'];

// Manejar acciones de cambio de estado o eliminación
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $producto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $action = $_GET['action'];

    // Verificar que el producto pertenece al usuario
    $verify_stmt = $mysqli->prepare("SELECT id FROM productos WHERE id = ? AND usuario_id = ?");
    $verify_stmt->bind_param("ii", $producto_id, $user_id);
    $verify_stmt->execute();
    $result = $verify_stmt->get_result();
    if ($result->num_rows === 1) {
        if ($action === 'deshabilitar' || $action === 'reactivar') {
            $new_estado = $action === 'reactivar' ? 'activo' : 'deshabilitado';
            $update_stmt = $mysqli->prepare("UPDATE productos SET estado = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_estado, $producto_id);
            $update_stmt->execute();
            $update_stmt->close();
        } elseif ($action === 'eliminar') {
            $delete_stmt = $mysqli->prepare("DELETE FROM productos WHERE id = ?");
            $delete_stmt->bind_param("i", $producto_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    }
    $verify_stmt->close();
    header("Location: productos_abm.php"); // Redirigir para limpiar URL
    exit();
}


$stmt = $mysqli->prepare("
    SELECT
        p.id, p.nombre, p.precio, p.ubicacion, p.estado,
        (SELECT id FROM producto_imagenes WHERE producto_id = p.id ORDER BY orden ASC LIMIT 1) AS img_id
    FROM productos p
    WHERE p.usuario_id = ?
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Productos - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/theme.css">
  <style>.thumb{width:80px;height:80px;object-fit:cover; border-radius: var(--border-radius);}</style>
</head>
<body>

<?php include("toolbar.php"); ?>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Mis Productos</h1>
    <a href="producto_nuevo.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Agregar Producto</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
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
            <td>
                <?php
                    $estado = htmlspecialchars($row['estado'], ENT_QUOTES, 'UTF-8');
                    $badge_class = 'bg-secondary';
                    if ($estado === 'activo') $badge_class = 'bg-success';
                    if ($estado === 'pendiente') $badge_class = 'bg-warning text-dark';
                    if ($estado === 'deshabilitado') $badge_class = 'bg-danger';
                ?>
                <span class="badge <?= $badge_class ?>"><?= ucfirst($estado) ?></span>
            </td>
            <td class="text-end">
              <?php if ($row['estado'] === 'activo'): ?>
                <a href="producto_editar.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Editar</a>
                <a href="?action=deshabilitar&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('¿Seguro que deseas deshabilitar este producto?')"><i class="fas fa-eye-slash"></i> Deshabilitar</a>
              <?php else: ?>
                <a href="?action=reactivar&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i> Reactivar</a>
                <a href="?action=eliminar&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Esta acción es permanente. ¿Seguro que deseas eliminar este producto?')"><i class="fas fa-trash"></i> Eliminar</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php if($result->num_rows === 0): ?>
            <tr>
                <td colspan="5" class="text-center text-muted">No tienes productos publicados. <a href="producto_nuevo.php">¡Agrega el primero!</a></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>