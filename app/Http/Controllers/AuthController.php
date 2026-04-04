<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException ;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user=User::where('email',$request->email)->first();

        if(!$user){
            throw ValidationException::withMessages([
                'email'=> ['The provided credentials are incorrect.']
            ]);
        }

        if(!Hash::check($request->password,$user->password)) {
                throw ValidationException::withMessages([
                'email'=> ['The provided credentials are incorrect.']
            ]);
        }

      $token= $user->createToken('api-token')->plainTextToken;

      return response([
        'token'=>$token
      ]);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return response()->json([
            "message"=>"Logged out succesfully"
        ]);
    }

    public function register(Request $request)
{
    // 1️⃣ Validáció
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'sometimes|string|max:50',
        'email' => 'required|string|email|max:255|unique:users',
        'phone' => 'sometimes|string|max:20',
        'address' =>'required|string|max:150',
        'password' => 'required|string|min:6|confirmed', // password_confirmation kell
    ]);

    // 2️⃣ User létrehozása
    $user = User::create([
        'name' => $data['name'],
        'username' => $data['username'] ?? null,
        'phone' => $data['phone'] ?? null,
        'email' => $data['email'],
        'address' => $data['address'],
        'password' => Hash::make($data['password']),
    ]);

    // 3️⃣ Welcome email küldése
  /*    Mail::to($user->email)->queue(new WelcomeMail($user)); */ 
  Mail::to($user->email)->send(new WelcomeMail($user));

    // 4️⃣ Auth token létrehozása
    $token = $user->createToken('auth_token')->plainTextToken;

    // email verification küldés
    $user->sendEmailVerificationNotification(); 

    return response()->json([
        'message' => 'User registered successfully',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ], 201);
}
}
