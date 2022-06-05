<?php

namespace Tests\Browser;

use App\Jobs\ProcessVideo;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\ExpectationFailedException;
use Tests\DuskTestCase;

class ScrapePwildUrls extends DuskTestCase
{
    protected $url;

    protected $filename;

    public function __construct($url, $filename, $name = null, array $data = [], $dataName = '')
    {
        $this->url = $url;
        $this->filename = $filename;
        parent::__construct($name, $data, $dataName);
    }

    public function scrape()
    {
        // override storage locations for logs and screenshots because it attempts to put it at the system's
        // root "/" directory and throws a permission denied exception.
        Browser::$storeScreenshotsAt = storage_path('logs/dusk/screenshots');
        Browser::$storeConsoleLogAt = storage_path('logs/dusk/console');

        // begin scraping:
        $this->browse(function (Browser $browser) {

            $browser->visit($this->url);

            // todo handle authentication

            $data = $browser->script("return window.flashvars");
            $data = $data[0]; // data array lives in the first item returned from javascript flashvars

            // the data will contain a series of alt_urls* for each resolution. The highest
            // alt url is the highest resolution which we always try to grab first.
            $video_url = $data['video_alt_url4'] // 4k
                ?? $data['video_alt_url3']       // 1440
                ?? $data['video_alt_url2']       // 1080
                ?? $data['video_alt_url']        // 720
                ?? $data['video_url']            // 480
                ?? null;

            if (!$video_url) {
                dd('Cant find valid video URL in `flashvars`');
            }

            ProcessVideo::dispatch($video_url, "{$this->filename}.mp4");

            $browser->quit();
        });
    }
}
