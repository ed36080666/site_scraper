# Porn Site Scraper
A web based scraper for downloading videos from various porn sites. Launches
a web driver for scraping and can be configured to handle authentication
for videos hidden behind logins.

**Currently supported sites**
1. Porntrex (porntrex.com)
2. PornWild (pornwild.com)

## Installation
### Pre-reqs
1. \>=PHP 7.2
2. [Chrome](https://www.google.com/chrome/) browser installed on host system
3. [FFmpeg](https://ffmpeg.org/) installed on host browser

### Installation steps
1. Clone the repository 
<br>`git clone https://github.com/ed36080666/site_scraper.git`
2. Install PHP dependencies
<br>`composer install`
3. Install Laravel Dusk chrome driver
<br>`php artisan dusk:chrome-driver`
4. Install frontend dependencies
<br> `npm install`
5. Copy and configure `.env`
<br>`cp .env.example .env`
   1. Set full system path for `FFMPEG_OUTPUT_PATH` variable in `.env`. This determines where saved videos are stored.
   2. Set full system path for `FFMPEG_LOG_PATH` variable in `.env`. This determines where FFmpeg will store log files.
6. Generate Laravel application key
<br> `php artisan key:generate`
7. Create the base SQLite database
<br> `touch ./database/database.sqlite`
8. Run database migrations
<br> `php artisan migrate`
9. Build frontend assets
<br>`npm run dev`
10. Start a queue worker (handles scraping jobs in background)
<br>`php artisan queue:work`
11. Start the application
    <br>`php artisan serve`

### Running queue workers
todo...

### Troubleshooting
1. Chrome Web Driver exceptions
   1. Ensure Chrome is installed on the host system
   2. Ensure Laravel Dusk Chrome driver binary is installed
      1. Visit [Laravel Dusk](https://github.com/ed36080666/site_scraper.git) docs for more info
