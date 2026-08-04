<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kimia write preparation
    |--------------------------------------------------------------------------
    |
    | Write execution remains disabled. An operation may only be prepared when
    | it is explicitly registered from owner-confirmed API evidence. Do not add
    | endpoints, action codes or payload fields from assumptions.
    |
    */
    'enabled' => env('KIMIA_WRITE_ENABLED', false),

    'operations' => [
        // Intentionally empty until real Kimia payloads and mappings are approved.
    ],
];
