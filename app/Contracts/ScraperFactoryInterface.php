<?php

namespace App\Contracts;

interface ScraperFactoryInterface
{
    /**
     * Resolves instance of a ScraperInterface using a driver name mapped in scrapers.php config.
     *
     * @param string $driver
     * @return ScraperInterface
     */
    public static function make(string $driver): ScraperInterface;

    /**
     * Attempts to resolve a matching ScraperInterface by comparing a given URL to the base_url in config.
     *
     * @param string $url
     * @return ScraperInterface
     */
    public static function resolveFromUrl(string $url): ScraperInterface;
}
