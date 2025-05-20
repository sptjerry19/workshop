<?php

namespace App\Services;

use App\Models\Category;

class CategoryService extends BaseService
{
    public function getAll()
    {
        return Category::all();
    }
}
