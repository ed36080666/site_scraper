<?php

namespace App\Scrapers;

use App\Contracts\ScraperInterface;
use App\Jobs\ProcessVideo;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GoodPornScraper extends DuskTestCase implements ScraperInterface
{
    public function scrape(string $url, string $filename): void
    {
        // override storage locations for logs and screenshots because it attempts to put it at the system's
        // root "/" directory and throws a permission denied exception.
        Browser::$storeScreenshotsAt = storage_path('logs/dusk/screenshots');
        Browser::$storeConsoleLogAt = storage_path('logs/dusk/console');

        // the resolutions we'll be looking to scrape. will iterate list & take the highest resolution available.
        $resolutions = collect([
            '2160p', '1440p', '1080p', '720p', '480p'
        ]);

        // unique ffmpeg args for removing the site watermark.
        // position of the watermark varies by resolution.
        $watermark_stripper_args = [
            '2160' => '-vf delogo=x=2:y=4:w=620:h=160',
            '1080' => '-vf delogo=x=3:y=6:w=320:h=70',
        ];

        $this->browse(function (Browser $browser) use ($resolutions, $url, $filename, $watermark_stripper_args) {
            $browser->visit($url);

            $flashvars = $browser->script("return window.flashvars");

            // filters flashvars to only the items that are related to video urls
            $urls = collect(array_filter($flashvars[0], function($key) {
                return strpos($key, 'video_alt_url') === 0;
            }, ARRAY_FILTER_USE_KEY));

            foreach($resolutions as $resolution) {
                // starting with largest resolution, see if we have any video urls that
                // contain the resolution.
                $key = $urls->search(function ($item) use ($resolution) {
                    return str_contains($item, $resolution);
                });

                if ($key) {
                    break;
                }
            }

            if (!isset($key)) {
                throw new \Exception('Could not find video URL for any supported resolutions.');
            }

            $url = $flashvars[0][$key];

            // attempt to find a watermark removal args
            $output_args = '-c copy';
            foreach($watermark_stripper_args as $resolution => $arg) {
                if (str_contains($url, $resolution)) {
                    $output_args = $arg;
                    break;
                }
            }

            ProcessVideo::dispatch($url, "$filename.mp4", false, '', $output_args);
        });
    }
}
