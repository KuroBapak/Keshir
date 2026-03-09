<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = ['name', 'total_stock', 'unit', 'content_per_pack', 'minimum_stock'];

    protected function casts(): array
    {
        return [
            'total_stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'content_per_pack' => 'decimal:2',
        ];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(IngredientBatch::class)->orderBy('expiry_date', 'asc');
    }

    /**
     * Check if ingredient is below minimum stock.
     */
    public function isBelowMinimum(): bool
    {
        return $this->total_stock <= $this->minimum_stock;
    }
}
