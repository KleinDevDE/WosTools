<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bouncer Tables
    |--------------------------------------------------------------------------
    |
    | Bouncer uses several tables to store abilities and their relationships.
    | You may change the table names here if needed.
    |
    */

    'tables' => [
        'abilities' => 'abilities',
        'assigned_roles' => 'assigned_roles',
        'permissions' => 'permissions',
        'roles' => 'roles',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bouncer Caching
    |--------------------------------------------------------------------------
    |
    | By default, Bouncer will cache all abilities for fast access. Here you
    | may configure the cache expiration time in minutes.
    |
    */

    'cache' => [
        'store' => 'default',
        'ttl' => 60 * 24, // 24 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model Resolver
    |--------------------------------------------------------------------------
    |
    | This resolver function returns the current user that Bouncer should
    | check abilities for. For our multi-character system, we return the
    | active character instead of the user.
    |
    */

    'user_resolver' => function () {
        // For character guard authentication
        if (auth()->guard('character')->check()) {
            return auth()->guard('character')->user();
        }

        // Fallback to web guard with active character
        if (auth()->guard('web')->check()) {
            $user = auth()->guard('web')->user();
            return $user->characters()->find(session('active_character_id'));
        }

        return null;
    },

    /*
    |--------------------------------------------------------------------------
    | Bouncer Ownership
    |--------------------------------------------------------------------------
    |
    | This controls how Bouncer determines ownership of models. By default,
    | Bouncer uses the user's ID. Here we customize it for characters.
    |
    */

    'custom_authority' => null,
];
