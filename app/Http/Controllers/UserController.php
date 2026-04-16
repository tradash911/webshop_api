<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function viewUsers()
    {
         Gate::authorize('viewAny', User::class);  

        $users = User::with('orders.orderItems.product')->paginate(20);
       return UserResource::collection($users); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
/*    public function show(string $id)
{
   
        $user = User::with('orders.orderItems.product')->findOrFail($id);

        Gate::authorize('isOwner', $user);
    
        return new UserResource($user);

        
} */

         public function show(Request $request)
{
   

    $userId = $request->user()->id;
      $user = User::with('orders.orderItems.product')->findOrFail($userId);
     
        Gate::authorize('isOwner', $user);
    
        return new UserResource($user);
 
        
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
            $userId = $request->user()->id;
            $user = User::findOrFail($userId);
            // Gate::authorize('isOwner', $user);

            $data = $request->validate([
                    "name"=> "sometimes|string",
                    "zip" => "sometimes|string",
                    "city" => "sometimes|string",
                    "address_line" => "sometimes|string",
                    "phone"=> "sometimes|string",
                    "address"=> "sometimes|string",
                    "newsletter_subscribed" => "sometimes|boolean"
                    
            ]);

            $user->update($data);

          return response([
            'message' => "Adatok sikeresen frissítve",
            $user
          ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
          $userId = $request->user()->id;
            $user = User::findOrFail($userId);
            $user->delete();

        return response()->json([
            'message' => "Fiók sikeresen törölve"
        ]);
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
}
