<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientBatch;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        $ingredients = Ingredient::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('dashboard.ingredients.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'content_per_pack' => 'nullable|numeric|min:0.01',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        Ingredient::create($request->only(['name', 'unit', 'content_per_pack', 'minimum_stock']));
        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function show(Ingredient $ingredient)
    {
        $batches = $ingredient->batches()->where('stock', '>', 0)->orderBy('expiry_date', 'asc')->paginate(10);
        return view('dashboard.ingredients.show', compact('ingredient', 'batches'));
    }

    public function edit(Ingredient $ingredient)
    {
        return view('dashboard.ingredients.form', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'content_per_pack' => 'nullable|numeric|min:0.01',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $ingredient->update($request->only(['name', 'unit', 'content_per_pack', 'minimum_stock']));
        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil diupdate.');
    }

    /**
     * Add a new stock batch (Stock In with Expiry Date).
     */
    public function addBatch(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'stock' => 'required|numeric|min:0.01',
            'input_mode' => 'nullable|in:base,pack',
            'expiry_date' => 'required|date|after:today',
            'purchase_price' => 'required|numeric|min:0',
        ]);

        $stock = $request->stock;

        // Convert pack to base units if applicable
        if ($request->input_mode === 'pack' && $ingredient->content_per_pack) {
            $stock = $request->stock * $ingredient->content_per_pack;
        }

        $ingredient->batches()->create([
            'stock' => $stock,
            'expiry_date' => $request->expiry_date,
            'purchase_price' => $request->purchase_price,
        ]);

        // Update total_stock
        $ingredient->update([
            'total_stock' => $ingredient->batches()->sum('stock'),
        ]);

        return back()->with('success', 'Stok batch berhasil ditambahkan.');
    }

    public function destroy(Ingredient $ingredient)
    {
        try {
            $ingredient->delete();
            return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if it's a foreign key constraint violation
            if ($e->getCode() == 23000) {
                return redirect()->route('ingredients.index')->with('error', 'Bahan baku tidak dapat dihapus karena sedang digunakan dalam resep.');
            }
            return redirect()->route('ingredients.index')->with('error', 'Terjadi kesalahan saat menghapus bahan baku.');
        }
    }
}
