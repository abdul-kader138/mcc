<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['item_id', 'name', 'path'])]
class Texture extends Model
{
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
