<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">
      <span style="color: var(--primary-color);">🌱</span> EcoPrima
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="main-nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="marketplace.php">Marketplace</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="productos_abm.php">Mis Productos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="empresa_form.php">Mi Empresa</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="stats.php">Estadísticas</a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <?php $isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'; ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo htmlspecialchars($_SESSION['email'] ?? 'Usuario', ENT_QUOTES, 'UTF-8'); ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <?php if ($isAdmin): ?>
            <li><a class="dropdown-item" href="usuarios_abm.php">Mi Perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <?php endif; ?>
            <li><a class="dropdown-item" href="dashboard.php">Inicio</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Salir</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
