<?php

namespace App\Services;

use App\Helpers\Common;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProductService extends BaseService
{
    protected Model $model;

    public function __construct()
    {
        $this->model = new Product();
    }

    public function getProducts(array $params)
    {
        $keyword = $params['q'] ?? null;
        $categoryId = $params['category_id'] ?? null;
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';
        $perPage = $params['per_page'] ?? 10;
        $take = $params['take'] ?? null;

        try {
            $products = Product::with('category')
                ->when($keyword, fn($query) => $query->where('name', 'like', "%$keyword%"))
                ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
                ->orderBy($sortBy, $sortOrder);

            if (!is_null($take)) {
                $result = $products->take($take)->get();
            } else {
                $result = $products->paginate($perPage);
            }

            return $this->transformPaginationResult($result, fn($item) => $item->transform());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getAllProducts(array $params)
    {
        $keyword = $params['q'] ?? null;
        $categoryId = $params['category_id'] ?? null;
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';

        try {
            $products = Product::with('category')
                ->when($keyword, fn($query) => $query->where('name', 'like', "%$keyword%"))
                ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
                ->orderBy($sortBy, $sortOrder)
                ->get();

            return $this->transformPaginationResult($products, fn($item) => $item->transform());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getAllProductsForExport(array $params)
    {
        $keyword = $params['q'] ?? null;
        $categoryId = $params['category_id'] ?? null;
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';

        try {
            $products = Product::with('category')
                ->when($keyword, fn($query) => $query->where('name', 'like', "%$keyword%"))
                ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
                ->orderBy($sortBy, $sortOrder)
                ->get();

            return $products;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getProductById($id)
    {
        try {
            return Product::with('category')->find($id);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
    public function createProduct(array $data)
    {
        try {
            if (!empty($data['image'])) {
                $path = Common::storeBase64Image($data['image']);
                if ($path) {
                    $data['image'] = $path;
                } else {
                    throw new \Exception('Invalid base64 image data.');
                }
            }

            return $this->create($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
    public function updateProduct(Product $product, array $data)
    {
        // Kiểm tra nếu tồn tại key 'image'
        if (isset($data['image'])) {
            $image = $data['image'];

            // Nếu là base64 image
            if (preg_match('/^data:image\/(\w+);base64,/', $image)) {
                $data['image'] = Common::storeBase64Image($image);
            } else {
                // Nếu không phải base64 (có thể là đường dẫn cũ) => giữ nguyên
                unset($data['image']);
            }
        }

        $product->update($data);
        return $product;
    }
    public function deleteProduct(Product $product)
    {
        return $product->delete();
    }


    // Tạo mới hoặc cập nhật sản phẩm từ dòng CSV
    public function updateOrCreateProduct($data, $isUpdate = false)
    {
        // Chuẩn hóa dữ liệu
        $fields = [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'image' => $data['image'],
            'category_id' => $data['category_id'],
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
            'stock' => $data['stock'],
            'discount' => $data['discount'] ?? null,
            'size' => $data['size'] ?? [],
        ];

        if ($isUpdate && !empty($data['id'])) {
            $product = Product::find($data['id']);
            if (!$product) {
                throw new \Exception('Product not found: ' . $data['id']);
            }
            $product->update($fields);
        } else {
            $product = Product::create($fields);
        }

        // Sync options & toppings
        if (isset($data['options'])) {
            $product->options()->sync($data['options']);
        }
        if (isset($data['toppings'])) {
            $product->toppings()->sync($data['toppings']);
        }

        return $product->fresh(['options', 'toppings']);
    }
}