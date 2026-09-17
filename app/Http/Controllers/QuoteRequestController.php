<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\QuoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ItemAnalyticsService;

class QuoteRequestController extends Controller
{
    public function store(Request $request, Item $item): JsonResponse
    {
        abort_unless($item->is_published || auth()->check(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'company' => ['nullable', 'string', 'max:180'],
            'message' => ['nullable', 'string', 'max:3000'],
            'configuration' => ['nullable', 'array', 'max:500'],
        ]);

        $quoteRequest = QuoteRequest::create([
            ...$validated,
            'item_id' => $item->id,
            'user_id' => auth()->id(),
        ]);
        app(ItemAnalyticsService::class)->record($item, 'quote_requested', $request, ['quote_request_id' => $quoteRequest->id]);

        return response()->json(['message' => 'Your quote request has been received.'], 201);
    }
}
