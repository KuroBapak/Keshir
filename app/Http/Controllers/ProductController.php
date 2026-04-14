<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            'is_active' => 'nullable|boolean',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants' => 'nullable|array',
            'variants.*.variant_name' => 'nullable|string|max:255',
            'variants.*.additional_price' => 'nullable|numeric|min:0',
            'addons' => 'nullable|array',
            'addons.*.addon_name' => 'nullable|string|max:255',
            'addons.*.price' => 'nullable|numeric|min:0',
        ]);

        $product = DB::transaction(function () use ($request) {
            $data = $request->only([
                'name', 'base_price', 'category_id', 'description', 'tags',
            ]);
            $data['is_active'] = $request->boolean('is_active');

            if ($request->hasFile('photos')) {
                $photos = [];
                foreach ($request->file('photos') as $file) {
                    $photos[] = $file->store('products', 'public');
                }
                $data['photos'] = $photos;
            }

            $product = Product::create($data);
            $this->syncProductOptions($product, $request->input('variants', []), $request->input('addons', []));

            return $product;
        });

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'addons', 'recipe.details.ingredient']);
        return view('dashboard.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['variants', 'addons']);
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
            'is_active' => 'nullable|boolean',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'delete_photos' => 'nullable|array',
            'variants' => 'nullable|array',
            'variants.*.variant_name' => 'nullable|string|max:255',
            'variants.*.additional_price' => 'nullable|numeric|min:0',
            'addons' => 'nullable|array',
            'addons.*.addon_name' => 'nullable|string|max:255',
            'addons.*.price' => 'nullable|numeric|min:0',
        ]);

        $currentPhotos = $product->photos ?? [];
        if ($request->hasFile('photos') && count($currentPhotos) + count($request->file('photos')) > 5) {
            return back()->withInput()->withErrors(['photos' => 'Maksimal 5 foto per produk.']);
        }

        DB::transaction(function () use ($request, $product) {
            $data = $request->only([
                'name', 'base_price', 'category_id', 'description', 'tags',
            ]);
            $data['is_active'] = $request->boolean('is_active');

            $currentPhotos = $product->photos ?? [];

            if ($request->has('delete_photos')) {
                foreach ($request->delete_photos as $deletePath) {
                    if (($key = array_search($deletePath, $currentPhotos)) !== false) {
                        Storage::disk('public')->delete($deletePath);
                        unset($currentPhotos[$key]);
                    }
                }
                $currentPhotos = array_values($currentPhotos);
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $currentPhotos[] = $file->store('products', 'public');
                }
            }

            $data['photos'] = $currentPhotos;
            $product->update($data);
            $this->syncProductOptions($product, $request->input('variants', []), $request->input('addons', []));
        });

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        if (is_array($product->photos)) {
            foreach ($product->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function syncProductOptions(Product $product, array $variants, array $addons): void
    {
        $variantPayload = collect($variants)
            ->map(function ($variant) {
                return [
                    'variant_name' => trim((string) ($variant['variant_name'] ?? '')),
                    'additional_price' => (float) ($variant['additional_price'] ?? 0),
                ];
            })
            ->filter(fn ($variant) => $variant['variant_name'] !== '')
            ->values()
            ->all();

        $addonPayload = collect($addons)
            ->map(function ($addon) {
                return [
                    'addon_name' => trim((string) ($addon['addon_name'] ?? '')),
                    'price' => (float) ($addon['price'] ?? 0),
                ];
            })
            ->filter(fn ($addon) => $addon['addon_name'] !== '')
            ->values()
            ->all();

        $product->variants()->delete();
        if (!empty($variantPayload)) {
            $product->variants()->createMany($variantPayload);
        }

        $product->addons()->delete();
        if (!empty($addonPayload)) {
            $product->addons()->createMany($addonPayload);
        }
    }
}
