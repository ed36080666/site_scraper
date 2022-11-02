<?php

namespace App\Scrapers;

use App\Contracts\ScraperInterface;
use App\Jobs\ProcessVideo;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Scraper for PornWild (https://pornwild.com).
 * Always attempts to grab highest resolution available.
 */
class PornwildScraper extends DuskTestCase implements ScraperInterface
{

    /**
     * Scrape a CDN video URL from PornWild and dispatch FFmpeg processing job.
     * @param string $url
     * @param string $filename
     * @return void
     * @throws \Throwable
     */
    public function scrape(string $url, string $filename): void
    {
        // override storage locations for logs and screenshots because it attempts to put it at the system's
        // root "/" directory and throws a permission denied exception.
        Browser::$storeScreenshotsAt = storage_path('logs/dusk/screenshots');
        Browser::$storeConsoleLogAt = storage_path('logs/dusk/console');

        // begin scraping:
        $this->browse(function (Browser $browser) use ($url, $filename) {

            $browser->visit($url);

            // todo handle authentication

            $data = $browser->script("return window.flashvars");
            $data = $data[0]; // data array lives in the first item returned from javascript flashvars

            // the data will contain a series of alt_urls* for each resolution. The highest
            // alt url is the highest resolution which we always try to grab first.
            $cdn_video_url = $data['video_alt_url4'] // 4k
                ?? $data['video_alt_url3']       // 1440
                ?? $data['video_alt_url2']       // 1080
                ?? $data['video_alt_url']        // 720
                ?? $data['video_url']            // 480
                ?? null;

            if (!$cdn_video_url) {
                throw new \Exception('Cant find valid video URL in `flashvars`');
            }

            ProcessVideo::dispatch($cdn_video_url, "$filename.mp4");

            $browser->quit();
        });
    }
}
