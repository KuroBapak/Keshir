<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->category_id, fn($q, $c) => $q->where('category_id', $c))
            ->paginate(15)
            ->withQueryString();

        $categories = Category::all();
        return view('dashboard.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('dashboard.products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $product = Product::create($request->only([
            'name', 'base_price', 'category_id', 'description', 'tags', 'is_active',
        ]));

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'addons', 'recipe.details.ingredient']);
        return view('dashboard.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('dashboard.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $product->update($request->only([
            'name', 'base_price', 'category_id', 'description', 'tags', 'is_active',
        ]));

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
