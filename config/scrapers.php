<?php

return [

    /*
    |--------------------------------------------------------------------------
    | FFmpeg
    |--------------------------------------------------------------------------
    |
    | Determines where scraped videos and FFmpeg logs are stored. Ensure these
    | are full system paths that already exist and have correct permissions.
    |
    */
    'ffmpeg' => [
        'output_path' => env('FFMPEG_OUTPUT_PATH'),
        'log_path'    => env('FFMPEG_LOG_PATH'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Drivers
    |--------------------------------------------------------------------------
    |
    | Configurations for the various scrapers that are supported. Each driver
    | will contain some metadata, optional authentication credentials, and a
    | mapping to the Laravel Dusk class handling the scraping.
    |
    */

    'drivers' => [
        'porntrex' => [
            'display_name'  => 'PornTrex',
            'base_url'      => 'porntrex.com',
            'scraper'       => \App\Scrapers\PorntrexScraper::class,
            'auth' => [
                'username'  => env('PORNTREX_USERNAME', null), // optional
                'password'  => env('PORNTREX_PASSWORD', null), // optional
                'login_url' => env('PORNTREX_LOGIN_URL', null), // optional
            ]
        ],

        'pornwild' => [
            'display_name'  => 'PornWild',
            'base_url'      => 'pornwild.com',
            'scraper'       => \App\Scrapers\PornwildScraper::class,
            'auth' => [
                'username'  => env('PORNWILD_USERNAME', null), // optional
                'password'  => env('PORNWILD_PASSWORD', null), // optional
                'login_url' => env('PORNWILD_LOGIN_URL', null), // optional
            ]
        ],

        'pornktube' => [
            'display_name' => 'PornKTube',
            'base_url'     => 'pornktube.tv',
            'scraper'      => \App\Scrapers\PornKTubeScraper::class,
            'auth' => [
                'username'  => env('PORNKTUBE_USERNAME', null), // optional
                'password'  => env('PORNKTUBE_PASSWORD', null), // optional
                'login_url' => env('PORNKTUBE_LOGIN_URL', null), // optional
            ]
        ],

        'hqporner' => [
            'display_name' => 'HQ Porner',
            'base_url'     => 'hqporner.com',
            'scraper'      => \App\Scrapers\HQPornerScraper::class,
            'auth' => [
                'username'  => env('HQPORNER_USERNAME', null), // optional
                'password'  => env('HQPORNER_PASSWORD', null), // optional
                'login_url' => env('HQPORNER_LOGIN_URL', null), // optional
            ]
        ]
    ]
];
