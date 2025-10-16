<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WABLAS Token
    |--------------------------------------------------------------------------
    |
    | This value is the token that get from WABLAS
    */
    'token' => env('WABLAS_TOKEN', null),

    /*
    |--------------------------------------------------------------------------
    | WABLAS URL
    |--------------------------------------------------------------------------
    |
    | This values is the URL subdomain from WABLAS.
    | If your domain is https://bandung.wablas.com, only input "bandung" as the URL
    */
    'server' => env('WABLAS_URL', 'jogja'),

    /*
    |--------------------------------------------------------------------------
    | DEBUG NUMBER
    |--------------------------------------------------------------------------
     */
    'debug_number' => env('WABLAS_DEBUG_NUMBER', null),
];
