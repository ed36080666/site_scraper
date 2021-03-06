<?php

namespace App\Http\Controllers;

use App\Factories\ScraperFactory;
use App\Video;
use Illuminate\Http\Request;

class PageScrapeController extends Controller
{
    public function index(Request $request)
    {
        $videos = Video::orderBy('started_at', 'DESC')->get();

        $logo_map = array_values(array_map(function ($driver_config) {
            return [
                'src'      => asset("storage/logos/{$driver_config['logo_filename']}"),
                'base_url' => $driver_config['base_url']
            ];
        }, config('scrapers.drivers')));

        return view('welcome', [
            'videos'   => $videos,
            'logo_map' => $logo_map
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
        $video = Video::findOrFail($id);

        if ($video->log_path && file_exists($video->log_path)) {
            unlink($video->log_path);
        }

        if ($video->path && file_exists($video->buildPath())) {
            unlink($video->buildPath());
        }

        $video->delete();

        return response()->json([
            'success' => true,
            'message' => "Video: {$video->id} deleted!"
        ]);
    }
}
