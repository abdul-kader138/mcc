<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemLike;
use App\Services\ItemAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemLikeController extends Controller
{
    public function toggle(Request $request, Item $item): JsonResponse
    {
        abort_unless($item->is_published, 404);

        $visitorHash = ItemAnalyticsService::visitorHash($request);
        $like = $item->likes()->where('visitor_hash', $visitorHash)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            ItemLike::create([
                'item_id' => $item->id,
                'user_id' => $request->user()?->id,
                'visitor_hash' => $visitorHash,
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $item->likes()->count(),
        ]);
    }
}
