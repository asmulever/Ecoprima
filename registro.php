<?php
// Debug activable
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();
include("config/db.php");

$error = "";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Error de validación CSRF.');
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password !== $password2) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si ya existe
        $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "El correo ya está registrado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(16));

            $insert = $mysqli->prepare("INSERT INTO usuarios (email, password_hash, estado, token) VALUES (?, ?, 'pendiente', ?)");
            $insert->bind_param("sss", $email, $hash, $token);

            if ($insert->execute()) {
                $link = "https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/activar.php?token=".$token;
                $subject = "Confirma tu cuenta - EcoPrima Marketplace";
                $message = "Bienvenido a EcoPrima!\n\nConfirmá tu cuenta haciendo clic en el siguiente enlace:\n$link\n\nSi no solicitaste este registro, ignorá este correo.";
                $headers = "From: no-reply@ecoprima.com";

                if (mail($email, $subject, $message, $headers)) {
                    $msg = "Te enviamos un correo para activar tu cuenta.";
                } else {
                    $error = "No se pudo enviar el correo de activación.";
                }
            } else {
                $error = "Error al registrar el usuario.";
            }
        }
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .auth-box {
      width: 100%;
      max-width: 400px;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<div class="auth-box">
  <h3 class="text-center mb-4">📝 Crear Cuenta</h3>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= $msg ?></div>
  <?php endif; ?>

  <?php if (empty($msg)): ?>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
      <div class="mb-3">
        <label class="form-label">Correo electrónico</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Repetir contraseña</label>
        <input type="password" name="password2" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Registrarse</button>
    </form>
  <?php endif; ?>

  <div class="text-center mt-3">
    <a href="index.php">← Volver al login</a>
  </div>
</div>

</body>
</html>
