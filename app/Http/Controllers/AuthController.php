<?php

namespace App\Http\Controllers;

use App\Mail\EmailConfirmationMail;
use App\Mail\WelcomeMail;
use App\Models\EmailChange;
use App\Models\User;
use App\Services\VerificationMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException ;
use App\Services\BrevoMailService;
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
                'email'=> ['Hibás email cím vagy jelszó!']
            ]);
        }

        if(!$user->email_verified_at){
            throw ValidationException::withMessages([
                'email'=>['Kérlek először erősítsd meg az email címed a belépéshez!']
            ]);
        }
       

        if(!Hash::check($request->password,$user->password)) {
                throw ValidationException::withMessages([
                'email'=> ['Hibás email cím vagy jelszó!']
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
            "message"=>"Sikeresen kijelentkeztél."
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
        //'address' =>'required|string|max:150',
        'zip' => 'required|string|max:10',
        'city' => 'required|string|max:40',
        'address_line' => 'required|string|max:70',
        'newsletter_subscribed' => 'sometimes',
        'password' => 'required|string|min:6|confirmed', // password_confirmation kell
    ]);

    // 2️⃣ User létrehozása
    $user = User::create([
        'name' => $data['name'],
        'username' => $data['username'] ?? null,
        'phone' => $data['phone'] ?? null,
        'email' => $data['email'],
        //'address' => $data['address'],
        'zip' => $data['zip'],
        'city' => $data['city'],
        'address_line' => $data['address_line'],
        'password' => Hash::make($data['password']),
    ]);

    // 3️⃣ Welcome email küldése
  /*    Mail::to($user->email)->queue(new WelcomeMail($user)); */ 
  /* Mail::to($user->email)->send(new WelcomeMail($user)); */

    // 4️⃣ Auth token létrehozása
    $token = $user->createToken('auth_token')->plainTextToken;
    // email verification küldés
    //$user->sendEmailVerificationNotification(); 


 
    app(VerificationMailService::class)->send($user);

    return response()->json([
        'message' => 'User registered successfully',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ], 201);
}

    ///Change email address
    ///Change email address
    ///Change email address
  /*   public function requestChangeEmailAddress(Request $request){
        
          $request->validate([
              "new_email" =>'required|email|unique:users,email'
              ]);
              
            $user=$request->user();
              
            $token=Str::random(64);


            $user->emailChanges()->create([
            'new_email' => $request->new_email,
            'token' => $token,
            'expires_at' => now()->addMinutes(60),
                ]);

            Mail::to($user->email)->send(new EmailConfirmationMail($token));

            return response()->json([
                "message" =>"Megerősítő email elküldve a jelenlegi email címre"
            ]);
    } */



public function requestChangeEmailAddress(Request $request, BrevoMailService $mail)
{
    $request->validate([
        "new_email" => 'required|email|unique:users,email'
    ]);

    $user = $request->user();

    $token = Str::random(64);

    $record = $user->emailChanges()->create([
        'new_email' => $request->new_email,
        'token' => $token,
        'expires_at' => now()->addMinutes(60),
    ]);

    // 🔥 LINK
    $url = url("/api/email/change/confirm/{$token}");

    // 🔥 EMAIL HTML
    $html = "
        <h2>Email változtatás</h2>
        <p>Kattints a megerősítéshez:</p>
        <a href='{$url}'>Email módosítása</a>
        <p>1 óráig érvényes</p>
    ";

    // 🔥 KÜLDÉS (régi emailre!)
    $mail->send(
        $user->email,
        $user->name,
        'Email cím módosítás megerősítése',
        $html
    );

    return response()->json([
        "message" => "Megerősítő email elküldve a jelenlegi email címre"
    ]);
}
///confirm email change
///confirm email change
///confirm email change
/*     public function confirmEmailChange(Request $request)
{
    $record = EmailChange::
        where('token', $request->token)
        ->where('expires_at', '>', now())
        ->first();

    if (!$record) {
        return response()->json(['message' => 'Invalid or expired token'], 400);
    }

    $user = User::findOrFail($record->user_id);

    $user->email = $record->new_email;
    $user->save();

    EmailChange::where('id', $record->id)->delete();

    return response()->json(['message' => 'Email updated successfully']);
}
     */
    public function confirmEmailChange($token) {
    $record = EmailChange::with('user')
            ->where('token', $token)
            ->firstOrFail();

        if ($record->expires_at < now()) {
            return response()->json(['message' => 'Token expired'], 403);
        }

        $user = $record->user;

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->email = $record->new_email;
        $user->save();

        $record->delete();

        return response()->json([
            'message' => 'Email cím sikeresen frissítve!'
        ]);

    }
}
