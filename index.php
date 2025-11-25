<?php
session_start();
require_once __DIR__ . "/App/config/db.php";

$error = "";

// Procesar login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, password_hash FROM usuarios WHERE email=? AND estado='activo'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $email;
            header("Location: App/dashboard.php");
            exit;
        } else {
            $error = "Clave incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado o inactivo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/Client/assets/css/theme.css">
</head>
<body>

<div class="auth-wrapper">
  <div class="auth-box">
    <div class="logo">
      <span class="leaf">🌱</span> EcoPrima
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" id="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>

    <div class="footer-links">
      <a href="App/registro.php">Registrarse</a>
      <span class="separator">|</span>
      <a href="App/reset.php">¿Olvidaste tu clave?</a>
    </div>
  </div>
</div>

</body>
</html>
