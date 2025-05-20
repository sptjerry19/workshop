<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseService
{
    protected function transformPaginationResult($result, callable $transform = null): array
    {
        if ($result instanceof LengthAwarePaginator) {
            $data = $transform ? $result->getCollection()->map($transform) : $result->getCollection();

            return [
                'items' => $data->values(),
                'pagination' => [
                    'total' => $result->total(),
                    'per_page' => $result->perPage(),
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                ]
            ];
        }

        // fallback nếu không phải phân trang
        $data = $transform ? $result->map($transform) : $result;
        return [
            'items' => $data->values(),
            'pagination' => null
        ];
    }
}
