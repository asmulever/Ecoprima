<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Estadísticas - EcoMarket</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="dashboard.php">EcoMarket</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="productos_abm.php">Mis Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
          <li class="nav-item"><a class="nav-link active" href="stats.php">Estadísticas</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Salir</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <h2 class="mb-4 text-center">Estadísticas</h2>
    
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card shadow p-3">
          <h5 class="card-title">Ventas por Mes</h5>
          <canvas id="chartVentas"></canvas>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow p-3">
          <h5 class="card-title">Productos más vendidos</h5>
          <canvas id="chartProductos"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Demo: Ventas por mes
    new Chart(document.getElementById("chartVentas"), {
      type: "line",
      data: {
        labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun"],
        datasets: [{
          label: "Ventas",
          data: [5, 9, 7, 12, 15, 10],
          borderColor: "green",
          backgroundColor: "rgba(0,128,0,0.2)",
          fill: true
        }]
      }
    });

    // Demo: Productos más vendidos
    new Chart(document.getElementById("chartProductos"), {
      type: "doughnut",
      data: {
        labels: ["Plástico", "Vidrio", "Metal", "Papel"],
        datasets: [{
          data: [12, 8, 5, 10],
          backgroundColor: ["#28a745", "#17a2b8", "#ffc107", "#dc3545"]
        }]
      }
    });
  </script>

</body>
</html>
