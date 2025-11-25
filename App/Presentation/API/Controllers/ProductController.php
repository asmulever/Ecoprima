<?php

declare(strict_types=1);

namespace App\Presentation\API\Controllers;

use App\Application\UseCase\Product\ListProductsUseCase;
use App\Presentation\API\Http\Request;
use App\Presentation\API\Http\Response;

class ProductController
{
    public function __construct(private ListProductsUseCase $listProducts)
    {
    }

    public function index(Request $request): Response
    {
        $products = $this->listProducts->execute();
        return new Response(['data' => $products]);
    }
}
