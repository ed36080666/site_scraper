<?php

namespace App\Scrapers;

use App\Contracts\ScraperInterface;
use App\Jobs\ProcessVideo;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FullPornerScraper extends DuskTestCase implements ScraperInterface
{
    const RESOLUTIONS = ['2160', '1440', '1080', '720', '480', '360'];

    public function scrape(string $url, string $filename): void
    {
        // override storage locations for logs and screenshots because it attempts to put it at the system's
        // root "/" directory and throws a permission denied exception.
        Browser::$storeScreenshotsAt = storage_path('logs/dusk/screenshots');
        Browser::$storeConsoleLogAt = storage_path('logs/dusk/console');

        $this->browse(function (Browser $browser) use ($url, $filename) {
            $browser->visit($url);

            $video_node = $browser->element('.single-video iframe');
            $video_url = $video_node->getAttribute('src');

            // the first 2 characters of scraped url are '//'. we need to append
            // https: for a valid path to give FFmpeg.
            $cdn_url = "https:$video_url";

            $browser->visit($cdn_url);

            $source_nodes = $browser->elements('#flvv source');
            $highest_resolution_node = $this->findHighestResolution($source_nodes);

            $cdn_video_url = 'https:' . $highest_resolution_node->getAttribute('src');

            ProcessVideo::dispatch($cdn_video_url, "$filename.mp4");

            $browser->quit();
        });
    }

    private function findHighestResolution(array $dom_nodes)
    {
        // iterate all resolutions starting at highest. each video has multiple DOM nodes containing
        // resolutions stored in the title attribute. look for highest resolution and return associated node.
        foreach (self::RESOLUTIONS as $resolution) {
            foreach ($dom_nodes as $node) {
                if (str_contains($node->getAttribute('title'), $resolution)) {
                    return $node;
                }
            }
        }

        throw new \Exception('Unable to find <source> DOM node for supported resolutions.');
    }
}
