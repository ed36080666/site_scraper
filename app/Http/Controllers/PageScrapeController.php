<?php

namespace App\Http\Controllers;

use App\DTOs\ScrapeItemDTO;
use App\Factories\ScraperFactory;
use App\ScrapeItem;
use Illuminate\Http\Request;

class PageScrapeController extends Controller
{
    public function index(Request $request)
    {
        $scrape_items = ScrapeItem::orderBy('started_at', 'DESC')->get();

        $logo_map = array_values(array_map(function ($driver_config) {
            return [
                'src'      => asset("storage/logos/{$driver_config['logo_filename']}"),
                'base_url' => $driver_config['base_url']
            ];
        }, config('scrapers.drivers')));

        return view('welcome', [
            'logo_map' => $logo_map,
            'scrape_items' => $scrape_items->map(function (ScrapeItem $item) {
                return (new ScrapeItemDTO($item->scrapable))->toArray();
            })
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\ScraperDriverNotFoundException
     */
    public function store(Request $request)
    {
        $scraper = ScraperFactory::resolveFromUrl($request->video_url);
        $scraper->scrape($request->video_url, $request->filename);

        return redirect()->to('/');
    }

    public function destroy($id)
    {
        $item = ScrapeItem::findOrFail($id);

        $scrapable = $item->scrapable;

        if ($item->log_path && file_exists($item->log_path)) {
            unlink($item->log_path);
        }

        if ($item->path && file_exists($item->buildFilepath())) {
            unlink($item->buildFilepath());
        }

        $scrapable->delete();
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item: '.$item->id.' - '.$scrapable->name().' deleted!'
        ]);
    }
}
