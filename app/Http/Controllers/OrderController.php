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
        'items.*.quantity' => 'required|integer|min:1',

        // shipping
    'name' => 'required|string|max:255',
    'zip' => 'required|string',
    'city' => 'required|string',
    'address_line' => 'required|string',
    'email' => 'required|email',
    'phone' => 'required|string',

    // billing (optional)
    'billing_name' => 'nullable|string|max:255',
    'billing_zip' => 'nullable|string',
    'billing_city' => 'nullable|string',
    'billing_address_line' => 'nullable|string',
    'company_name' => 'nullable|string|max:255|required_with:tax_id',
    'tax_id' => 'nullable|string|max:50|required_with:company_name',

    
    'same_as_shipping' => 'nullable|boolean',
        
    ],[
        'company_name.required_with' => 'Cégnév kötelező ha van adószám',
        'tax_id.required_with' => 'Adószám kötelező ha van cégnév',
    ]);

    $billing = [];

if ($request->same_as_shipping || !$request->billing_name) {
    $billing = [
        'billing_name' => $data['name'],
        'billing_zip' => $data['zip'],
        'billing_city' => $data['city'],
        'billing_address_line' => $data['address_line'],
        'company_name' => null,
        'tax_id' => null,
    ];
} else {
    $billing = [
        'billing_name' => $data['billing_name'],
        'billing_zip' => $data['billing_zip'],
        'billing_city' => $data['billing_city'],
        'billing_address_line' => $data['billing_address_line'],
        'company_name' => $data['company_name'],
        'tax_id' => $data['tax_id'],
    ];
}


    $order = Order::create([
        'user_id' => $request->user()->id,
        'total_price' => 0,
        'status' => 'pending',
        //shipping
        'name' => $data['name'],
        'zip' => $data['zip'],
        'city' => $data['city'],
        'address_line' => $data['address_line'],
        'email' => $data['email'],
        'phone' => $data['phone'],  
        //billing
        ...$billing
        //'order_number' => rand(100000000,20000000)
    ]);

    $total = 0;

    foreach ($data['items'] as $item) {
        $product = Product::findOrFail($item['product_id']);

        $order->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => $item['quantity'],
            'price' => $product->price,
            'product_name' => $product->name,
            'discount_price' => $product->discount_price,
            
            
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
