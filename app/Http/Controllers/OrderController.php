<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request)
{
    $data = $request->validate([
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1'
    ]);

    $order = Order::create([
        'user_id' => $request->user()->id,
        'total_price' => 0,
        'status' => 'pending',
        'order_number' => rand(100000000,20000000)
    ]);

    $total = 0;

    foreach ($data['items'] as $item) {
        $product = Product::findOrFail($item['product_id']);

        $order->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => $item['quantity'],
            'price' => $product->price,
            
        ]);
        $total += $product->price * $item['quantity'];
        $product->decrement('quantity', $item['quantity']);
    }

    $order->update([
        'total_price' => $total
    ]);

    return $order->load('orderItems.product');
}

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
