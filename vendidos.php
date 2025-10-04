<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Más adelante traer datos reales de ventas desde la DB
$ventas_demo = [
  ["id"=>1, "producto"=>"Plástico reciclado", "cantidad"=>"100 kg", "comprador"=>"Empresa A", "contacto"=>"contacto@empresaA.com"],
  ["id"=>2, "producto"=>"Vidrio molido", "cantidad"=>"50 kg", "comprador"=>"Industria B", "contacto"=>"ventas@industriaB.com"],
  ["id"=>3, "producto"=>"Residuos metálicos", "cantidad"=>"200 kg", "comprador"=>"Metalúrgica C", "contacto"=>"info@metalC.com"]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Productos Vendidos - EcoMarket</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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
          <li class="nav-item"><a class="nav-link" href="stats.php">Estadísticas</a></li>
          <li class="nav-item"><a class="nav-link active" href="vendidos.php">Vendidos</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Salir</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <h2 class="mb-4 text-center">Productos Vendidos</h2>

    <div class="card shadow p-3">
      <table id="tablaVendidos" class="table table-striped">
        <thead>
          <tr>
            <th>ID Venta</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Comprador</th>
            <th>Contacto</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($ventas_demo as $v): ?>
          <tr>
            <td><?= $v['id'] ?></td>
            <td><?= $v['producto'] ?></td>
            <td><?= $v['cantidad'] ?></td>
            <td><?= $v['comprador'] ?></td>
            <td><?= $v['contacto'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#tablaVendidos').DataTable();
    });
  </script>

</body>
</html>
