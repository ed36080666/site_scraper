# Porn Site Scraper
A web based scraper and UI monitoring tool for downloading videos and full photo galleries from various porn sites. Launches
a web driver for scraping and can be configured to handle authentication
for scraping media hidden behind logins.

### Currently supported sites

**Videos**
1. PornHub (pornhub.com)
2. Porntrex (porntrex.com)
2. PornWild (pornwild.com)
3. PornKTube (pornktube.tv)
4. HQPorner (hqporner.com)

**Photo Galleries**
1. PornPics (pornpics.com)
2. PicHunter (pichunter.com)

## Usage
1. Visit one of the supported sites and navigate to a desired video.
2. Copy the URL from the site and paste it into the web UI input and add a name to be used for saved file.
3. Click scrape button to launch the job and monitor progress in the UI.

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
To get the most out of this application, you should leverage the Laravel worker queue. The best way to do this is by running queue workers in the background using [Supervisor](http://supervisord.org/installing.html). Supervisor will launch a given number of worker threads and keep them running.

1. Install supervisor
<br>`sudo apt update && sudo apt install supervisor`
2. Create a new config file for our workers:
<br>`sudo vim /etc/supervisor/conf.d/site_scraper_worker.conf`

```
[program:site_scraper_worker]
process_name=%(program_name)s_%(process_num)02d
# cstomize system path to root of the site_scraper directory
command=php /var/www/vhosts/site_scraper/artisan queue:work --tries=1 --timeout=7000
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
redirect_stderr=true
stopwaitsecs=7201
user=# set appropriate system user
numprocs=8 # Can add more or fewer works based on your hardware, network etc.
stdout_logfile=# Customize this to wherever you want to place your queue worker logs.
```
3. Reread the config files and update supervisor
<br>`sudo supervisorctl reread`
<br>`sudo supervisorctl update`
4. Check the workers are running
<br>`sudo supervisorctl update`
<br><br>You should see something along the following:
```
site_scraper_worker:site_scraper_worker_00   RUNNING   pid 20567, uptime 0:02:55
... 1 entry for each worker
```

### Troubleshooting
1. Chrome Web Driver exceptions
   1. Ensure Chrome is installed on the host system
   2. Ensure Laravel Dusk Chrome driver binary is installed
      1. Visit [Laravel Dusk](https://github.com/ed36080666/site_scraper.git) docs for more info
  2. Out of date errors. Sometimes Laravel Dusk will install a version of the Chrome driver that requires a higher version of the Chrome binary than what is installed on the system. If you see errors about unsupported versions during scraping, try updating the Chrome binary to a higher version (aka re-install/update Chrome browser).
2. Permission errors
    1. Ensure ffmpeg binary has execute permissions allowing server to launch processes
    2. Ensure server has write permissions to the video output directory
    3. Ensure server has write permissions to all the log directories
