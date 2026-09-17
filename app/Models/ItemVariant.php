<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['item_id', 'name', 'sku', 'description', 'price', 'configuration', 'is_default', 'sort_order'])]
class ItemVariant extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'configuration' => 'array',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
