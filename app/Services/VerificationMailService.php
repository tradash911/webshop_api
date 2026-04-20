<?php

namespace App\Services;

use Illuminate\Support\Facades\URL;

class VerificationMailService
{
    public function send($user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $html = view('verify', [
            'url' => $verificationUrl,
            'user' => $user
        ])->render();

        app(BrevoMailService::class)->send(
            $user->email,
            $user->name,
            'Email verification',
            $html
        );
    }
}