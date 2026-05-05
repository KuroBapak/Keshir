<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeDetail;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * List all products with recipe status.
     */
    public function index()
    {
        $products = Product::with(['category', 'recipe.details'])
            ->paginate(15);
        return view('dashboard.recipes.index', compact('products'));
    }

    /**
     * Set/Update recipe for a product.
     */
    public function edit(Product $product)
    {
        $product->load('recipe.details.ingredient');
        $ingredients = Ingredient::orderBy('name')->get();
        return view('dashboard.products.recipe', compact('product', 'ingredients'));
    }

    /**
     * Save recipe details (bulk save).
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'ingredients' => 'required|array|min:1',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
        ]);

        // Create or get recipe
        $recipe = $product->recipe ?? Recipe::create(['product_id' => $product->id]);

        // Clear old details and replace
        $recipe->details()->delete();

        foreach ($request->ingredients as $item) {
            $recipe->details()->create([
                'ingredient_id' => $item['ingredient_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('products.show', $product)->with('success', 'Resep berhasil disimpan.');
    }

    /**
     * Delete the recipe for a product.
     */
    public function destroy(Product $product)
    {
        if ($product->recipe) {
            $product->recipe->delete();
        }
        return back()->with('success', 'Resep berhasil dihapus.');
    }
}
