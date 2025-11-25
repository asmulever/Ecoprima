<?php

declare(strict_types=1);

namespace App\Application\UseCase\Product;

use App\Application\DTO\Product\ProductDTO;
use App\Domain\Repository\ProductRepositoryInterface;

class ListProductsUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function execute(): array
    {
        $products = $this->productRepository->findAllActive();
        return array_map(fn($product) => ProductDTO::fromEntity($product), $products);
    }
}
