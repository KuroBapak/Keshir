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
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only([
            'name', 'base_price', 'category_id', 'description', 'tags', 'is_active',
        ]);

        if ($request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $file) {
                $photos[] = $file->store('products', 'public');
            }
            $data['photos'] = $photos;
        }

        $product = Product::create($data);

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
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'delete_photos' => 'nullable|array',
        ]);

        $data = $request->only([
            'name', 'base_price', 'category_id', 'description', 'tags', 'is_active',
        ]);

        $currentPhotos = $product->photos ?? [];

        if ($request->has('delete_photos')) {
            foreach ($request->delete_photos as $deletePath) {
                if (($key = array_search($deletePath, $currentPhotos)) !== false) {
                    \Storage::disk('public')->delete($deletePath);
                    unset($currentPhotos[$key]);
                }
            }
            $currentPhotos = array_values($currentPhotos);
        }

        if ($request->hasFile('photos')) {
            // Check max 5 constraint
            if (count($currentPhotos) + count($request->file('photos')) > 5) {
                return back()->withInput()->withErrors(['photos' => 'Maksimal 5 foto per produk.']);
            }
            foreach ($request->file('photos') as $file) {
                $currentPhotos[] = $file->store('products', 'public');
            }
        }

        $data['photos'] = $currentPhotos;
        $product->update($data);

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        if (is_array($product->photos)) {
            foreach ($product->photos as $photo) {
                \Storage::disk('public')->delete($photo);
            }
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
