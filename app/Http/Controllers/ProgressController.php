<?php

namespace App\Http\Controllers;

use App\Video;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $in_progress = Video::where('status', 'processing')
            ->orWhere('status', 'done')
            ->get()
            ->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'total_size' => $item->size,
                    'total_duration' => $item->duration,
                    'progress' => $item->processingProgress(),
                    'started_at' => $item->started_at,
                    'height' => $item->height,
                    'width' => $item->width,
                    'status' => $item->status,
                    'is_stream' => $item->is_stream,
                ];
            });

        return response()->json($in_progress);
    }
}
