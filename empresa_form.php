<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include("config/db.php");

$user_id = $_SESSION['user_id'];

// Buscar si ya existe empresa para este usuario
$stmt = $mysqli->prepare("SELECT * FROM empresas WHERE usuario_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();
$stmt->close();

// Procesar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre     = $mysqli->real_escape_string($_POST['nombre']);
    $cuit       = $mysqli->real_escape_string($_POST['cuit']);
    $email      = $mysqli->real_escape_string($_POST['email']);
    $telefono   = $mysqli->real_escape_string($_POST['telefono']);
    $direccion  = $mysqli->real_escape_string($_POST['direccion']);
    $rubro      = $mysqli->real_escape_string($_POST['rubro']);
    $descripcion= $mysqli->real_escape_string($_POST['descripcion']);
    $sitio_web  = $mysqli->real_escape_string($_POST['sitio_web']);
    $cuenta_bancaria = $mysqli->real_escape_string($_POST['cuenta_bancaria']);

    // Logo como BLOB
    $logo_data = null;
    $logo_tipo = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo_data = $mysqli->real_escape_string(file_get_contents($_FILES['logo']['tmp_name']));
        $logo_tipo = $mysqli->real_escape_string($_FILES['logo']['type']);
    }

    if ($empresa) {
        // UPDATE existente
        if ($logo_data) {
            $sql = "UPDATE empresas SET nombre='$nombre', cuit='$cuit', email='$email',
                    telefono='$telefono', direccion='$direccion', rubro='$rubro',
                    descripcion='$descripcion', sitio_web='$sitio_web', cuenta_bancaria='$cuenta_bancaria',
                    logo='$logo_data', logo_tipo='$logo_tipo'
                    WHERE usuario_id='$user_id'";
        } else {
            $sql = "UPDATE empresas SET nombre='$nombre', cuit='$cuit', email='$email',
                    telefono='$telefono', direccion='$direccion', rubro='$rubro',
                    descripcion='$descripcion', sitio_web='$sitio_web', cuenta_bancaria='$cuenta_bancaria'
                    WHERE usuario_id='$user_id'";
        }
    } else {
        // INSERT nuevo
        $sql = "INSERT INTO empresas
        (usuario_id, nombre, cuit, email, telefono, direccion, rubro, descripcion, sitio_web, cuenta_bancaria, logo, logo_tipo)
        VALUES ('$user_id','$nombre','$cuit','$email','$telefono','$direccion','$rubro','$descripcion',
        '$sitio_web','$cuenta_bancaria','$logo_data','$logo_tipo')";
    }

    if ($mysqli->query($sql) === TRUE) {
        echo "<script>alert('Datos de empresa guardados correctamente'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        echo "Error: " . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Datos de Empresa - EcoMarket</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light">

  <!-- Navbar igual a dashboard -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="dashboard.php">EcoMarket</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="productos_abm.php">Mis Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Salir</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenido -->
  <div class="container py-5">
    <h2 class="mb-4 text-center">Datos de tu Empresa</h2>

    <div class="card shadow">
      <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">

          <div class="mb-3">
            <label class="form-label">Nombre de la empresa</label>
            <input type="text" name="nombre" class="form-control"
                   value="<?= htmlspecialchars($empresa['nombre'] ?? '') ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">CUIT</label>
            <input type="text" name="cuit" class="form-control"
                   value="<?= htmlspecialchars($empresa['cuit'] ?? '') ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($empresa['email'] ?? '') ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control"
                   value="<?= htmlspecialchars($empresa['telefono'] ?? '') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control"
                   value="<?= htmlspecialchars($empresa['direccion'] ?? '') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Rubro</label>
            <input type="text" name="rubro" class="form-control"
                   value="<?= htmlspecialchars($empresa['rubro'] ?? '') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control"><?= htmlspecialchars($empresa['descripcion'] ?? '') ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Sitio Web</label>
            <input type="url" name="sitio_web" class="form-control"
                   value="<?= htmlspecialchars($empresa['sitio_web'] ?? '') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Cuenta Bancaria / CBU / IBAN</label>
            <input type="text" name="cuenta_bancaria" class="form-control"
                   value="<?= htmlspecialchars($empresa['cuenta_bancaria'] ?? '') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Logo</label>
            <input type="file" name="logo" class="form-control">
            <?php if (!empty($empresa['logo_tipo'])): ?>
              <div class="mt-2">
                <img src="data:<?= $empresa['logo_tipo'] ?>;base64,<?= base64_encode($empresa['logo']) ?>"
                     alt="Logo actual" style="max-height:80px;">
              </div>
            <?php endif; ?>
          </div>

          <button type="submit" class="btn btn-success">Guardar Datos</button>
          <a href="dashboard.php" class="btn btn-secondary">Volver</a>

        </form>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
