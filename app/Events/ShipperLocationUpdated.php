<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipperLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $orderId;
    public float $shipperLat;
    public float $shipperLng;
    public string $status;

    public function __construct(Order $order)
    {
        $this->orderId = $order->id;
        $this->shipperLat = (float) $order->shipper_lat;
        $this->shipperLng = (float) $order->shipper_lng;
        $this->status = $order->status;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('order.' . $this->orderId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ShipperLocationUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'shipper_lat' => $this->shipperLat,
            'shipper_lng' => $this->shipperLng,
            'status' => $this->status,
        ];
    }
}
