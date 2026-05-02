<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\PersonalAccessToken;
use App\Services\SendOrderConfirmMail;

use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function viewOrders(Request $request)
    {
      Gate::authorize('viewAny', User::class);  

      $orders = Order::paginate(10);
      
      return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request){
$token = $request->bearerToken();

$user = null;

if ($token) {
    $accessToken = PersonalAccessToken::findToken($token);
    $user = $accessToken?->tokenable;
}
    $data = $request->validate([
       
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',

        'name' => $user ? 'nullable|string|max:255' : 'required|string|max:255',
        'email' => $user ? 'nullable|email' : 'required|email',
        'phone' => $user ? 'nullable|string' : 'required|string',

        'zip' => $user ? 'nullable|string' : 'required|string',
        'city' => $user ? 'nullable|string' : 'required|string',
        'address_line' => $user ? 'nullable|string' : 'required|string',


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

if ($request->same_as_shipping) {
    $billing = [
        'billing_name' => $user?->name ?? $request->name,
        'billing_zip' => $user?->zip ?? $request->zip,
        'billing_city' => $user?->city ?? $request->city,
        'billing_address_line' => $user?->address_line ?? $request->address_line,
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


foreach ($data['items'] as $item) {
        $product = Product::findOrFail($item['product_id']);

    if ($product->quantity < $item['quantity']) {
        return response()->json([
            'message' => "Not enough stock"
        ], 400);
    }
}

   return DB::transaction(function () use ($data, $user, $billing) {
        $total = 0;

 $order = Order::create([
    'user_id' => $user?->id,
    'total_price' => 0,
    'status' => 'pending',

    // shipping
    'name' => $user?->name ?? $data['name'],
    'email' => $user?->email ?? $data['email'],
    'phone' => $user?->phone ?? $data['phone'],

    'zip' => $user?->zip ?? $data['zip'],
    'city' => $user?->city ?? $data['city'],
    'address_line' => $user?->address_line ?? $data['address_line'],

    // billing
    ...$billing
]);
    
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
    $items=[];
    foreach ($data['items'] as $item) {

      $items[] =  Product::findOrFail($item['product_id']);
    }
  
   
    app(SendOrderConfirmMail::class)->send($user,$order,$items);
   
    return $order->load('orderItems.product');
   });

   
}

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    public function findOrder(Request $request)
    {
        Gate::authorize('viewAny', User::class); 
        
        $query = Order::query();

        if( $search=$request->search) {

              $query->where(function ($q) use ($search) {
             $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('order_number','like', "%$search%");
             });

            return OrderResource::collection($query->paginate(10));}
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
