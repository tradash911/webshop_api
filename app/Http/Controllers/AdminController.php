<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
   // routes/api.php


public function adminLogin(Request $request) {
        $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    if (!$user->is_admin) {
        return response()->json(['message' => 'Unauthorized, not an admin'], 403);
    }

    $token = $user->createToken('admin-token')->plainTextToken;

   

    return response()->json([
        'token' => $token,
    ]);
}

/* public function viewOrders(Request $request){
     Gate::authorize('viewAny', User::class);  

      $orders = Order::paginate(10);
      
      return OrderResource::collection($orders);
}

public function viewUsers (Request $request) {
     Gate::authorize('viewAny', User::class);

    $users = User::with('orders.orderItems.product')->paginate(20);
    return UserResource::collection($users);

}

public function findUser(Request $request) {
    Gate::authorize('viewAny', User::class);
  $query = User::query();

        if( $search=$request->search) {

              $query->where(function ($q) use ($search) {
             $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
             });

            return UserResource::collection($query->paginate(10));}
}


public function createProduct(Request $request) {
     Gate::authorize('create', Product::class);  

        $product = Product::create([
            ...$request->validate([
                'name'=>'required|string|max:255|',
                'description'=>'required|string|max:255',
                'price'=>'required',
                'quantity'=>'required',
                'category_id'=>'required',
                'discount_price' => 'sometimes|integer',
                "weight" => 'sometimes|integer'
            ])
        ]);
        $product->load('category');
        return new ProductResource($product);
}
 */
}