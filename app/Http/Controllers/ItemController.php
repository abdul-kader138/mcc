<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Texture;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ItemController extends Controller
{
    private const SORTS = ['featured', 'latest', 'popular', 'liked', 'name'];

    public function index(Request $request): View
    {
        $query = Item::query()->where('is_published', true)->withCount('likes');
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));
        $tag = trim((string) $request->query('tag', ''));
        $sort = in_array($request->query('sort'), self::SORTS, true) ? $request->query('sort') : 'featured';

        if ($search !== '') {
            $query->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%"));
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if ($tag !== '') {
            $query->whereJsonContains('tags', $tag);
        }

        match ($sort) {
            'latest' => $query->latest(),
            'popular' => $query->orderByDesc('view_count'),
            'liked' => $query->orderByDesc('likes_count'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('is_featured')->latest(),
        };

        return view('items.index', [
            'items' => $query->paginate(12)->withQueryString(),
            'categories' => Cache::remember('public-model-categories', 60, fn () => Item::query()->where('is_published', true)->whereNotNull('category')->where('category', '!=', '')->distinct()->orderBy('category')->pluck('category')->all()),
            'tags' => Cache::remember('public-model-tags', 60, fn () => Item::query()->where('is_published', true)->whereNotNull('tags')->pluck('tags')->flatten()->filter()->unique()->sort()->values()->all()),
            'search' => $search,
            'category' => $category,
            'tag' => $tag,
            'sort' => $sort,
        ]);
    }

    public function show(Request $request, Item $item): View
    {
        abort_unless($item->is_published || auth()->check(), 404);
        if ($item->is_published) {
            $item->increment('view_count');
            app(\App\Services\ItemAnalyticsService::class)->record($item, 'view', $request);
        }

        $relatedItems = Item::query()
            ->where('is_published', true)
            ->where('id', '!=', $item->id)
            ->when($item->category, fn ($query) => $query->where('category', $item->category))
            ->orderByDesc('is_featured')
            ->latest()
            ->limit(3)
            ->get();

        return view('items.show', [
            'item' => $item,
            'relatedItems' => $relatedItems,
            'hotspots' => $item->hotspots->map(fn ($hotspot): array => [
                'title' => $hotspot->title,
                'description' => $hotspot->description,
                'x' => $hotspot->position_x,
                'y' => $hotspot->position_y,
                'z' => $hotspot->position_z,
            ])->values(),
            'variants' => $item->variants->map(fn ($variant): array => [
                'name' => $variant->name,
                'sku' => $variant->sku,
                'description' => $variant->description,
                'price' => $variant->price,
                'configuration' => $variant->configuration,
                'is_default' => $variant->is_default,
            ])->values(),
            'textures' => Texture::query()
                ->where(fn ($query) => $query->whereNull('item_id')->orWhere('item_id', $item->id))
                ->orderBy('name')
                ->get()
                ->map(fn (Texture $texture): array => [
                    'name' => $texture->name,
                    'url' => '/storage/'.ltrim($texture->path, '/'),
                ])
                ->values(),
            // Keep local public-disk assets relative to the current host. This
            // avoids localhost/127.0.0.1 CORS mismatches during development.
            'imageUrl' => $item->image_path ? '/storage/'.ltrim($item->image_path, '/') : null,
            'modelUrl' => '/storage/'.ltrim($item->model_path, '/'),
            'viewCount' => $item->view_count,
            'likeCount' => $item->likes()->count(),
            'liked' => $item->isLikedByVisitor(\App\Services\ItemAnalyticsService::visitorHash($request)),
        ]);
    }

    public function download(Item $item): Response
    {
        abort_unless($item->is_published && $item->allow_download, 404);

        return Storage::disk('public')->download($item->model_path, $item->slug.'.glb');
    }
}
