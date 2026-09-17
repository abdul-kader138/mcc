<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['item_id', 'title', 'description', 'position_x', 'position_y', 'position_z', 'sort_order'])]
class Hotspot extends Model
{
    protected function casts(): array
    {
        return [
            'position_x' => 'float',
            'position_y' => 'float',
            'position_z' => 'float',
            'sort_order' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
