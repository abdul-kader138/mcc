<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ModelConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ItemAnalyticsService;

class ModelConfigurationController extends Controller
{
    public function store(Request $request, Item $item): JsonResponse
    {
        abort_unless($item->is_published || auth()->check(), 404);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'configuration' => ['required', 'array', 'max:500'],
            'configuration.version' => ['nullable', 'integer'],
            'configuration.item' => ['nullable', 'string', 'max:180'],
            'configuration.parts' => ['required', 'array', 'max:500'],
        ]);

        $configuration = ModelConfiguration::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'name' => $validated['name'] ?: 'My configuration',
            'configuration' => $validated['configuration'],
        ]);
        app(ItemAnalyticsService::class)->record($item, 'configuration_saved', $request, ['configuration_id' => $configuration->id]);

        return response()->json([
            'token' => $configuration->token,
            'url' => route('items.show', ['item' => $item->slug]).'?configuration='.$configuration->token,
        ], 201);
    }

    public function show(Item $item, ModelConfiguration $configuration): JsonResponse
    {
        abort_unless($configuration->item_id === $item->id, 404);
        abort_unless($item->is_published || auth()->check(), 404);

        return response()->json([
            'name' => $configuration->name,
            'configuration' => $configuration->configuration,
        ]);
    }
}
