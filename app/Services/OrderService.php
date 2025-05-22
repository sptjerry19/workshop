<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class OrderService extends BaseService
{
    protected Model $model;

    public function __construct()
    {
        $this->model = new Order();
    }

    public function getOrders(array $params)
    {
        $keyword = $params['q'] ?? null;
        $status = $params['status'] ?? null;
        $paymentStatus = $params['payment_status'] ?? null;
        $perPage = $params['per_page'] ?? 10;
        try {
            $orders = Order::with('orderItems.product')
                ->orderBy('created_at', 'desc')
                ->when($keyword, fn($query) => $query->where('id', 'like', "%$keyword%"))
                ->when($status, fn($query) => $query->where('order_status', $status))
                ->when($paymentStatus, fn($query) => $query->where('payment_status', $paymentStatus))
                ->paginate($perPage);
            return $this->transformPaginationResult($orders, fn($item) => $item->transform());
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createOrder(array $params)
    {
        try {
            $data = [
                'customer_name' => $params['customer']['name'],
                'customer_phone' => $params['customer']['phone'],
                'customer_address' => $params['customer']['address'],
                'total_amount' => $params['total'],
                'payment_method' => $params['payment_method'],
                'items' => $params['items'],
                'payment_status' => $params['payment_method'] === 'cod' ? 'pending' : 'pending',
                'order_status' => 'pending'
            ];
            $order = Order::create($data);
            return $order;
        } catch (\Exception $e) {
            return false;
        }
    }
}
