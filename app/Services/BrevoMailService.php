<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMailService
{
    public function send($toEmail, $toName, $subject, $html)
    {
        return Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => 'valaki',
                'email' => 'tradash@gmail.com',
            ],
            'to' => [
                [
                    'email' => $toEmail,
                    'name' => $toName,
                ]
            ],
            'subject' => $subject,
            'htmlContent' => $html,
        ]);
    }
}