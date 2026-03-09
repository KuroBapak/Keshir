<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAddon;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'variant_name' => 'required|string|max:255',
            'additional_price' => 'required|numeric|min:0',
        ]);

        $product->variants()->create($request->only(['variant_name', 'additional_price']));
        return back()->with('success', 'Varian berhasil ditambahkan.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $variant->delete();
        return back()->with('success', 'Varian berhasil dihapus.');
    }
}
