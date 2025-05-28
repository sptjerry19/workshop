<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
        Log::info('OrderStatusUpdated event constructed', [
            'order_id' => $order->id,
            'order_status' => $order->order_status
        ]);
    }

    public function broadcastOn()
    {
        return new Channel('order.' . $this->order->id);
    }

    public function broadcastAs()
    {
        return 'OrderStatusUpdated';
    }

    public function broadcastWith()
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'order_status' => $this->order->order_status,
                'payment_status' => $this->order->payment_status,
                'updated_at' => $this->order->updated_at
            ]
        ];
    }
}
