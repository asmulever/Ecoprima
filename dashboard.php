<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - EcoMarket</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="dashboard.php">EcoMarket</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="productos_abm.php">Mis Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Salir</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenido -->
  <div class="container py-5">
    <h2 class="mb-4 text-center">Panel de Control</h2>

    <div class="row g-4">

      <!-- Mercado -->
      <div class="col-md-6 col-lg-3">
        <div class="card text-center shadow h-100">
          <div class="card-body">
            <i class="fas fa-store fa-3x text-success mb-3"></i>
            <h5 class="card-title">Mercado</h5>
            <p class="card-text">Explora productos disponibles en el marketplace.</p>
            <a href="marketplace.php" class="btn btn-success">Entrar</a>
          </div>
        </div>
      </div>

      <!-- Mis Ventas -->
      <div class="col-md-6 col-lg-3">
        <div class="card text-center shadow h-100">
          <div class="card-body">
            <i class="fas fa-boxes fa-3x text-primary mb-3"></i>
            <h5 class="card-title">Mis Ventas</h5>
            <p class="card-text">Gestiona tus productos publicados y nuevas ventas.</p>
            <a href="productos_abm.php" class="btn btn-primary">Entrar</a>
          </div>
        </div>
      </div>

      <!-- Estadísticas -->
      <div class="col-md-6 col-lg-3">
        <div class="card text-center shadow h-100">
          <div class="card-body">
            <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
            <h5 class="card-title">Estadísticas</h5>
            <p class="card-text">Consulta reportes y gráficos de tu actividad.</p>
            <a href="stats.php" class="btn btn-warning">Ver</a>
          </div>
        </div>
      </div>

      <!-- Productos Vendidos -->
      <div class="col-md-6 col-lg-3">
        <div class="card text-center shadow h-100">
          <div class="card-body">
            <i class="fas fa-handshake fa-3x text-danger mb-3"></i>
            <h5 class="card-title">Productos Vendidos</h5>
            <p class="card-text">Accede al historial de ventas y contactos de compradores.</p>
            <a href="vendidos.php" class="btn btn-danger">Ver</a>
          </div>
        </div>
      </div>

      <!-- NUEVA TARJETA: Datos de Empresa/Usuario -->
      <div class="col-md-6 col-lg-3">
        <div class="card text-center shadow h-100 border-success">
          <div class="card-body">
            <i class="fas fa-building fa-3x text-success mb-3"></i>
            <h5 class="card-title">Datos de Empresa</h5>
            <p class="card-text">Completa la información de tu empresa/usuario que falta en el perfil.</p>
            <a href="empresa_form.php" class="btn btn-outline-success">Completar</a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
