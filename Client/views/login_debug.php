<?php
require_once dirname(__DIR__, 2) . "/App/config/db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $mysqli->prepare("SELECT id, password_hash, estado, rol FROM usuarios WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['estado'] !== 'activo') {
            $error = "Tu cuenta no está activa. Revisa tu correo para activarla.";
        } elseif (password_verify($password, $row['password_hash'])) {
            // Guardar sesión
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email']   = $email;
            $_SESSION['rol']     = $row['rol'];

            // En debug mostramos todo en lugar de redirigir
            echo "<h2>✅ Sesión iniciada correctamente</h2>";
            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";
            exit();
        } else {
            $error = "Clave incorrecta.";
        }
    } else {
        $error = "El usuario no existe.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Debug - EcoMarket</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">

  <div class="card p-4 shadow" style="max-width: 400px; width: 100%;">
    <h3 class="text-center mb-3">Login Debug</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label>Email</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <button type="submit" class="btn btn-success w-100">Entrar</button>
    </form>
  </div>

</body>
</html>
