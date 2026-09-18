<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'name', 'slug', 'description', 'category', 'tags', 'image_path', 'model_path', 'is_published', 'is_featured', 'allow_download'])]
class Item extends \Illuminate\Database\Eloquent\Model
{
    protected function casts(): array
    {
        return ['tags' => 'array', 'is_published' => 'boolean', 'is_featured' => 'boolean', 'allow_download' => 'boolean', 'model_validation_messages' => 'array'];
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function textures(): HasMany
    {
        return $this->hasMany(Texture::class);
    }

    public function hotspots(): HasMany
    {
        return $this->hasMany(Hotspot::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ItemVariant::class)->orderBy('sort_order');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ItemLike::class);
    }

    public function isLikedByVisitor(string $visitorHash): bool
    {
        return $this->likes()->where('visitor_hash', $visitorHash)->exists();
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('public-model-categories'));
        static::deleted(fn () => Cache::forget('public-model-categories'));

        static::creating(function (self $item): void {
            $item->slug = static::uniqueSlug($item->name);
        });

        static::updating(function (self $item): void {
            if ($item->isDirty('name') && ! $item->isDirty('slug')) {
                $item->slug = static::uniqueSlug($item->name, $item->id);
            }
        });
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $counter = 2;

        while (static::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
