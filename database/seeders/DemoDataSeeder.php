<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductVariant;
use App\Models\Recipe;
use App\Models\RecipeDetail;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori
        $catKopi = Category::firstOrCreate(['name' => 'Kopi']);
        $catNonKopi = Category::firstOrCreate(['name' => 'Non-Kopi']);
        $catMakanan = Category::firstOrCreate(['name' => 'Makanan Utama']);
        $catCemilan = Category::firstOrCreate(['name' => 'Cemilan']);

        // 2. Ingredients (Bahan Baku)
        $ingKopi = Ingredient::firstOrCreate(['name' => 'Biji Kopi Arabica'], ['total_stock' => 5000, 'unit' => 'gram', 'minimum_stock' => 1000]);
        $ingSusu = Ingredient::firstOrCreate(['name' => 'Susu Segar'], ['total_stock' => 10000, 'unit' => 'ml', 'minimum_stock' => 2000]);
        $ingGulaAren = Ingredient::firstOrCreate(['name' => 'Gula Aren Cair'], ['total_stock' => 2000, 'unit' => 'ml', 'minimum_stock' => 500]);
        $ingSirupVanilla = Ingredient::firstOrCreate(['name' => 'Sirup Vanilla'], ['total_stock' => 1000, 'unit' => 'ml', 'minimum_stock' => 200]);
        $ingMatcha = Ingredient::firstOrCreate(['name' => 'Bubuk Matcha'], ['total_stock' => 1000, 'unit' => 'gram', 'minimum_stock' => 200]);
        $ingCokelat = Ingredient::firstOrCreate(['name' => 'Bubuk Cokelat'], ['total_stock' => 2000, 'unit' => 'gram', 'minimum_stock' => 500]);
        $ingRoti = Ingredient::firstOrCreate(['name' => 'Roti Croissant'], ['total_stock' => 50, 'unit' => 'pcs', 'minimum_stock' => 10]);
        $ingKentang = Ingredient::firstOrCreate(['name' => 'Kentang Goreng'], ['total_stock' => 5000, 'unit' => 'gram', 'minimum_stock' => 1000]);
        $ingAyam = Ingredient::firstOrCreate(['name' => 'Daging Ayam'], ['total_stock' => 3000, 'unit' => 'gram', 'minimum_stock' => 1000]);
        $ingNasi = Ingredient::firstOrCreate(['name' => 'Beras Putih'], ['total_stock' => 10000, 'unit' => 'gram', 'minimum_stock' => 2000]);

        $ingredients = [$ingKopi, $ingSusu, $ingGulaAren, $ingSirupVanilla, $ingMatcha, $ingCokelat, $ingRoti, $ingKentang, $ingAyam, $ingNasi];
        foreach ($ingredients as $ing) {
            if ($ing->batches()->count() === 0) {
                \App\Models\IngredientBatch::create([
                    'ingredient_id' => $ing->id,
                    'stock' => $ing->total_stock,
                    'expiry_date' => \Illuminate\Support\Carbon::now()->addYears(1)->format('Y-m-d'),
                ]);
            }
        }

        // 3. Products
        $products = [
            // Kopi
            [
                'name' => 'Kopi Susu Gula Aren',
                'base_price' => 25000,
                'category_id' => $catKopi->id,
                'description' => 'Kopi susu dengan gula aren asli yang manis dan gurih.',
                'photos' => ["products/2XgM0XuRWZE6GvaHCPIggGt61gykCNHIYyUX0dOT.jpg"],
                'tags' => 'recommended,bestseller',
                'ingredients' => [
                    [$ingKopi->id, 15], // 15 gram kopi
                    [$ingSusu->id, 150], // 150 ml susu
                    [$ingGulaAren->id, 30], // 30 ml gula aren
                ]
            ],
            [
                'name' => 'Vanilla Latte',
                'base_price' => 28000,
                'category_id' => $catKopi->id,
                'description' => 'Espresso dengan susu hangat dan sirup vanilla.',
                'photos' => ["products/XpeHbXeE1AkJ47nFA03e8ntEIVrLnOaSAXgZyBj1.jpg"],
                'tags' => 'hot',
                'ingredients' => [
                    [$ingKopi->id, 18],
                    [$ingSusu->id, 180],
                    [$ingSirupVanilla->id, 20],
                ]
            ],
            [
                'name' => 'Americano',
                'base_price' => 20000,
                'category_id' => $catKopi->id,
                'description' => 'Espresso dengan tambahan air panas/dingin.',
                'photos' => ["products/LSa6GDhlqltNl7JfsMd4Xjf9MvEmuy4VLMFR63Pg.jpg"],
                'tags' => 'strong,diet',
                'ingredients' => [
                    [$ingKopi->id, 18],
                ]
            ],
            // Non-Kopi
            [
                'name' => 'Matcha Latte',
                'base_price' => 30000,
                'category_id' => $catNonKopi->id,
                'description' => 'Matcha premium khas Jepang dipadukan dengan susu segar.',
                'photos' => ["products/SUsFsdN1gJCEVARo27DtaLVoLl23sN3G2qs817oV.jpg"],
                'tags' => 'recommended,sweet',
                'ingredients' => [
                    [$ingMatcha->id, 20],
                    [$ingSusu->id, 150],
                ]
            ],
            [
                'name' => 'Ice Chocolate',
                'base_price' => 27000,
                'category_id' => $catNonKopi->id,
                'description' => 'Cokelat pekat yang disajikan dingin dengan susu.',
                'photos' => ["products/RIhyi4RSV6t2hRDFWkT0RQuGLMcRIoczsan3atTP.jpg"],
                'tags' => 'sweet',
                'ingredients' => [
                    [$ingCokelat->id, 25],
                    [$ingSusu->id, 150],
                ]
            ],
            // Makanan Utama
            [
                'name' => 'Nasi Goreng Spesial',
                'base_price' => 35000,
                'category_id' => $catMakanan->id,
                'description' => 'Nasi goreng dengan bumbu rahasia dan suwiran ayam.',
                'photos' => ["products/JnarubxjcBmyKBUK02X3pIq14WuRe4GkKs3yROvn.jpg"],
                'tags' => 'spicy,bestseller',
                'ingredients' => [
                    [$ingNasi->id, 200],
                    [$ingAyam->id, 50],
                ]
            ],
            [
                'name' => 'Ayam Penyet Keshir',
                'base_price' => 38000,
                'category_id' => $catMakanan->id,
                'description' => 'Ayam penyet dengan sambal khas Keshir yang super pedas.',
                'photos' => ["products/alKHRJHnjJioJRtezbOkrk3zuDqYSUdz2vjcKn1a.jpg"],
                'tags' => 'spicy',
                'ingredients' => [
                    [$ingAyam->id, 150],
                    [$ingNasi->id, 150],
                ]
            ],
            // Cemilan
            [
                'name' => 'French Fries',
                'base_price' => 22000,
                'category_id' => $catCemilan->id,
                'description' => 'Kentang goreng gurih renyah.',
                'photos' => ["products/gpZYT33iPPjY2g5eiTYoDXbN2pC2V0eeSIgD2HM9.jpg"],
                'tags' => 'snack',
                'ingredients' => [
                    [$ingKentang->id, 150],
                ]
            ],
            [
                'name' => 'Butter Croissant',
                'base_price' => 25000,
                'category_id' => $catCemilan->id,
                'description' => 'Croissant renyah dengan rasa mentega yang gurih.',
                'photos' => ["products/AzkJygyskRE9UiL6aqUyiWr8SldC1lK7tos96lfs.jpg"],
                'tags' => 'snack,pastry',
                'ingredients' => [
                    [$ingRoti->id, 1],
                ]
            ],
            [
                'name' => 'Platter Keshir',
                'base_price' => 45000,
                'category_id' => $catCemilan->id,
                'description' => 'Campuran kentang goreng, sosis, dan nugget ayam.',
                'photos' => ["products/KzeH5X9TxsWLysEN4M8zzZfc9ITvh637NcN7Wgv8.jpg"],
                'tags' => 'snack,sharing',
                'ingredients' => [
                    [$ingKentang->id, 100],
                    [$ingAyam->id, 100],
                ]
            ],
        ];

        $productModels = [];

        foreach ($products as $p) {
            $product = Product::firstOrCreate(
                ['name' => $p['name']],
                [
                    'base_price' => $p['base_price'],
                    'category_id' => $p['category_id'],
                    'description' => $p['description'],
                    'photos' => $p['photos'] ?? null,
                    'tags' => $p['tags'],
                ]
            );

            $productModels[] = $product;

            // Create Recipe
            $recipe = Recipe::firstOrCreate(['product_id' => $product->id]);

            // Create Recipe Details
            foreach ($p['ingredients'] as $ingData) {
                RecipeDetail::firstOrCreate([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingData[0],
                ], [
                    'quantity' => $ingData[1],
                ]);
            }
        }

        // 4. Product Variants & Addons (contoh)
        // Varian ukuran untuk Kopi Susu
        ProductVariant::firstOrCreate(['product_id' => $productModels[0]->id, 'variant_name' => 'Regular'], ['additional_price' => 0]);
        ProductVariant::firstOrCreate(['product_id' => $productModels[0]->id, 'variant_name' => 'Large'], ['additional_price' => 5000]);

        // Addon Extra Shot Espresso
        ProductAddon::firstOrCreate(['product_id' => $productModels[0]->id, 'addon_name' => 'Extra Shot'], ['price' => 8000]);
        ProductAddon::firstOrCreate(['product_id' => $productModels[1]->id, 'addon_name' => 'Extra Shot'], ['price' => 8000]);
        ProductAddon::firstOrCreate(['product_id' => $productModels[2]->id, 'addon_name' => 'Extra Shot'], ['price' => 8000]);
        ProductAddon::firstOrCreate(['product_id' => $productModels[3]->id, 'addon_name' => 'Oat Milk'], ['price' => 10000]);

        // 5. Dummy Transactions untuk Best Sellers (sekitar 20 transaksi selesai hari ini/kemarin)
        for ($i = 0; $i < 20; $i++) {
            $trans = Transaction::create([
                'order_type' => 'dine_in',
                'source' => 'pos',
                'cashier_id' => 3, // id 3 is usually cashier based on seeder
                'table_id' => rand(1, 10),
                'customer_name' => 'Customer ' . rand(1, 50),
                'subtotal' => 0, 
                'grand_total' => 0,
                'payment_status' => 'paid',
                'payment_method' => 'cash',
            ]);

            $total = 0;
            // 1-3 items per transaction
            $itemsCount = rand(1, 3);
            for ($j = 0; $j < $itemsCount; $j++) {
                $randomProduct = $productModels[rand(0, 9)];
                $qty = rand(1, 3);
                $subtotal = $randomProduct->base_price * $qty;
                
                TransactionDetail::create([
                    'transaction_id' => $trans->id,
                    'product_id' => $randomProduct->id,
                    'price' => $randomProduct->base_price,
                    'qty' => $qty,
                    'status' => 'done'
                ]);

                $total += $subtotal;
            }

            // tax and service 
            $tax = $total * 0.11;
            $service = $total * 0.05;
            $grandTotal = $total + $tax + $service;

            $trans->update([
                'subtotal' => $total,
                'tax_total' => $tax,
                'service_total' => $service,
                'grand_total' => $grandTotal,
            ]);
            usleep(10000); // small delay to make tx unique
        }
    }
}
