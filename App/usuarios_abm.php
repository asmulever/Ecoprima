<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once __DIR__ . "/config/db.php";

$authStmt = $mysqli->prepare("SELECT rol FROM usuarios WHERE id = ?");
$authStmt->bind_param("i", $_SESSION['user_id']);
$authStmt->execute();
$authResult = $authStmt->get_result()->fetch_assoc();

if (!$authResult || $authResult['rol'] !== 'admin') {
    http_response_code(403);
    die("Acceso denegado");
}

$result = $mysqli->query("SELECT * FROM usuarios ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h1 class="text-center mb-4">Gestión de Usuarios</h1>

  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Email</th>
          <th>Empresa</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['empresa']) ?></td>
            <td><?= htmlspecialchars($row['rol']) ?></td>
            <td>
              <span class="badge bg-<?= $row['estado']=='activo'?'success':'danger' ?>">
                <?= htmlspecialchars($row['estado']) ?>
              </span>
            </td>
            <td>
              <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
              <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Eliminar</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-4 text-center">
    <a href="dashboard.php" class="btn btn-secondary">⬅ Volver al Dashboard</a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
