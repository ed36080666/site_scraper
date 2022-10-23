<?php

namespace App\Scrapers;

use App\Contracts\ScraperInterface;
use App\Jobs\ProcessVideo;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PornHubScraper extends DuskTestCase implements ScraperInterface
{
    public function scrape(string $video_url, string $filename): void
    {
        // override storage locations for logs and screenshots because it attempts to put it at the system's
        // root "/" directory and throws a permission denied exception.
        Browser::$storeScreenshotsAt = storage_path('logs/dusk/screenshots');
        Browser::$storeConsoleLogAt = storage_path('logs/dusk/console');

        // the resolutions we'll be looking to scrape. will iterate list & take the highest resolution available.
        $resolutions = collect([
            '2160', '1440', '1080', '720', '480'
        ]);

        $this->browse(function (Browser $browser) use ($resolutions, $video_url, $filename) {
            $browser->visit($video_url);

            $video_id = $browser->attribute('#player', 'data-video-id');
            $data_key = "flashvars_$video_id";
            $video_data = $browser->script("return window['$data_key']");
            $media_data = collect($video_data[0]['mediaDefinitions']);

            $video_url = null;
            foreach ($resolutions as $resolution) {
                $video_definition = $media_data->first(function ($item) use ($resolution, $video_url) {
                    return $item['quality'] === $resolution;
                });

                if ($video_definition) {
                   $video_url = $video_definition['videoUrl'];
                   break;
                }
            }

            if (is_null($video_url)) {
                throw new \Exception('Could not find valid video URL for the supported resolutions.');
            }

            ProcessVideo::dispatch($video_url, "$filename.mp4", true);

            $browser->quit();
        });
    }
}
