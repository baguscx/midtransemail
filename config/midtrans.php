<?php

return [
    // Set your Merchant Server Key
    'serverKey' => env('MIDTRANS_SERVER_KEY'),
    // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
    'isProduction' => env('MIDTRANS_IS_PRODUCTION'),
    // Set sanitization on (default)
    'isSanitized' => env('MIDTRANS_IS_SANITIZED'),
    // Set 3DS transaction for credit card to true
    'is3ds' => env('MIDTRANS_IS_3DS'),
];
