<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Profile Picture Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for profile picture handling in both development and production
    |
    */

    'storage_disk' => env('PROFILE_PICTURE_DISK', 'public'),
    
    'storage_path' => env('PROFILE_PICTURE_PATH', 'profile_pictures'),
    
    'max_file_size' => env('PROFILE_PICTURE_MAX_SIZE', 2048), // KB
    
    'allowed_mimes' => [
        'image/jpeg',
        'image/png', 
        'image/jpg',
        'image/gif',
        'image/webp'
    ],
    
    'allowed_extensions' => [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp'
    ],
    
    // URL generation settings
    'url_generation' => [
        'use_https' => env('PROFILE_PICTURE_USE_HTTPS', true),
        'force_absolute' => env('PROFILE_PICTURE_FORCE_ABSOLUTE', true),
        'cache_busting' => env('PROFILE_PICTURE_CACHE_BUSTING', false),
    ],
    
    // Fallback settings
    'fallback' => [
        'enabled' => env('PROFILE_PICTURE_FALLBACK_ENABLED', true),
        'default_avatar' => env('PROFILE_PICTURE_DEFAULT_AVATAR', '/images/default-avatar.png'),
    ]
];
