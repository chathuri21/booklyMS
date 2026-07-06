<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Settings
    |--------------------------------------------------------------------------
    |
    | The secret is shared with the user service, which issues the tokens.
    | The gateway verifies signatures locally - no network call needed.
    |
    */

    'secret' => env('JWT_SECRET'),

    'algo' => 'HS256',

];
