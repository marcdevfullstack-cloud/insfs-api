<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Origines autorisées — plusieurs URLs séparées par des virgules dans .env
    |--------------------------------------------------------------------------
    | Exemple : FRONTEND_URL=http://localhost:3000,https://insfs-gestion.vercel.app
    */
    'allowed_origins' => array_values(array_filter(
        array_map('trim', explode(',', env('FRONTEND_URL', 'http://localhost:3000')))
    )),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
