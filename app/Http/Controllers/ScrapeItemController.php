<?php

namespace App\Http\Controllers;

use App\DTOs\ScrapeItemDTO;
use App\ScrapeItem;
use Illuminate\Http\Request;

class ScrapeItemController extends Controller
{
    public function index(Request $request)
    {
        $paginator = ScrapeItem::query()
            ->when($request->get('query'), function ($query) use ($request) {
                $query->leftJoin('videos as v', 'v.id', '=', 'scrape_items.scrapable_id')
                    ->leftJoin('photo_galleries', 'photo_galleries.id', '=', 'scrape_items.scrapable_id')
                    ->where('v.name', 'like', '%'.$request->get('query').'%')
                    ->orWhere('photo_galleries.name', 'like', '%'.$request->get('query').'%');
            })
            ->orderBy('started_at', 'desc')
            ->paginate(50);


        return response()->json([
            'data' => collect($paginator->items())->map(function (ScrapeItem $item) {
                return (new ScrapeItemDTO($item->scrapable))->toArray();
            }),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'next' => $paginator->nextPageUrl(),
                'previous' => $paginator->previousPageUrl(),
                'per_page' => $paginator->perPage(),
                'has_more' => $paginator->hasMorePages(),
            ]
        ]);
    }
}
