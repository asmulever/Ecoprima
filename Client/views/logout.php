<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cerrando Sesión - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <meta http-equiv="refresh" content="3;url=/index.php">
  <style>
    body {
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .logout-box {
      width: 100%;
      max-width: 400px;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
  </style>
</head>
<body>

<div class="logout-box">
  <h3 class="mb-3">👋 Sesión cerrada</h3>
  <p>Serás redirigido al login en <strong>3 segundos</strong>...</p>
</div>

</body>
</html>
