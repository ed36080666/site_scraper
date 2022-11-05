<?php

namespace App\Scrapers;

use App\Contracts\ScraperInterface;
use App\Jobs\ProcessVideo;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class PornKTubeScraper extends DuskTestCase implements ScraperInterface
{
    const BASE_CDN_URL = 'stormedia.info/whpvid/';

    /**
     * Scrape a CDN video URL from PornWild and dispatch FFmpeg processing job.
     * @param string $url
     * @param string $filename
     * @return void
     * @throws Throwable
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

            // there are hidden divs that act as buttons for each resolution. these contain
            // all the url parts in a semi-colon delimited string in the `data-c` attribute.
            // the first child here will always be the highest available resolution.
            $data_url = $browser->attribute('.listlinks > div', 'data-c');
            $url_parts = explode(';', $data_url);
            $url = $this->buildUrlFromParts($url_parts);

            ProcessVideo::dispatch($url, "$filename.mp4");

            $browser->quit();
        });
    }

    private function buildUrlFromParts(array $url_parts)
    {
        // example url: https://s2.stormedia.info/whpvid/1657432818/Ntw9EdVYIRsyxZJAOuPL4A/18000/18248/18248_480p.mp4
        // reconstruct the direct CDN url using parts scraped from the DOM. first we
        // have to pull index 7. this will be an integer value typically `2` or `3` to
        // map to the `s2` or `s3` before the base url.
        $url = "https://s$url_parts[7]." . self::BASE_CDN_URL;

        // next we add an integer stored in part 5
        $url .= $url_parts[5] . '/';

        // then we add a hash stored in part 6
        $url .= $url_parts[6] . '/';

        // then we take the video id stored in part 4 and strip the first
        // 2 values and pad with 3x 0s (e.g. 18248 = 18000)
        $url .= substr($url_parts[4], 0, 2) . '000' . '/';

        // then we add the video id
        $url .= $url_parts[4] . '/';

        // then we create the filename which is always: <video id>_<resolution>.mp4
        // unless resolution is 720, then it is just <video id>.mp4
        if ($url_parts[1] === '720p') {
            $url .= $url_parts[4] . '.mp4';
        } else {
            $url .= $url_parts[4] . '_' . $url_parts[1] . '.mp4';
        }

        return $url;
    }
}
