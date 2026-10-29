<?php

return [
    /*
    |--------------------------------------------------------------------------
    | M-Pesa Environment Mode
    |--------------------------------------------------------------------------
    | Options: 'sandbox' or 'live'
    */
    'env' => env('MPESA_ENV', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | API Base URLs
    |--------------------------------------------------------------------------
    */
    'sandbox_url' => 'https://sandbox.safaricom.co.ke',
    'live_url' => 'https://api.safaricom.co.ke',

    /*
    |--------------------------------------------------------------------------
    | Daraja API Credentials (Consumer Key & Secret)
    |--------------------------------------------------------------------------
    | Generated from developer.safaricom.co.ke
    */
    'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Lipa na M-Pesa (STK Push) Settings
    |--------------------------------------------------------------------------
    | 174379 is the default Safaricom Daraja sandbox shortcode
    */
    'shortcode' => env('MPESA_SHORTCODE', '174379'),
    'passkey' => env('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'),
    'transaction_type' => env('MPESA_TRANSACTION_TYPE', 'CustomerPayBillOnline'), // or CustomerBuyGoodsOnline

    /*
    |--------------------------------------------------------------------------
    | Webhook CallBack URLs
    |--------------------------------------------------------------------------
    */
    'callback_url' => env('MPESA_CALLBACK_URL', env('APP_URL') . '/api/mpesa/stk-callback'),

    /*
    |--------------------------------------------------------------------------
    | B2C Payout / Withdrawal Settings
    |--------------------------------------------------------------------------
    */
    'b2c_shortcode' => env('MPESA_B2C_SHORTCODE', '600980'),
    'b2c_initiator_name' => env('MPESA_B2C_INITIATOR', 'testapi'),
    'b2c_security_credential' => env('MPESA_B2C_SECURITY_CREDENTIAL', ''),
    'b2c_result_url' => env('MPESA_B2C_RESULT_URL', env('APP_URL') . '/api/mpesa/b2c-result'),
    'b2c_timeout_url' => env('MPESA_B2C_TIMEOUT_URL', env('APP_URL') . '/api/mpesa/b2c-timeout'),
];
