<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Events\ShipperLocationUpdated;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string|max:255',
            'delivery_lat' => 'nullable|numeric',
            'delivery_lng' => 'nullable|numeric',
        ]);

        Order::create([
            'customer_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'status' => 'waiting',
            'delivery_address' => $request->delivery_address,
            'delivery_lat' => $request->delivery_lat,
            'delivery_lng' => $request->delivery_lng,
        ]);

        return redirect()->back()->with('success', 'Order placed successfully!');
    }

    public function accept(Order $order)
    {
        if ($order->status !== 'waiting') {
            return redirect()->back()->with('error', 'This order has already been accepted!');
        }

        $order->update([
            'shipper_id' => Auth::id(),
            'status' => 'accepted',
        ]);

        return redirect()->back()->with('success', 'Order accepted successfully!');
    }

    public function startDelivery(Request $request, Order $order)
    {
        if ($order->shipper_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to deliver this order!');
        }

        $order->update([
            'status' => 'delivering',
            'shipper_lat' => $request->shipper_lat,
            'shipper_lng' => $request->shipper_lng,
        ]);

        return redirect()->route('orders.map', $order->id)->with('success', 'Delivery started!');
    }

    public function complete(Order $order)
    {
        if ($order->shipper_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to complete this order!');
        }

        $order->update([
            'status' => 'completed',
        ]);

        return redirect()->back()->with('success', 'Order completed successfully!');
    }

    public function showMap(Order $order)
    {
        $order->load(['product', 'customer']);
        return view('map', compact('order'));
    }

    public function updateLocation(Request $request, Order $order)
    {
        if ($order->shipper_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'shipper_lat' => 'required|numeric',
            'shipper_lng' => 'required|numeric',
        ]);

        $order->update([
            'shipper_lat' => $request->shipper_lat,
            'shipper_lng' => $request->shipper_lng,
        ]);

        broadcast(new ShipperLocationUpdated($order));

        return response()->json(['success' => true]);
    }

    public function getOrderData(Order $order)
    {
        $order->load(['product', 'customer']);
        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'customer_name' => $order->customer->name ?? 'Customer',
            'product_name' => $order->product->name ?? 'Product',
            'quantity' => $order->quantity,
            'delivery_address' => $order->delivery_address,
            'delivery_lat' => $order->delivery_lat,
            'delivery_lng' => $order->delivery_lng,
            'shipper_lat' => $order->shipper_lat,
            'shipper_lng' => $order->shipper_lng,
        ]);
    }
}
