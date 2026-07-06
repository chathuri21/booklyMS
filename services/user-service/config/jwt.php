<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Settings
    |--------------------------------------------------------------------------
    |
    | The secret is shared with the API gateway, which verifies token
    | signatures locally without calling back to this service.
    |
    */

    'secret' => env('JWT_SECRET'),

    'algo' => 'HS256',

    // Token lifetime in minutes
    'ttl' => (int) env('JWT_TTL', 60),

    'issuer' => env('JWT_ISSUER', 'user-service'),

];
