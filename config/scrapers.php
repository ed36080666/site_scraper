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
    | Photo Galleries
    |--------------------------------------------------------------------------
    |
    | Determines where scraped photo galleries and scraping logs are stored. Ensure these
    | are full system paths that already exist and have correct permissions.
    |
    */
    'photo_gallery' => [
        'output_path' => env('PHOTO_OUTPUT_PATH'),
        'log_path' => env('PHOTO_LOG_PATH'),
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
            'logo_filename' => 'porntrex.png',
            'scraper'       => \App\Scrapers\PorntrexScraper::class,
            'auth' => [
                'username'  => env('PORNTREX_USERNAME', null), // optional
                'password'  => env('PORNTREX_PASSWORD', null), // optional
                'login_url' => env('PORNTREX_LOGIN_URL', null), // optional
            ]
        ],

        'fullporner' => [
            'display_name'  => 'FullPorner',
            'base_url'      => 'fullporner.com',
            'logo_filename' => 'fullporner.png',
            'scraper'       => \App\Scrapers\FullPornerScraper::class,
        ],

        'hqporner' => [
            'display_name'  => 'HQ Porner',
            'base_url'      => 'hqporner.com',
            'logo_filename' => 'hqporner.png',
            'scraper'       => \App\Scrapers\HQPornerScraper::class,
            'auth' => [
                'username'  => env('HQPORNER_USERNAME', null), // optional
                'password'  => env('HQPORNER_PASSWORD', null), // optional
                'login_url' => env('HQPORNER_LOGIN_URL', null), // optional
            ]
        ],

        'pornhub' => [
            'display_name'  => 'PornHub',
            'base_url'      => 'pornhub.com',
            'logo_filename' => 'pornhub.png',
            'scraper'       => \App\Scrapers\PornHubScraper::class,
            'auth' => [
                'username'  => env('PORNHUB_USERNAME', null), // optional
                'password'  => env('PORNHUB_PASSWORD', null), // optional
                'login_url' => env('PORNHUB_LOGIN_URL', null), // optional
            ]
        ],

        'pornpics' => [
            'display_name'  => 'PornPics',
            'base_url'      => 'pornpics.com',
            'logo_filename' => 'pornpics.png',
            'scraper'       => \App\Scrapers\PornPicsScraper::class,
            'auth' => [
                'username'  => env('PORNPICS_USERNAME', null), // optional
                'password'  => env('PORNPICS_PASSWORD', null), // optional
                'login_url' => env('PORNPICS_LOGIN_URL', null), // optional
            ]
        ],

        'pichunter' => [
            'display_name'  => 'Pic Hunter',
            'base_url'      => 'pichunter.com',
            'logo_filename' => 'pichunter.png',
            'scraper'       => \App\Scrapers\PicHunterScraper::class,
            'auth' => [
                'username'  => env('PICHUNTER_USERNAME', null), // optional
                'password'  => env('PICHUNTER_PASSWORD', null), // optional
                'login_url' => env('PICHUNTER_LOGIN_URL', null), // optional
            ]
        ],

        'whoreshub' => [
            'display_name'  => 'WhoresHub',
            'base_url'      => 'whoreshub.com',
            'logo_filename' => 'whoreshub.png',
            'scraper'       => \App\Scrapers\WhoresHubScraper::class,
            'auth' => [
                'username'  => env('WHORESHUB_USERNAME', null), // optional
                'password'  => env('WHORESHUB_PASSWORD', null), // optional
                'login_url' => env('WHORESHUB_LOGIN_URL', null), // optional
            ]
        ],

        'youjizz' => [
            'display_name'  => 'YouJizz',
            'base_url'      => 'youjizz.com',
            'logo_filename' => 'youjizz.png',
            'scraper'       => \App\Scrapers\YouJizzScraper::class,
            'auth' => [
                'username'  => env('WHORESHUB_USERNAME', null), // optional
                'password'  => env('WHORESHUB_PASSWORD', null), // optional
                'login_url' => env('WHORESHUB_LOGIN_URL', null), // optional
            ]
        ],

        'goodporn' => [
            'display_name'  => 'GoodPorn',
            'base_url'      => 'goodporn.to',
            'logo_filename' => 'goodporn.png',
            'scraper'       => \App\Scrapers\GoodPornScraper::class,
            'auth' => [
                'username'  => env('GOODPORN_USERNAME', null), // optional
                'password'  => env('GOODPORN_PASSWORD', null), // optional
                'login_url' => env('GOODPORN_LOGIN_URL', null), // optional
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | Deprecated Drivers
        |--------------------------------------------------------------------------
        |
        | The following driver configs are for sites that appear to have been shut down. These
        | were all working prior to shutdowns and the underlying scrapers have not been removed.
        | These configs will be stashed here to remove them from the UI but allow
        | quick activation if the sties come back online.
        |
        */
//        'pornktube' => [
//            'display_name'  => 'PornKTube',
//            'base_url'      => 'pornktube.tv',
//            'logo_filename' => 'pornktube.png',
//            'scraper'       => \App\Scrapers\PornKTubeScraper::class,
//            'auth' => [
//                'username'  => env('PORNKTUBE_USERNAME', null), // optional
//                'password'  => env('PORNKTUBE_PASSWORD', null), // optional
//                'login_url' => env('PORNKTUBE_LOGIN_URL', null), // optional
//            ]
//        ],

//        'pornwild' => [
//            'display_name'  => 'PornWild',
//            'base_url'      => 'pornwild.com',
//            'logo_filename' => 'pornwild.png',
//            'scraper'       => \App\Scrapers\PornwildScraper::class,
//            'auth' => [
//                'username'  => env('PORNWILD_USERNAME', null), // optional
//                'password'  => env('PORNWILD_PASSWORD', null), // optional
//                'login_url' => env('PORNWILD_LOGIN_URL', null), // optional
//            ]
//        ],
    ]
];
