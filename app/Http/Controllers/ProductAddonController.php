<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAddon;
use Illuminate\Http\Request;

class ProductAddonController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'addon_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $product->addons()->create($request->only(['addon_name', 'price']));
        return back()->with('success', 'Add-on berhasil ditambahkan.');
    }

    public function destroy(Product $product, ProductAddon $addon)
    {
        $addon->delete();
        return back()->with('success', 'Add-on berhasil dihapus.');
    }
}
