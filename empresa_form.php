<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include("config/db.php");

$user_id = (int)$_SESSION['user_id'];

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Buscar si ya existe empresa para este usuario
$stmt = $mysqli->prepare("SELECT * FROM empresas WHERE usuario_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();
$stmt->close();

// Procesar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Error de validación CSRF.');
    }

    $nombre     = $_POST['nombre'] ?? '';
    $cuit       = $_POST['cuit'] ?? '';
    $email      = $_POST['email'] ?? '';
    $telefono   = $_POST['telefono'] ?? '';
    $direccion  = $_POST['direccion'] ?? '';
    $rubro      = $_POST['rubro'] ?? '';
    $descripcion= $_POST['descripcion'] ?? '';
    $sitio_web  = $_POST['sitio_web'] ?? '';
    $cuenta_bancaria = $_POST['cuenta_bancaria'] ?? '';

    $logo_data = null;
    $logo_tipo = null;
    $is_logo_uploaded = isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK;

    if ($empresa) {
        // UPDATE
        if ($is_logo_uploaded) {
            $logo_data = file_get_contents($_FILES['logo']['tmp_name']);
            $logo_tipo = $_FILES['logo']['type'];
            $stmt = $mysqli->prepare("UPDATE empresas SET nombre=?, cuit=?, email=?, telefono=?, direccion=?, rubro=?, descripcion=?, sitio_web=?, cuenta_bancaria=?, logo=?, logo_tipo=? WHERE usuario_id=?");
            $stmt->bind_param("sssssssssssi", $nombre, $cuit, $email, $telefono, $direccion, $rubro, $descripcion, $sitio_web, $cuenta_bancaria, $logo_data, $logo_tipo, $user_id);
        } else {
            $stmt = $mysqli->prepare("UPDATE empresas SET nombre=?, cuit=?, email=?, telefono=?, direccion=?, rubro=?, descripcion=?, sitio_web=?, cuenta_bancaria=? WHERE usuario_id=?");
            $stmt->bind_param("sssssssssi", $nombre, $cuit, $email, $telefono, $direccion, $rubro, $descripcion, $sitio_web, $cuenta_bancaria, $user_id);
        }
    } else {
        // INSERT
        if ($is_logo_uploaded) {
            $logo_data = file_get_contents($_FILES['logo']['tmp_name']);
            $logo_tipo = $_FILES['logo']['type'];
        }
        $stmt = $mysqli->prepare("INSERT INTO empresas (usuario_id, nombre, cuit, email, telefono, direccion, rubro, descripcion, sitio_web, cuenta_bancaria, logo, logo_tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssss", $user_id, $nombre, $cuit, $email, $telefono, $direccion, $rubro, $descripcion, $sitio_web, $cuenta_bancaria, $logo_data, $logo_tipo);
    }

    if ($stmt->execute()) {
        header("Location: dashboard.php?status=success");
    } else {
        error_log("Error guardando datos de empresa: " . $stmt->error);
        header("Location: empresa_form.php?status=error");
    }
    $stmt->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Datos de Empresa - EcoPrima</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

  <?php include 'toolbar.php'; ?>

  <div class="container py-5">
    <h2 class="mb-4 text-center">Datos de tu Empresa</h2>

    <div class="card shadow-sm col-md-8 mx-auto">
      <div class="card-body">
        <form action="empresa_form.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

          <div class="mb-3">
            <label class="form-label">Nombre de la empresa</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($empresa['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">CUIT</label>
              <input type="text" name="cuit" class="form-control" value="<?= htmlspecialchars($empresa['cuit'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email de Contacto</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($empresa['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
          </div>

          <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($empresa['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($empresa['direccion'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Rubro</label>
            <input type="text" name="rubro" class="form-control" value="<?= htmlspecialchars($empresa['rubro'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Descripción de la Empresa</label>
            <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($empresa['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Sitio Web</label>
            <input type="url" name="sitio_web" class="form-control" placeholder="https://www.ejemplo.com" value="<?= htmlspecialchars($empresa['sitio_web'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Datos Bancarios (CBU/Alias)</label>
            <input type="text" name="cuenta_bancaria" class="form-control" value="<?= htmlspecialchars($empresa['cuenta_bancaria'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Logo</label>
            <input type="file" name="logo" class="form-control" accept="image/png, image/jpeg, image/gif">
            <?php if (!empty($empresa['logo']) && !empty($empresa['logo_tipo'])): ?>
              <div class="mt-2">
                <p class="mb-1">Logo actual:</p>
                <img src="data:<?= htmlspecialchars($empresa['logo_tipo'], ENT_QUOTES, 'UTF-8') ?>;base64,<?= base64_encode($empresa['logo']) ?>" alt="Logo actual" style="max-height:80px; border-radius: 4px;">
              </div>
            <?php endif; ?>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-success">Guardar Datos</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>