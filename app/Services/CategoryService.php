<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class CategoryService extends BaseService
{
    protected Model $model;

    public function __construct()
    {
        $this->model = new Category();
    }

    public function getAll()
    {
        try {
            $categories = Category::with('products')
                ->orderBy('created_at', 'desc')
                ->select('id', 'name', 'description')
                ->paginate(10);
            return $this->transformPaginationResult($categories, fn($item) => $item->transform());
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getSelect()
    {
        try {
            $categories = Category::select('id', 'name')->get();
            return $categories;
        } catch (\Exception $e) {
            return false;
        }
    }
}
