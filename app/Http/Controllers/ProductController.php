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

        $product = $this->productService->createProduct($fields);
        if (!$product) {
            return ApiResponse::error('Failed to create product', 500);
        }

        // Log before broadcasting
        Log::info('Broadcasting ProductUpdated event', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'broadcast_driver' => config('broadcasting.default'),
            'pusher_app_id' => config('broadcasting.connections.pusher.app_id'),
            'pusher_app_key' => config('broadcasting.connections.pusher.key'),
            'pusher_app_cluster' => config('broadcasting.connections.pusher.options.cluster')
        ]);

        try {
            // Broadcast the event
            broadcast(new ProductUpdated($product));

            // Log after broadcasting
            Log::info('ProductUpdated event broadcasted successfully', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'channel' => 'products',
                'event' => 'ProductUpdated'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast ProductUpdated event', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'trace' => $e->getTraceAsString()
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
        $product = $this->productService->getProductById($id);
        if (!$product) {
            return ApiResponse::error('Product not found', 404);
        }

        $updatedProduct = $this->productService->updateProduct($product, $fields);

        // Log before broadcasting
        Log::info('Broadcasting ProductUpdated event', [
            'product_id' => $updatedProduct->id,
            'product_name' => $updatedProduct->name,
            'broadcast_driver' => config('broadcasting.default')
        ]);

        try {
            // Broadcast the event
            broadcast(new ProductUpdated($updatedProduct))->toOthers();

            // Log after broadcasting
            Log::info('ProductUpdated event broadcasted successfully', [
                'product_id' => $updatedProduct->id,
                'product_name' => $updatedProduct->name
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast ProductUpdated event', [
                'error' => $e->getMessage(),
                'product_id' => $updatedProduct->id
            ]);
        }

        return ApiResponse::success($updatedProduct->transform(), 'Product updated successfully', 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}