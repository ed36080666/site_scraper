<?php

namespace App\Scrapers;

use App\Contracts\ScraperInterface;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class PornPicsScraper extends DuskTestCase implements ScraperInterface
{
    /**
     * Scrape a gallery of pictures from PornPics into a directory.
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

            $browser->quit();
        });
    }
}
