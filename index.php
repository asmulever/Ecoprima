<?php
// Debug activable (comentar en producción)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();
include("config/db.php");

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
            header("Location: dashboard.php");
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
  <title>Login - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-bg">

<div class="auth-box">
  <h3 class="text-center mb-4">🌱 EcoPrima Marketplace</h3>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Correo electrónico</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success w-100">Ingresar</button>
  </form>

  <div class="text-center mt-3">
    <a href="registro.php" class="btn btn-link">Registrarse</a><br>
    <a href="reset.php">¿Olvidaste tu clave?</a>
  </div>
</div>

</body>
</html>
