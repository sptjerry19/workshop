<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array',
                'customer' => 'required|array',
                'customer.name' => 'required|string|max:255',
                'customer.phone' => 'required|string|max:20',
                'customer.address' => 'required|string',
                'total' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:cod,momo'
            ]);

            $order = Order::create([
                'customer_name' => $validated['customer']['name'],
                'customer_phone' => $validated['customer']['phone'],
                'customer_address' => $validated['customer']['address'],
                'total_amount' => $validated['total'],
                'payment_method' => $validated['payment_method'],
                'items' => $validated['items'],
                'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'pending',
                'order_status' => 'pending'
            ]);

            Log::info('New order created', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_method' => $order->payment_method
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
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

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating order status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $orders = Order::latest()->paginate(10);
            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
