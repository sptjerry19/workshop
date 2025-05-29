<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendOrderNotifications extends Command
{
    protected $signature = 'orders:send-notifications';
    protected $description = 'Send notifications for completed orders';

    public function handle()
    {
        try {
            $this->info('Start push notification');
            // Get completed orders that haven't been notified
            $completedOrders = Order::where('order_status', 'completed')
                // ->whereDoesntHave('notifications', function ($query) {
                //     $query->where('type', 'order_completed');
                // })
                ->get();

            $this->info("Found " . $completedOrders->count() . " orders to notify");

            foreach ($completedOrders as $order) {
                // Create notification
                $notification = Notification::create([
                    'order_id' => $order->id,
                    'type' => 'order_completed',
                    'content' => "Đơn hàng #{$order->id} đã được hoàn thành. Thời gian: " . now()->format('H:i d/m/Y'),
                    'is_sent' => true,
                    'sent_at' => now()
                ]);

                // Broadcast notification to all connected clients
                broadcast(new \App\Events\OrderCompleted($notification))->toOthers();

                $this->info("Notification sent for order #{$order->id}");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Error sending order notifications: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
