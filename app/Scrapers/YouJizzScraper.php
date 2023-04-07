<?php

namespace App\Scrapers;

use App\Contracts\ScraperInterface;
use App\Jobs\ProcessVideo;
use Laravel\Dusk\Browser;
use Symfony\Component\Process\Process;
use Tests\DuskTestCase;

class YouJizzScraper extends DuskTestCase implements ScraperInterface
{
    public function scrape(string $url, string $filename): void
    {
        $config = config('scrapers.drivers.whoreshub');

        // override storage locations for logs and screenshots because it attempts to put it at the system's
        // root "/" directory and throws a permission denied exception.
        Browser::$storeScreenshotsAt = storage_path('logs/dusk/screenshots');
        Browser::$storeConsoleLogAt = storage_path('logs/dusk/console');

        // the resolutions we'll be looking to scrape. will iterate list & take the highest resolution available.

        $this->browse(function (Browser $browser) use ($url, $filename) {

            $browser->visit($url);

            $browser->waitFor('#yj-fluid', 5);
            $src_node = $browser->element('#yj-fluid source');

            $url = 'https:' . $src_node->getAttribute('src');

            ProcessVideo::dispatch($url, "$filename.mp4", true);
        });
    }
}
