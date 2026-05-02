<?php

namespace App\Services;

use App\Services\BrevoMailService;

class SendOrderConfirmMail {

    public function send($user, $order,$items)
    {
        $html = view('confirmMail', [
            'user' => $user,
            'order' => $order,
            'items' =>$items
        ])->render();

        app(BrevoMailService::class)->send(
            $user->email,
            $user->name,
            'Order confirmation',
            $html
        );
    }
}