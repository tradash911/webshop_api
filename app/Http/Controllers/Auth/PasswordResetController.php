<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    // 1️⃣ Email küldése a reset linkhez
    public function sendResetLink(Request $request)
    {
    
        $request->validate([
            'email'=> 'required|email'
        ]) ;
        
        $user = User::where('email',$request->email)->first();
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email'=> $user->email],
            ['token'=>hash('sha256',$token),
            'created_at' => now()
            ]
        );

        $url = config('app.frontend_url') . "/reset-password?token=$token&email=" . urlencode($user->email);

            $html = "
        <h1 >Password reset</h1>
        <p>Kattints ide:</p>
        <a href='$url'>Reset password</a>
    ";

    app(BrevoMailService::class)->send(
        $user->email,
        $user->name,
        'Password reset',
        $html
    );

    return response()->json([
        'message' => 'Helyreállító sikeresen elküldve a megadott email címre.'
    ]);
    }

    // 2️⃣ Jelszó reset a tokennel
    public function resetPassword(Request $request)
    {
  
            $request->validate([
                'token' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string|confirmed|min:8',
            ]);

             $record = DB::table('password_reset_tokens')
             ->where('email', $request->email)
             ->first();

              if (!$record || !hash_equals($record->token, hash('sha256', $request->token))) {
               return response()->json(['message' => 'Invalid token'], 400);
             }

             if (now()->diffInMinutes($record->created_at) > 60) {
             return response()->json(['message' => 'Token expired'], 403);
            }

            $user = User::where('email', $request->email)->first();

            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

             return response()->json(['message' => 'Jelszó sikeresen frissítve!']);
    }
}