pre-reqs
1. ffmpeg needs installed
2. greater or equal php7.2 
3. chrome needs to be installed

steps
1. Clone the repo
2. Composer install
3. php artisan dusk:chrome-driver
4. npm install
5. cp .env.example .env
   1. Update FFMPEG_OUTPUT_PATH
   2. Update FFMPEG_OUTPUT_PATH (this is used for getting progress)
6. php artisan key generate
7. touch <root>/database/database.sqlite
8. php artisan migrate
9. build dependencies (npm run dev)
10. php artisan serve
11. Must start a queue worker
    1. todo: provide some info about running multiple (maybe also supervisor?)


Troubleshooting:
1. Exceptions in chrome web driver 
   1. Ensure chrome installed on system
   2. (see https://laravel.com/docs/7.x/dusk)
