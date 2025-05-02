<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    private array $products = [
        ['id' => 1, 'name' => 'Product 1', 'description' => 'Desc 1', 'price' => 100],
        ['id' => 2, 'name' => 'Product 2', 'description' => 'Desc 2', 'price' => 200],
    ];

    public function getProducts(Request $request)
    {
        $filtered = collect($this->products)->filter(function ($product) use ($request) {
            return (!$request->has('id') || $product['id'] == $request->id)
                && (!$request->has('name') || str_contains(strtolower($product['name']), strtolower($request->name)))
                && (!$request->has('price') || $product['price'] == $request->price);
        })->values();

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedItems = $filtered->slice($offset, $perPage);

        $paginator = new LengthAwarePaginator(
            $paginatedItems,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginator);
    }

    public function getProductItem($id)
    {
        $product = collect($this->products)->firstWhere('id', $id);

        if (!$product) {
            return response()->json(['data' => ['error' => "Not found product by id $id"]], 404);
        }

        return response()->json(['data' => $product], 200);
    }

    public function createProduct(Request $request)
    {
        $data = $request->all();

        $newProduct = [
            'id' => rand(100, 999),
            'name' => $data['name'] ?? 'No Name',
            'description' => $data['description'] ?? '',
            'price' => $data['price'] ?? 0,
        ];

        return response()->json(['data' => $newProduct], 201);
    }
}
