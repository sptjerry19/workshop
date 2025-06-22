<?php

namespace App\Http\Controllers;

use App\Events\ProductUpdated;
use App\Helpers\APIResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Log;

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
            'take',
        ]);

        $result = $this->productService->getProducts($params);

        if (!$result) {
            return ApiResponse::success([], 'Failed to fetch products', 500);
        }

        return ApiResponse::success($result['items'], 'Fetched successfully', 200, $result['pagination']);
    }

    public function indexAll(Request $request)
    {
        $params = $request->only([
            'q',
            'category_id',
            'sort_by',
            'sort_order',
            'per_page',
            'take',
        ]);

        $result = $this->productService->getAllProducts($params);

        if (!$result) {
            return ApiResponse::success([], 'Failed to fetch products', 500);
        }

        return ApiResponse::success($result['items'], 'Fetched successfully', 200, $result['pagination']);
    }

    public function store(StoreRequest $request)
    {
        $fields = $request->validated();

        // Extract options and toppings from the request
        $options = $fields['options'] ?? [];
        $toppings = $fields['toppings'] ?? [];
        unset($fields['options'], $fields['toppings']);

        $product = $this->productService->createProduct($fields);
        if (!$product) {
            return ApiResponse::error('Failed to create product', 500);
        }

        // Attach options and toppings
        if (!empty($options)) {
            $product->options()->attach($options);
        }
        if (!empty($toppings)) {
            $product->toppings()->attach($toppings);
        }

        // Load relationships for response
        $product->load(['options.values', 'toppings']);

        try {
            broadcast(new ProductUpdated($product));
        } catch (\Exception $e) {
            Log::error('Failed to broadcast ProductUpdated event', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);
        }

        return ApiResponse::success($product->transform(), 'Product created successfully', 200);
    }

    public function show(Product $product)
    {
        $product = $this->productService->getProductById($product->id);
        if (!$product) {
            return ApiResponse::error('Product not found', 404);
        }
        return ApiResponse::success($product->transform(), 'Product fetched successfully', 200);
    }

    public function update(UpdateRequest $request, string $id)
    {
        $fields = $request->validated();

        // Extract options and toppings from the request
        $options = $fields['options'] ?? [];
        $toppings = $fields['toppings'] ?? [];
        unset($fields['options'], $fields['toppings']);

        $product = $this->productService->getProductById($id);
        if (!$product) {
            return ApiResponse::error('Product not found', 404);
        }

        $updatedProduct = $this->productService->updateProduct($product, $fields);

        // Sync options and toppings
        if (isset($options)) {
            $updatedProduct->options()->sync($options);
        }
        if (isset($toppings)) {
            $updatedProduct->toppings()->sync($toppings);
        }

        // Load relationships for response
        $updatedProduct->load(['options.values', 'toppings']);

        try {
            broadcast(new ProductUpdated($updatedProduct))->toOthers();
        } catch (\Exception $e) {
            Log::error('Failed to broadcast ProductUpdated event', [
                'error' => $e->getMessage(),
                'product_id' => $updatedProduct->id
            ]);
        }

        return ApiResponse::success($updatedProduct->transform(), 'Product updated successfully', 200);
    }

    public function destroy(string   $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            if (!$product) {
                return ApiResponse::error('Product not found', 404);
            }
            broadcast(new ProductUpdated($product))->toOthers();

            // Detach options and toppings
            $product->options()->detach();
            $product->toppings()->detach();

            // Delete the product
            $product->delete();

            return APIResponse::success([], 'Product deleted successfully', 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete product', [
                'error' => $e->getMessage(),
                'product_id' => $id
            ]);
            return APIResponse::error('Failed to delete product', 500);
        }
    }
}
