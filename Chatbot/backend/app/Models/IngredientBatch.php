<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredientBatch extends Model
{
    protected $fillable = ['ingredient_id', 'stock', 'expiry_date', 'purchase_price'];

    protected function casts(): array
    {
        return [
            'stock' => 'decimal:2',
            'expiry_date' => 'date',
            'purchase_price' => 'decimal:2',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }
}
