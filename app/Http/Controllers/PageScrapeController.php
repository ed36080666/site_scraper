<?php

namespace App\Http\Controllers;

use App\Factories\ScraperFactory;
use App\ScrapeItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PageScrapeController extends Controller
{
    public function index(Request $request)
    {
        $logo_map = array_values(array_map(function ($driver_config) {
            return [
                'src'      => asset("storage/logos/{$driver_config['logo_filename']}"),
                'base_url' => $driver_config['base_url']
            ];
        }, config('scrapers.drivers')));

        return view('welcome', [
            'logo_map' => $logo_map,
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
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $scraper = ScraperFactory::resolveFromUrl($request->video_url);
            $filename = str_replace("'", '', $request->filename);

            $scraper->scrape($request->video_url, $filename);

            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
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
