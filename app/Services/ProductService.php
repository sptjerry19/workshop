<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductService extends BaseService
{
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
}