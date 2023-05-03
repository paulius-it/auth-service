<?php

return [
    'providers' => [
    'lp_express' => [
        'api_access_key'        => env('LP_EXPRESS_API_ACCESS_KEY'),
        'api_secret'            => env('LP_EXPRESS_API_SECRET'),
    ],
    'omniva'    => [
        'api_access_key'        => env('OMNIVA_API_ACCESS_KEY'),
    'api_secret'            => env('OMNIVA_API_SECRET'),
    ],
    ],
];
