<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/App/bootstrap.php';

$route = $_GET['route'] ?? '';
$route = trim($route);
$route = $route === '' ? 'dashboard' : str_replace('-', '_', strtolower($route));

$routes = [
    'dashboard'        => 'dashboard.php',
    'marketplace'      => 'marketplace.php',
    'productos_abm'    => 'productos_abm.php',
    'producto_nuevo'   => 'producto_nuevo.php',
    'producto_editar'  => 'producto_editar.php',
    'producto_detalle' => 'producto_detalle.php',
    'producto_borrar'  => 'producto_borrar.php',
    'usuarios_abm'     => 'usuarios_abm.php',
    'vendidos'         => 'vendidos.php',
    'empresa_form'     => 'empresa_form.php',
    'guardar_empresa'  => 'guardar_empresa.php',
    'stats'            => 'stats.php',
    'logout'           => 'logout.php',
    'login_debug'      => 'login_debug.php',
    'registro'         => 'registro.php',
    'reset'            => 'reset.php',
    'activar'          => 'activar.php',
    'imagen'           => 'imagen.php',
];

$publicRoutes = ['login_debug', 'registro', 'reset', 'activar', 'imagen'];

if (!isset($routes[$route])) {
    http_response_code(404);
    echo 'Ruta no encontrada';
    exit;
}

if (!in_array($route, $publicRoutes, true) && empty($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

render_view($routes[$route]);

function render_view(string $view): void
{
    $basePath = __DIR__ . '/Client/views/';
    $file = $basePath . $view;

    if (!is_file($file)) {
        http_response_code(500);
        echo "Vista no encontrada: {$view}";
        return;
    }

    require $file;
}
