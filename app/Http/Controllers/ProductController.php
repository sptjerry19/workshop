<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $params = $request->only([
            'q',
            'category_id',
            'sort_by',
            'sort_order',
            'per_page',
            'take'
        ]);

        $result = $this->productService->getProducts($params);

        if (!$result) {
            return ApiResponse::success([], 'Failed to fetch products', 500);
        }

        return ApiResponse::success($result['items'], 'Fetched successfully', 200, $result['pagination']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|string',
            'price' => 'required|numeric|min:0',
            'size' => 'nullable|array',
            'size.*.label' => 'required|string',
            'size.*.price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'discount' => 'nullable|numeric|min:0'
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        $product->load('category');
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'image' => $product->image,
            'price' => $product->price,
            'size' => $product->size,
            'sold' => $product->sold,
            'rating' => $product->rating,
            'reviews' => $product->reviews,
            'description' => $product->description,
            'category_id' => $product->category_id,
            'category_name' => $product->category->name,
            'stock' => $product->stock,
            'discount' => $product->discount,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|string',
            'price' => 'required|numeric|min:0',
            'size' => 'nullable|array',
            'size.*.label' => 'required|string',
            'size.*.price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'discount' => 'nullable|numeric|min:0'
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
