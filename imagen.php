<?php
include("config/db.php");

$id = intval($_GET['id']);
$stmt = $mysqli->prepare("SELECT imagen, mime_type FROM producto_imagenes WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($img, $mime);

if ($stmt->fetch()) {
    header("Content-Type: $mime");
    echo $img;
} else {
    header("Content-Type: image/png");
    readfile("assets/img/placeholder.png");
}
