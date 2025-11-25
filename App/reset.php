<?php
// Debug activable (comentar en producción)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once __DIR__ . "/config/db.php";

$error = "";
$msg = "";

// --- Solicitud de reseteo por email ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email=? AND estado='activo'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $token = bin2hex(random_bytes(16));
        $update = $mysqli->prepare("UPDATE usuarios SET token=? WHERE id=?");
        $update->bind_param("si", $token, $row['id']);
        $update->execute();

        $link = "https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/reset.php?token=".$token;
        $subject = "Recupera tu clave - EcoPrima Marketplace";
        $message = "Hola,\n\nRecibimos tu solicitud de cambio de clave.\nAccedé al siguiente enlace para establecer una nueva:\n$link\n\nSi no pediste este cambio, ignorá este mensaje.";
        $headers = "From: no-reply@ecoprima.com";

        if (mail($email, $subject, $message, $headers)) {
            $msg = "Te enviamos un enlace a tu correo para resetear la clave.";
        } else {
            $error = "No se pudo enviar el correo. Contactá con soporte.";
        }
    } else {
        $error = "El email no está registrado o no está activo.";
    }
}

// --- Reset con token ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['newpass']) && isset($_GET['token'])) {
    $token = $_GET['token'];
    $newpass = password_hash($_POST['newpass'], PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE token=? AND estado='activo'");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $update = $mysqli->prepare("UPDATE usuarios SET password_hash=?, token=NULL WHERE id=?");
        $update->bind_param("si", $newpass, $row['id']);
        $update->execute();
        $msg = "Tu clave fue actualizada. Ya podés iniciar sesión.";
    } else {
        $error = "Token inválido o vencido.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reset de Contraseña - EcoPrima</title>
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
  <h3 class="text-center mb-4">🔑 Recuperar Contraseña</h3>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= $msg ?></div>
  <?php endif; ?>

  <?php if (isset($_GET['token']) && empty($msg)): ?>
    <!-- Formulario nueva clave -->
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Nueva contraseña</label>
        <input type="password" name="newpass" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-success w-100">Actualizar clave</button>
    </form>
  <?php elseif (empty($msg)): ?>
    <!-- Formulario solicitud de email -->
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Tu email registrado</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
    </form>
  <?php endif; ?>

  <div class="text-center mt-3">
    <a href="index.php">← Volver al login</a>
  </div>
</div>

</body>
</html>
