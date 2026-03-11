<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAddon;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'public_cart';

    /**
     * Get all items in the cart.
     */
    public function getCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Add an item to the cart.
     */
    public function addItem(int $productId, ?int $variantId, array $addonIds, int $qty, ?string $notes): void
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::findOrFail($variantId) : null;
        
        // Calculate base + variant price
        $price = $product->base_price + ($variant ? $variant->additional_price : 0);
        
        // Handle Addons
        $addonsInfo = [];
        $addonsTotal = 0;
        if (!empty($addonIds)) {
            $addons = ProductAddon::whereIn('id', $addonIds)->get();
            foreach ($addons as $addon) {
                $addonsInfo[] = [
                    'id' => $addon->id,
                    'name' => $addon->addon_name,
                    'price' => $addon->price,
                ];
                $addonsTotal += $addon->price;
            }
        }
        
        $itemPrice = $price + $addonsTotal;
        $subtotal = $itemPrice * $qty;

        $cartItemId = uniqid('cart_');

        $cart = $this->getCart();
        $cart[$cartItemId] = [
            'id' => $cartItemId,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variant_id' => $variant?->id,
            'variant_name' => $variant?->variant_name,
            'addons' => $addonsInfo,
            'qty' => $qty,
            'notes' => $notes,
            'price' => $itemPrice,
            'subtotal' => $subtotal,
        ];

        Session::put(self::SESSION_KEY, $cart);
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(string $cartItemId): void
    {
        $cart = $this->getCart();
        if (isset($cart[$cartItemId])) {
            unset($cart[$cartItemId]);
            Session::put(self::SESSION_KEY, $cart);
        }
    }

    /**
     * Clear the entire cart.
     */
    public function clearCart(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Get the cart total summary (items subtotal).
     */
    public function getSummary(): array
    {
        $cart = $this->getCart();
        $subtotal = collect($cart)->sum('subtotal');
        $itemCount = collect($cart)->sum('qty');

        return [
            'subtotal' => $subtotal,
            'item_count' => $itemCount,
        ];
    }
}
