<?php

namespace App\Http\Controllers;

use App\Video;
use Illuminate\Http\Request;
use Tests\Browser\ScrapePtrexUrls;
use Tests\Browser\ScrapePwildUrls;

class PageScrapeController extends Controller
{
    public function index(Request $request)
    {
        $videos = Video::orderBy('started_at', 'DESC')->get();
        return view('welcome', [
            'videos' => $videos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // todo think of cleaner way to do this that allows flexibility moving forward.
        // factory? scrapers implementing interface? 
        if (str_contains($request->video_url, 'porntrex.com')) {
            $scraper = new ScrapePtrexUrls($request->video_url, $request->filename);
        } elseif (str_contains($request->video_url, 'pornwild.com')) {
            $scraper = new ScrapePwildUrls($request->video_url, $request->filename);
        } else {
            abort(422, 'Unknown base video UR: Doesnt match supported site');
        }

        $scraper->prepare();
        $scraper->scrape();

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
