<?php

namespace App\Contracts;

interface ScraperInterface
{
    /**
     * Performs a scrape action on a given URL. Should locate a URL
     * directly to a video and launch the ProcessVideo job.
     *
     * @param string $video_url
     * @param string $filename
     * @return void
     */
    public function scrape(string $video_url, string $filename): void;
}
