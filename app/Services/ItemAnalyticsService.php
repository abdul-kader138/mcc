<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemAnalyticsEvent;
use Illuminate\Http\Request;

class ItemAnalyticsService
{
    public function record(Item $item, string $event, Request $request, array $metadata = []): void
    {
        ItemAnalyticsEvent::create([
            'item_id' => $item->id,
            'user_id' => $request->user()?->id,
            'event' => $event,
            'visitor_hash' => hash('sha256', $request->ip().'|'.($request->userAgent() ?? '')),
            'metadata' => $metadata,
        ]);
    }
}
