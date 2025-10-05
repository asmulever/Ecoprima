<?php
session_start();
include("config/db.php");

$error = "";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password !== $password2) {
        $error = "Las contraseñas no coinciden.";
    } else {
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body class="bg-register">

<div class="auth-wrapper">
  <div class="auth-box">
    <div class="logo">
      <span class="leaf">📝</span> Crear Cuenta
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (!empty($msg)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
      <div class="text-center mt-3">
        <a href="index.php" class="btn btn-secondary">Volver al Login</a>
      </div>
    <?php else: ?>
      <form method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password2" class="form-label">Repetir contraseña</label>
          <input type="password" id="password2" name="password2" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
      </form>

      <div class="footer-links">
        <a href="index.php">¿Ya tienes una cuenta? Inicia sesión</a>
      </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>