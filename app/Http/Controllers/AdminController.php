<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

}