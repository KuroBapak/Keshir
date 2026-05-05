<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'name', 'base_price', 'category_id', 'description',
        'photos', 'tags', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'is_active' => 'boolean',
            'photos' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ProductAddon::class);
    }

    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class);
    }

    /**
     * Check if the product is out of stock based on its recipe ingredients.
     */
    public function getIsOutOfStockAttribute(): bool
    {
        return !$this->checkAvailability(1);
    }

    /**
     * Check if there is enough stock for a given quantity.
     */
    public function checkAvailability(int $qty = 1): bool
    {
        $recipe = $this->recipe()->with('details.ingredient')->first();
        if (!$recipe) {
            return false; // If no recipe, cannot be made
        }

        foreach ($recipe->details as $detail) {
            $required = $detail->quantity * $qty;
            if (!$detail->ingredient || $detail->ingredient->total_stock < $required) {
                return false;
            }
        }

        return true;
    }
}
