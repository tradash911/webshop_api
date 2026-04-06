<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);  

        $users = User::all();
        return $users;
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
   public function show(string $id)
{
   /*  $user = User::with('orders')->findOrFail($id); */
        $user = User::with('orders.orderItems')->findOrFail($id);

    Gate::authorize('isOwner', $user);

    return $user;
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
            Gate::authorize('isOwner', User::class);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
