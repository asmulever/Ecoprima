<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Product
{
    public function __construct(
        private int $id,
        private string $nombre,
        private ?string $descripcion,
        private float $precio,
        private string $ubicacion,
        private string $estado,
        private ?string $email,
        private ?int $imagenId
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getUbicacion(): string
    {
        return $this->ubicacion;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getImagenId(): ?int
    {
        return $this->imagenId;
    }
}
