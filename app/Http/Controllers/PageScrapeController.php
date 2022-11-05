<?php

namespace App\Http\Controllers;

use App\DTOs\ScrapeItemDTO;
use App\Factories\ScraperFactory;
use App\ScrapeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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

    public function log(ScrapeItem $scrape_item): \Illuminate\Http\Response
    {
        $log_contents = file_get_contents($scrape_item->log_path);
        $response = Response::make($log_contents);
        $response->header('Content-Type', 'text/plain');

        return $response;
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

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $item = ScrapeItem::findOrFail($id);

        $scrapable = $item->scrapable;

        if ($item->log_path && file_exists($item->log_path)) {
            unlink($item->log_path);
        }

        $scrapable->removeFiles();

        $scrapable->delete();
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item: '.$item->id.' - '.$scrapable->name().' deleted!'
        ]);
    }
}
