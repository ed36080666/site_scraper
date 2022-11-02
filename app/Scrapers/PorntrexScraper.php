<?php

namespace App\Scrapers;

use App\Contracts\ScraperInterface;
use App\Jobs\ProcessVideo;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\ExpectationFailedException;
use Tests\DuskTestCase;

/**
 * Scraper for PornTrex (https://porntrex.com).
 * Always attempts to grab highest resolution available.
 */
class PorntrexScraper extends DuskTestCase implements ScraperInterface
{
    /**
     * A Dusk test example.
     */
    public function scrape(string $url, string $filename): void
    {
        $config = config('scrapers.drivers.porntrex');

        // override storage locations for logs and screenshots because it attempts to put it at the system's
        // root "/" directory and throws a permission denied exception.
        Browser::$storeScreenshotsAt = storage_path('logs/dusk/screenshots');
        Browser::$storeConsoleLogAt = storage_path('logs/dusk/console');

        // the resolutions we'll be looking to scrape. will iterate list & take the highest resolution available.
        $resolutions = collect([
            '2160p', '1440p', '1080p', '720p', '480p'
        ]);

        // begin scraping:
        $this->browse(function (Browser $browser) use ($config, $resolutions, $url, $filename) {

            $browser->visit($url);

            // determines if login is required because video is private.
            $login_required = true;
            try {
                $browser->assertSee("This video is a private video");
            } catch (ExpectationFailedException $e) {
                $login_required = false;
            }

            if ($login_required) {
                // navigate to & enter credentials for login form.
                $browser->visit($config['auth']['login_url']);
                $browser->type('#login_username', $config['auth']['username']);
                $browser->type('#login_pass', $config['auth']['password']);
                $browser->press('Log in');

                // login always redirects to home so re-visit the original page and continue script.
                $browser->visit($url);
            }

            // video urls are stored in global js object "flashvars"
            $data = $browser->script("return window.flashvars;");

            // filters data to only the items that are related to video urls
            $urls = collect(array_filter($data[0], function($key) {
                return strpos($key, 'video_alt_url') === 0;
            }, ARRAY_FILTER_USE_KEY));

            // iterate each of the resolutions we'll process in descending
            // quality order (best is first choice).
            foreach ($resolutions as $resolution) {
                $url_key = $urls->search(function ($item, $key) use ($resolution) {
                    return str_contains($item, $resolution . '.mp4');
                });

                if ($url_key) {
                    break;
                }
            }

            if (!$url_key) {
                throw new \Exception('Could not find video URL for any supported resolutions.');
            }

            ProcessVideo::dispatch($urls[$url_key], "$filename.mp4");

            $browser->quit();
        });
    }
}
