<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$user_email = $_SESSION['email'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - EcoPrima</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="/Client/assets/css/theme.css">
  <style>
      .dashboard-card {
          transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
      }
      .dashboard-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 8px 20px rgba(0,0,0,0.12);
      }
      .dashboard-card .card-body {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          text-align: center;
      }
      .dashboard-card .icon {
          font-size: 3rem;
          margin-bottom: 1rem;
      }
  </style>
</head>
<body>

  <?php include 'toolbar.php'; ?>

  <main class="container py-5">
    <header class="mb-5 text-center">
      <h1>Bienvenido a EcoPrima</h1>
      <p class="lead text-muted">Gestiona tus productos y explora el marketplace de economía circular.</p>
    </header>

    <div class="row g-4 justify-content-center">

      <div class="col-md-6 col-lg-4">
        <div class="card dashboard-card h-100">
          <div class="card-body">
            <div class="icon text-success"><i class="fas fa-store"></i></div>
            <h5 class="card-title">Marketplace</h5>
            <p class="card-text">Explora productos y materiales disponibles para reutilizar.</p>
            <a href="marketplace.php" class="btn btn-primary mt-auto">Explorar Mercado</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card dashboard-card h-100">
          <div class="card-body">
            <div class="icon text-primary"><i class="fas fa-boxes-stacked"></i></div>
            <h5 class="card-title">Mis Productos</h5>
            <p class="card-text">Administra tus publicaciones, precios y stock.</p>
            <a href="productos_abm.php" class="btn btn-primary mt-auto">Gestionar Productos</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card dashboard-card h-100">
          <div class="card-body">
            <div class="icon text-info"><i class="fas fa-building-user"></i></div>
            <h5 class="card-title">Mi Empresa</h5>
            <p class="card-text">Actualiza los datos de tu perfil y empresa.</p>
            <a href="empresa_form.php" class="btn btn-primary mt-auto">Ver Perfil</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card dashboard-card h-100">
          <div class="card-body">
            <div class="icon text-warning"><i class="fas fa-chart-pie"></i></div>
            <h5 class="card-title">Estadísticas</h5>
            <p class="card-text">Consulta reportes y gráficos de tu actividad comercial.</p>
            <a href="stats.php" class="btn btn-secondary mt-auto">Ver Estadísticas</a>
          </div>
        </div>
      </div>

    </div>
  </main>

</body>
</html>
