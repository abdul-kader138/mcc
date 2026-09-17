<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['item_id', 'user_id', 'token', 'name', 'configuration'])]
class ModelConfiguration extends Model
{
    protected function casts(): array
    {
        return ['configuration' => 'array'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $configuration): void {
            $configuration->token ??= Str::random(40);
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
