<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Nếu là Shipper (role = 1)
        if ($user && $user->role === 1) {
            $availableOrders = Order::with('product', 'customer')
                ->where('status', 'waiting')
                ->latest()
                ->get();

            $myOrders = Order::with('product', 'customer')
                ->where('shipper_id', $user->id)
                ->whereIn('status', ['accepted', 'delivering'])
                ->latest()
                ->get();

            return view('shipper', compact('availableOrders', 'myOrders'));
        }

        $product = Product::all();

        return view('home', compact('product'));
    }
}
