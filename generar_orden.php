<?php
session_start();
include('config/db.php');

// 1. Validar sesión y parámetros
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: marketplace.php?status=error&message=" . urlencode("ID de producto inválido."));
    exit();
}

$producto_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

// 2. Obtener datos del producto y del comprador
$mysqli->begin_transaction();

try {
    // Bloquear el producto para evitar race conditions
    $stmt = $mysqli->prepare("SELECT * FROM productos WHERE id = ? AND estado = 'activo' FOR UPDATE");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();

    if (!$producto) {
        throw new Exception("El producto no está disponible o no existe.");
    }

    // Obtener email del comprador
    $user_stmt = $mysqli->prepare("SELECT email FROM usuarios WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $comprador = $user_stmt->get_result()->fetch_assoc();

    if (!$comprador) {
        throw new Exception("Usuario comprador no encontrado.");
    }

    // 3. Actualizar el estado del producto a 'pendiente'
    $update_stmt = $mysqli->prepare("UPDATE productos SET estado = 'pendiente' WHERE id = ?");
    $update_stmt->bind_param("i", $producto_id);
    if (!$update_stmt->execute()) {
        throw new Exception("No se pudo actualizar el estado del producto.");
    }

    // 4. Enviar email de confirmación al comprador
    $email_comprador = $comprador['email'];
    $nombre_producto = $producto['nombre'];
    $descripcion_producto = $producto['descripcion'];
    $precio_producto = number_format((float)$producto['precio'], 2);

    $subject = "Confirmación de Orden de Compra - EcoPrima";
    $link_confirmacion = "https://".$_SERVER['HTTP_HOST']."/confirmar_orden.php?producto_id=".$producto_id; // Script hipotético

    $message = "Hola,\n\n";
    $message .= "Has generado una orden de compra para el siguiente producto en EcoPrima:\n\n";
    $message .= "Producto: $nombre_producto\n";
    $message .= "Descripción: $descripcion_producto\n";
    $message .= "Precio: $$precio_producto\n\n";
    $message .= "El vendedor ha sido notificado y se pondrá en contacto contigo.\n\n";
    $message .= "Gracias por usar EcoPrima.";

    $headers = "From: no-reply@ecoprima.com";

    if (!mail($email_comprador, $subject, $message, $headers)) {
        // Aunque el email falle, la transacción no debe revertirse, pero sí registrar el error.
        error_log("Fallo al enviar el email de orden de compra a: " . $email_comprador);
    }

    // 5. Confirmar la transacción
    $mysqli->commit();

    header("Location: marketplace.php?status=success&message=" . urlencode("Orden de compra generada. Se ha enviado un correo de confirmación."));

} catch (Exception $e) {
    $mysqli->rollback();
    header("Location: marketplace.php?status=error&message=" . urlencode($e->getMessage()));
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($user_stmt)) $user_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    $mysqli->close();
}
?>