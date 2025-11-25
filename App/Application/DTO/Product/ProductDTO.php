<?php

declare(strict_types=1);

namespace App\Application\DTO\Product;

use App\Domain\Entity\Product;

class ProductDTO
{
    public static function fromEntity(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'nombre' => $product->getNombre(),
            'descripcion' => $product->getDescripcion(),
            'precio' => $product->getPrecio(),
            'ubicacion' => $product->getUbicacion(),
            'estado' => $product->getEstado(),
            'email' => $product->getEmail(),
            'imagen_id' => $product->getImagenId(),
        ];
    }
}
