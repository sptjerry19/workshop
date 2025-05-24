<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProductUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
        Log::info('ProductUpdated event constructed', [
            'product_id' => $product->id,
            'product_name' => $product->name
        ]);
    }

    public function broadcastOn()
    {
        Log::info('Broadcasting on channel: products');
        return new Channel('products');
    }

    public function broadcastAs()
    {
        $eventName = 'ProductUpdated';
        Log::info('Broadcasting as event: ' . $eventName);
        return $eventName;
    }

    public function broadcastWith()
    {
        $data = [
            'message' => 'Product updated',
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->price,
                'description' => $this->product->description,
                'image' => $this->product->image,
                'category_id' => $this->product->category_id,
                'discount' => $this->product->discount,
                'is_new' => $this->product->is_new,
                'size' => $this->product->size,
                'updated_at' => $this->product->updated_at
            ]
        ];

        Log::info('Broadcasting with data', [
            'event_name' => $this->broadcastAs(),
            'data' => $data
        ]);

        return $data;
    }

    // public function broadcastQueue()
    // {
    //     return 'broadcasts';
    // }
}
