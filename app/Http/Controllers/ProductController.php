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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function destroy(string $id)
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

    public function exportCsv(Request $request)
    {
        try {
            $params = $request->only([
                'q',
                'category_id',
                'sort_by',
                'sort_order',
                'per_page',
                'take',
            ]);

            $fileName = 'products_export_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];

            $callback = function () use ($params) {
                $handle = fopen('php://output', 'w');

                // Thêm BOM UTF-8 để Excel nhận đúng tiếng Việt
                fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Header
                fputcsv($handle, [
                    'id',
                    'name',
                    'description',
                    'price',
                    'image',
                    'category_name',
                    'is_active',
                    'stock',
                    'discount',
                    'size',
                    'options',
                    'toppings'
                ]);
                // Data
                $products = $this->productService->getAllProductsForExport($params);
                foreach ($products as $product) {
                    $transformed = $product->transform();
                    // options: name:value,name:value,...
                    $options = collect($transformed['options'])->map(function ($option) {
                        $values = collect($option['values'])->map(function ($v) {
                            return $v['value'];
                        })->implode('|');
                        return $option['name'] . ($values ? (':' . $values) : '');
                    })->implode(',');
                    // toppings: name|price,name|price,...
                    $toppings = collect($transformed['toppings'])->map(function ($topping) {
                        return $topping['name'] . (isset($topping['price']) ? ('|' . $topping['price']) : '');
                    })->implode(',');
                    fputcsv($handle, [
                        $transformed['id'],
                        $transformed['name'],
                        $transformed['description'],
                        $transformed['price'],
                        $transformed['image'],
                        $transformed['category_name'],
                        $product->is_active,
                        $transformed['stock'],
                        $transformed['discount'],
                        json_encode($transformed['size']),
                        $options,
                        $toppings,
                    ]);
                }
                fclose($handle);
            };
            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return APIResponse::error('Export failed: ' . $e->getMessage(), 500);
        }
    }

    public function importCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $results = [
            'created' => [],
            'updated' => [],
            'errors' => [],
        ];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);

                // Parse fields
                $data['size'] = $data['size'] ? json_decode($data['size'], true) : [];
                $data['options'] = $data['options'] ? explode(',', $data['options']) : [];
                $data['toppings'] = $data['toppings'] ? explode(',', $data['toppings']) : [];

                // Validate each row (basic)
                $rowValidator = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'description' => 'required|string',
                    'price' => 'required|numeric|min:0',
                    'image' => 'required|string',
                    'category_id' => 'required|exists:categories,id',
                    'stock' => 'required|integer|min:0',
                    'discount' => 'nullable|numeric|min:0',
                ]);
                if ($rowValidator->fails()) {
                    $results['errors'][] = [
                        'row' => $data,
                        'errors' => $rowValidator->errors()->all(),
                    ];
                    continue;
                }

                // Create or update
                if (!empty($data['id'])) {
                    $result = $this->productService->updateOrCreateProduct($data, true);
                    $results['updated'][] = $result;
                } else {
                    $result = $this->productService->updateOrCreateProduct($data, false);
                    $results['created'][] = $result;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return APIResponse::error('Import failed: ' . $e->getMessage(), 500);
        } finally {
            fclose($handle);
        }

        return APIResponse::success($results, 'Import completed', 200);
    }
}