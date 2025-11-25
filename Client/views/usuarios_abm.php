<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

require_once dirname(__DIR__, 2) . "/App/config/db.php";

$authStmt = $mysqli->prepare("SELECT rol FROM usuarios WHERE id = ? LIMIT 1");
$authStmt->bind_param("i", $_SESSION['user_id']);
$authStmt->execute();
$authResult = $authStmt->get_result()->fetch_assoc();

if (!$authResult || $authResult['rol'] !== 'admin') {
    http_response_code(403);
    die("Acceso denegado");
}

$errors = [];
$success = "";

function normalizeEstado(string $estado): string {
    $allowed = ['activo','pendiente','inactivo'];
    return in_array($estado, $allowed, true) ? $estado : 'activo';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $empresa = trim($_POST['empresa'] ?? '');
        $rol = $_POST['rol'] === 'admin' ? 'admin' : 'usuario';
        $estado = normalizeEstado($_POST['estado'] ?? 'activo');
        $password = trim($_POST['password'] ?? '');

        if (!$email) {
            $errors[] = "El email es obligatorio y debe ser válido.";
        }
        if ($password === '') {
            $errors[] = "La contraseña no puede estar vacía.";
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO usuarios (email, password_hash, empresa, rol, estado) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $email, $hash, $empresa, $rol, $estado);
            if ($stmt->execute()) {
                $success = "Usuario creado correctamente.";
            } else {
                $errors[] = "Error al crear usuario: " . $mysqli->error;
            }
        }
    } elseif ($action === 'update') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $empresa = trim($_POST['empresa'] ?? '');
        $rol = $_POST['rol'] === 'admin' ? 'admin' : 'usuario';
        $estado = normalizeEstado($_POST['estado'] ?? 'activo');
        $password = trim($_POST['password'] ?? '');

        if ($userId <= 0) {
            $errors[] = "ID de usuario inválido.";
        }
        if (!$email) {
            $errors[] = "El email es obligatorio y debe ser válido.";
        }

        if (empty($errors)) {
            $sql = "UPDATE usuarios SET email=?, empresa=?, rol=?, estado=?";
            $types = "ssss";
            $params = [$email, $empresa, $rol, $estado];

            if ($password !== '') {
                $sql .= ", password_hash=?";
                $types .= "s";
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id=?";
            $types .= "i";
            $params[] = $userId;

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $success = "Usuario actualizado correctamente.";
            } else {
                $errors[] = "Error al actualizar usuario: " . $mysqli->error;
            }
        }
    } elseif ($action === 'deactivate') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            $errors[] = "ID de usuario inválido.";
        }
        if (empty($errors)) {
            $stmt = $mysqli->prepare("UPDATE usuarios SET estado='inactivo' WHERE id=?");
            $stmt->bind_param("i", $userId);
            if ($stmt->execute()) {
                $success = "El usuario fue marcado como inactivo.";
            } else {
                $errors[] = "No se pudo desactivar el usuario.";
            }
        }
    }
}

$activeUsers = $mysqli->query("SELECT id, email, empresa, rol, estado, fecha_creacion FROM usuarios WHERE estado='activo' ORDER BY fecha_creacion DESC");

$editUser = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    if ($editId > 0) {
        $stmt = $mysqli->prepare("SELECT id, email, empresa, rol, estado FROM usuarios WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $editUser = $stmt->get_result()->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include __DIR__ . "/toolbar.php"; ?>

<main class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1">Usuarios activos</h1>
      <p class="text-muted mb-0">Solo los usuarios con estado activo aparecen en este listado.</p>
    </div>
    <a href="app.php?route=usuarios_abm" class="btn btn-outline-secondary">Recargar</a>
  </div>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Email</th>
                  <th>Empresa</th>
                  <th>Rol</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($user = $activeUsers->fetch_assoc()): ?>
                  <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['empresa'] ?? '-') ?></td>
                    <td><span class="badge bg-<?= $user['rol']==='admin'?'primary':'secondary' ?>"><?= htmlspecialchars($user['rol']) ?></span></td>
                    <td><span class="badge bg-success"><?= htmlspecialchars($user['estado']) ?></span></td>
                    <td class="d-flex gap-2">
                      <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                      <form method="POST" onsubmit="return confirm('¿Marcar como inactivo este usuario?');">
                        <input type="hidden" name="action" value="deactivate">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Desactivar</button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title">Crear usuario</h5>
          <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="mb-2">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Contraseña</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Empresa</label>
              <input type="text" name="empresa" class="form-control">
            </div>
            <div class="mb-2">
              <label class="form-label">Rol</label>
              <select name="rol" class="form-select">
                <option value="usuario">Usuario</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select">
                <option value="activo" selected>Activo</option>
                <option value="pendiente">Pendiente</option>
                <option value="inactivo">Inactivo</option>
              </select>
            </div>
            <button type="submit" class="btn btn-success w-100">Crear</button>
          </form>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Editar usuario</h5>
          <?php if ($editUser): ?>
            <form method="POST">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">
              <div class="mb-2">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($editUser['email']) ?>" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Empresa</label>
                <input type="text" name="empresa" class="form-control" value="<?= htmlspecialchars($editUser['empresa'] ?? '') ?>">
              </div>
              <div class="mb-2">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select">
                  <option value="usuario" <?= $editUser['rol']==='usuario'?'selected':'' ?>>Usuario</option>
                  <option value="admin" <?= $editUser['rol']==='admin'?'selected':'' ?>>Admin</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                  <option value="activo" <?= $editUser['estado']==='activo'?'selected':'' ?>>Activo</option>
                  <option value="pendiente" <?= $editUser['estado']==='pendiente'?'selected':'' ?>>Pendiente</option>
                  <option value="inactivo" <?= $editUser['estado']==='inactivo'?'selected':'' ?>>Inactivo</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Nueva contraseña (opcional)</label>
                <input type="password" name="password" class="form-control" placeholder="Dejar vacío para conservar">
              </div>
              <button type="submit" class="btn btn-primary w-100">Actualizar</button>
            </form>
          <?php else: ?>
            <p class="text-muted mb-0">Selecciona “Editar” en la tabla para cargar un usuario.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-4">
    <a href="dashboard.php" class="btn btn-outline-secondary">← Volver al dashboard</a>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
