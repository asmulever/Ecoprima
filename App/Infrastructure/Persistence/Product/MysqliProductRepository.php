<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Product;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use mysqli;

class MysqliProductRepository implements ProductRepositoryInterface
{
    public function __construct(private mysqli $conn)
    {
    }

    public function findAllActive(): array
    {
        $sql = "
            SELECT p.id, p.nombre, p.descripcion, p.precio, p.ubicacion, p.estado,
                   u.email,
                   (
                       SELECT id FROM producto_imagenes
                       WHERE producto_id = p.id
                       ORDER BY orden ASC
                       LIMIT 1
                   ) AS imagen_id
            FROM productos p
            JOIN usuarios u ON u.id = p.usuario_id
            WHERE p.estado = 'activo'
            ORDER BY p.fecha_creacion DESC
        ";

        $result = $this->conn->query($sql);
        if (!$result) {
            return [];
        }

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = new Product(
                (int) $row['id'],
                $row['nombre'],
                $row['descripcion'],
                (float) $row['precio'],
                $row['ubicacion'],
                $row['estado'],
                $row['email'],
                $row['imagen_id'] ? (int) $row['imagen_id'] : null
            );
        }

        return $products;
    }
}
