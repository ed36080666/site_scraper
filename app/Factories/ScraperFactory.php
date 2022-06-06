<?php

namespace App\Factories;

use App\Contracts\ScraperFactoryInterface;
use App\Contracts\ScraperInterface;
use App\Exceptions\ScraperDriverNotFoundException;

class ScraperFactory implements ScraperFactoryInterface
{
    /**
     * Resolve an instance of a scraper by name.
     *
     * @param string $driver
     * @return ScraperInterface
     * @throws ScraperDriverNotFoundException
     */
    public static function make(string $driver): ScraperInterface
    {
        $drivers = config('scrapers.drivers');

        if (! array_key_exists($driver, $drivers)) {
            throw new ScraperDriverNotFoundException("Scraper driver not found: `$driver`");
        }

        $scraper = resolve($drivers[$driver]['scraper']);
        $scraper->prepare();
        return $scraper;
    }

    /**
     * Resolve an instance of a scraper by matching against URL (e.g. video url).
     *
     * @param string $url
     * @return ScraperInterface
     * @throws ScraperDriverNotFoundException
     */
    public static function resolveFromUrl(string $url): ScraperInterface
    {
        $drivers = config('scrapers.drivers');
        foreach ($drivers as $driver => $config) {
            if (str_contains($url, $config['base_url'])) {
                return self::make($driver);
            }
        }

        throw new ScraperDriverNotFoundException("Can't resolve matching driver from URL: `$url`");
    }
}
