<?php

namespace App\Http\Controllers;

use App\DTOs\ScrapeItemDTO;
use App\ScrapeItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // todo determine if these status filters are causing bugs when
        // items status changes to something not in query
        $in_progress = ScrapeItem::where('status', 'processing')
            ->orWhere('status', 'done')
            ->get()
            ->transform(function (ScrapeItem $item) {
                return (new ScrapeItemDTO($item->scrapable))->toArray();
            });

        return response()->json($in_progress);
    }
}
