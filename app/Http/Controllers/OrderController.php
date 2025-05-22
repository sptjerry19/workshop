<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Helpers\Common;
use App\Http\Requests\Order\StoreRequest;
use App\Models\Order;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    /**
     * Store a newly created order in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            $fields = $request->validated();

            $order = $this->orderService->createOrder($fields);

            return APIResponse::success($order, 'create order success');
        } catch (\Exception $e) {
            Log::error('Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return APIResponse::error('create order failed', 500);
        }
    }

    public function show($id)
    {
        try {
            $order = Order::findOrFail($id);
            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'order_status' => 'required|string|in:pending,processing,completed,cancelled',
                'payment_status' => 'required|string|in:pending,paid,failed'
            ]);

            $order = Order::findOrFail($id);
            $order->update($validated);

            Log::info('Order status updated', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'new_status' => $validated
            ]);

            return APIResponse::success([], 'Order status updated successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error updating order status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return APIResponse::error('Failed to update order status', 500);
        }
    }

    public function index(Request $request)
    {
        $params = $request->only([
            'q',
            'status',
            'payment_status',
        ]);

        $orders = $this->orderService->getOrders($params);
        if (!$orders) {
            return APIResponse::error('Failed to fetch orders', 500);
        }
        return APIResponse::success($orders['items'], 'Orders fetched successfully', 200, $orders['pagination']);
    }

    public function generateQr(Request $request)
    {
        try {
            $params = $request->only([
                'order_number',
                'amount',
            ]);
            $amout = $params['amount'] ?? 0;
            $description = 'Thanh toán đơn hàng ' . $params['order_number'] ?? '';
            $accountName = 'Pham Duy Linh';
            return APIResponse::success(Common::generateQr($amout, $description, $accountName), 'QR code generated successfully', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return APIResponse::error('Failed to generate QR code', 500);
        }
    }
}
