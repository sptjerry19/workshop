<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseService
{
    protected Model $model;

    public function __construct()
    {
        if (!isset($this->model)) {
            throw new \Exception("Model property must be set in child service class.");
        }
    }

    /**
     * Danh sách có phân trang hoặc giới hạn take
     */
    public function getList(array $params, callable $filterCallback = null, callable $transform = null)
    {
        $query = $this->model->query();

        // Callback xử lý điều kiện filter
        if ($filterCallback) {
            $query = $filterCallback($query, $params);
        }

        $perPage = $params['per_page'] ?? 10;
        $take = $params['take'] ?? null;

        if ($take) {
            $result = $query->take($take)->get();
        } else {
            $result = $query->paginate($perPage);
        }

        return $this->transformPaginationResult($result, $transform);
    }

    public function getById($id, array $relations = [])
    {
        return $this->model->with($relations)->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data)
    {
        $model->update($data);
        return $model;
    }

    public function delete(Model $model)
    {
        return $model->delete();
    }

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

        $data = $transform ? $result->map($transform) : $result;
        return [
            'items' => $data->values(),
            'pagination' => null
        ];
    }
}
