<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
require_once __DIR__ . "/config/db.php";

$id=intval($_GET['id']);
$stmt=$mysqli->prepare("SELECT * FROM productos WHERE id=? AND usuario_id=?");
$stmt->bind_param("ii",$id,$_SESSION['user_id']);
$stmt->execute();
$producto=$stmt->get_result()->fetch_assoc();
if(!$producto) die("Producto no encontrado");

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $up=$mysqli->prepare("UPDATE productos SET nombre=?,precio=?,ubicacion=? WHERE id=?");
    $up->bind_param("sdsi",$_POST['nombre'],$_POST['precio'],$_POST['ubicacion'],$id);
    $up->execute();

    if(!empty($_FILES['imagenes']['name'][0])){
        foreach($_FILES['imagenes']['tmp_name'] as $i=>$tmp){
            if(is_uploaded_file($tmp)){
                $imgData=file_get_contents($tmp);
                $mime=mime_content_type($tmp);
                $orden=$i+1;
                $imgStmt=$mysqli->prepare("INSERT INTO producto_imagenes (producto_id,imagen,mime_type,orden) VALUES (?,?,?,?)");
                $null=NULL;
                $imgStmt->bind_param("ibsi",$id,$null,$mime,$orden);
                $imgStmt->send_long_data(1,$imgData);
                $imgStmt->execute();
            }
        }
    }
    header("Location: productos_abm.php"); exit();
}
$imgs=$mysqli->query("SELECT id FROM producto_imagenes WHERE producto_id=$id ORDER BY orden ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Editar Producto - EcoPrima</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>.thumb{width:100px;height:100px;object-fit:cover;margin:5px;}</style></head>
<body class="bg-light">
<?php include("toolbar.php"); ?>
<div class="container py-4">
  <h2>Editar Producto</h2>
  <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="nombre" value="<?=htmlspecialchars($producto['nombre'])?>" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Precio</label><input type="number" step="0.01" name="precio" value="<?=$producto['precio']?>" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Ubicación</label><input type="text" name="ubicacion" value="<?=htmlspecialchars($producto['ubicacion'])?>" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Imágenes actuales</label><br>
      <?php while($img=$imgs->fetch_assoc()): ?>
        <img src="imagen.php?id=<?=$img['id']?>" class="thumb">
      <?php endwhile; ?>
    </div>
    <div class="mb-3"><label class="form-label">Subir nuevas imágenes</label><input type="file" name="imagenes[]" multiple accept="image/*" class="form-control"></div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="productos_abm.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
