<?php

return [
    'ffmpeg' => [
        'output_path' => env('FFMPEG_OUTPUT_PATH'),
        'log_path'    => env('FFMPEG_LOG_PATH'),
    ],

    'auth' => [
        'username' => env('SCRAPER_USERNAME'),
        'password' => env('SCRAPER_PASSWORD'),
        'url' => env('SCRAPER_LOGIN_URL')
    ],
];
