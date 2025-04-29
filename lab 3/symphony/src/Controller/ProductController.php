<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private const PRODUCTS = [
        ['id' => 1, 'name' => 'Product 1', 'description' => 'Desc 1', 'price' => 100],
        ['id' => 2, 'name' => 'Product 2', 'description' => 'Desc 2', 'price' => 200],
    ];

    #[Route('/products', name: 'get_products', methods: ['GET'])]
    public function getProducts(): JsonResponse
    {
        return new JsonResponse(['data' => self::PRODUCTS], JsonResponse::HTTP_OK);
    }

    #[Route('/products/{id}', name: 'get_product_item', methods: ['GET'])]
    public function getProductItem(int $id): JsonResponse
    {
        $product = array_filter(self::PRODUCTS, fn($item) => $item['id'] === $id);
        if (empty($product)) {
            return new JsonResponse(['data' => ['error' => "Not found product by id $id"]], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['data' => array_values($product)[0]], JsonResponse::HTTP_OK);
    }

    #[Route('/products', name: 'post_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newProduct = [
            'id' => random_int(100, 999),
            'name' => $data['name'] ?? 'No Name',
            'description' => $data['description'] ?? '',
            'price' => $data['price'] ?? 0,
        ];

        // TODO: Insert to database here

        return new JsonResponse(['data' => $newProduct], JsonResponse::HTTP_CREATED);
    }
}
