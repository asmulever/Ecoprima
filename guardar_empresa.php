<?php
// Reutilizar la misma configuración de conexión que usuarios_abm.php
include("config/db.php");

// Escapar datos del formulario
$nombre     = $mysqli->real_escape_string($_POST['nombre']);
$cuit       = $mysqli->real_escape_string($_POST['cuit']);
$email      = $mysqli->real_escape_string($_POST['email']);
$telefono   = $mysqli->real_escape_string($_POST['telefono']);
$direccion  = $mysqli->real_escape_string($_POST['direccion']);
$rubro      = $mysqli->real_escape_string($_POST['rubro']);
$descripcion= $mysqli->real_escape_string($_POST['descripcion']);
$sitio_web  = $mysqli->real_escape_string($_POST['sitio_web']);
$cuenta_bancaria = $mysqli->real_escape_string($_POST['cuenta_bancaria']);

// Logo como BLOB
$logo_data = null;
$logo_tipo = null;
if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    $logo_data = file_get_contents($_FILES['logo']['tmp_name']);
    $logo_data = $mysqli->real_escape_string($logo_data);
    $logo_tipo = $mysqli->real_escape_string($_FILES['logo']['type']);
}

// Query de inserción
$sql = "INSERT INTO empresas 
(nombre, cuit, email, telefono, direccion, rubro, descripcion, sitio_web, cuenta_bancaria, logo, logo_tipo) 
VALUES ('$nombre','$cuit','$email','$telefono','$direccion','$rubro','$descripcion','$sitio_web','$cuenta_bancaria','$logo_data','$logo_tipo')";

if ($mysqli->query($sql) === TRUE) {
    echo "<script>alert('Empresa registrada correctamente'); window.location.href='dashboard.php';</script>";
} else {
    echo "Error: " . $mysqli->error;
}

$mysqli->close();
?>
