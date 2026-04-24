<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeDetail;
use Illuminate\Database\Seeder;

class CoffeeShopSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // CATEGORIES
        // =====================
        $categories = [
            'Hot Coffee',
            'Iced Coffee', 
            'Non-Coffee',
            'Tea',
            'Frappe & Blended',
            'Snacks',
            'Pastry',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        // =====================
        // INGREDIENTS
        // =====================
        $ingredients = [
            // Coffee & Base
            ['name' => 'Espresso Shot', 'unit' => 'ml', 'total_stock' => 5000, 'minimum_stock' => 500],
            ['name' => 'Arabica Coffee Beans', 'unit' => 'gram', 'total_stock' => 10000, 'minimum_stock' => 1000],
            ['name' => 'Robusta Coffee Beans', 'unit' => 'gram', 'total_stock' => 5000, 'minimum_stock' => 500],
            
            // Milk & Cream
            ['name' => 'Fresh Milk', 'unit' => 'ml', 'total_stock' => 20000, 'minimum_stock' => 2000],
            ['name' => 'Oat Milk', 'unit' => 'ml', 'total_stock' => 5000, 'minimum_stock' => 500],
            ['name' => 'Almond Milk', 'unit' => 'ml', 'total_stock' => 3000, 'minimum_stock' => 300],
            ['name' => 'Whipped Cream', 'unit' => 'ml', 'total_stock' => 2000, 'minimum_stock' => 200],
            ['name' => 'Condensed Milk', 'unit' => 'ml', 'total_stock' => 5000, 'minimum_stock' => 500],
            
            // Syrups & Sweeteners
            ['name' => 'Vanilla Syrup', 'unit' => 'ml', 'total_stock' => 3000, 'minimum_stock' => 300],
            ['name' => 'Caramel Syrup', 'unit' => 'ml', 'total_stock' => 3000, 'minimum_stock' => 300],
            ['name' => 'Hazelnut Syrup', 'unit' => 'ml', 'total_stock' => 2000, 'minimum_stock' => 200],
            ['name' => 'Brown Sugar Syrup', 'unit' => 'ml', 'total_stock' => 3000, 'minimum_stock' => 300],
            ['name' => 'Simple Syrup', 'unit' => 'ml', 'total_stock' => 5000, 'minimum_stock' => 500],
            ['name' => 'Chocolate Sauce', 'unit' => 'ml', 'total_stock' => 3000, 'minimum_stock' => 300],
            
            // Tea
            ['name' => 'Green Tea Leaves', 'unit' => 'gram', 'total_stock' => 2000, 'minimum_stock' => 200],
            ['name' => 'Black Tea Leaves', 'unit' => 'gram', 'total_stock' => 2000, 'minimum_stock' => 200],
            ['name' => 'Jasmine Tea', 'unit' => 'gram', 'total_stock' => 1000, 'minimum_stock' => 100],
            ['name' => 'Matcha Powder', 'unit' => 'gram', 'total_stock' => 1000, 'minimum_stock' => 100],
            
            // Others
            ['name' => 'Ice Cubes', 'unit' => 'gram', 'total_stock' => 50000, 'minimum_stock' => 5000],
            ['name' => 'Water', 'unit' => 'ml', 'total_stock' => 100000, 'minimum_stock' => 10000],
            ['name' => 'Cocoa Powder', 'unit' => 'gram', 'total_stock' => 2000, 'minimum_stock' => 200],
            
            // Snacks & Pastry
            ['name' => 'Croissant', 'unit' => 'pcs', 'total_stock' => 50, 'minimum_stock' => 10],
            ['name' => 'Butter', 'unit' => 'gram', 'total_stock' => 2000, 'minimum_stock' => 200],
            ['name' => 'Bread', 'unit' => 'pcs', 'total_stock' => 30, 'minimum_stock' => 5],
            ['name' => 'French Fries', 'unit' => 'gram', 'total_stock' => 5000, 'minimum_stock' => 500],
            ['name' => 'Cheese', 'unit' => 'gram', 'total_stock' => 2000, 'minimum_stock' => 200],
            ['name' => 'Ham', 'unit' => 'gram', 'total_stock' => 1000, 'minimum_stock' => 100],
        ];

        $ingredientModels = [];
        foreach ($ingredients as $data) {
            $ingredientModels[$data['name']] = Ingredient::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        // =====================
        // PRODUCTS
        // =====================
        $hotCoffee = Category::where('name', 'Hot Coffee')->first();
        $icedCoffee = Category::where('name', 'Iced Coffee')->first();
        $nonCoffee = Category::where('name', 'Non-Coffee')->first();
        $tea = Category::where('name', 'Tea')->first();
        $frappe = Category::where('name', 'Frappe & Blended')->first();
        $snacks = Category::where('name', 'Snacks')->first();
        $pastry = Category::where('name', 'Pastry')->first();

        $products = [
            // Hot Coffee
            ['name' => 'Espresso', 'base_price' => 18000, 'category_id' => $hotCoffee->id, 'description' => 'Single shot of rich espresso', 'tags' => 'classic,strong', 'photos' => ['https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=400&q=80']],
            ['name' => 'Americano', 'base_price' => 22000, 'category_id' => $hotCoffee->id, 'description' => 'Espresso diluted with hot water', 'tags' => 'classic', 'photos' => ['https://images.unsplash.com/photo-1521302080334-4bebac2763a6?w=400&q=80']],
            ['name' => 'Cappuccino', 'base_price' => 28000, 'category_id' => $hotCoffee->id, 'description' => 'Espresso with steamed milk foam', 'tags' => 'classic,recommended', 'photos' => ['https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&q=80']],
            ['name' => 'Latte', 'base_price' => 30000, 'category_id' => $hotCoffee->id, 'description' => 'Espresso with steamed milk', 'tags' => 'classic,recommended', 'photos' => ['https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&q=80']],
            ['name' => 'Mocha', 'base_price' => 35000, 'category_id' => $hotCoffee->id, 'description' => 'Espresso with chocolate and steamed milk', 'tags' => 'sweet', 'photos' => ['https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?w=400&q=80']],
            ['name' => 'Vanilla Latte', 'base_price' => 35000, 'category_id' => $hotCoffee->id, 'description' => 'Latte with vanilla syrup', 'tags' => 'sweet', 'photos' => ['https://images.unsplash.com/photo-1587080413959-06b859fb107d?w=400&q=80']],
            ['name' => 'Caramel Macchiato', 'base_price' => 38000, 'category_id' => $hotCoffee->id, 'description' => 'Espresso with vanilla, milk, and caramel drizzle', 'tags' => 'sweet,recommended', 'photos' => ['https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=400&q=80']],
            ['name' => 'Hazelnut Latte', 'base_price' => 36000, 'category_id' => $hotCoffee->id, 'description' => 'Latte with hazelnut syrup', 'tags' => 'sweet', 'photos' => ['https://images.unsplash.com/photo-1534687941688-651ccaafbff8?w=400&q=80']],
            
            // Iced Coffee
            ['name' => 'Iced Americano', 'base_price' => 25000, 'category_id' => $icedCoffee->id, 'description' => 'Chilled espresso with cold water and ice', 'tags' => 'classic,refreshing', 'photos' => ['https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400&q=80']],
            ['name' => 'Iced Latte', 'base_price' => 32000, 'category_id' => $icedCoffee->id, 'description' => 'Espresso with cold milk over ice', 'tags' => 'classic,refreshing', 'photos' => ['https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&q=80']],
            ['name' => 'Iced Mocha', 'base_price' => 38000, 'category_id' => $icedCoffee->id, 'description' => 'Iced chocolate coffee with whipped cream', 'tags' => 'sweet,refreshing', 'photos' => ['https://images.unsplash.com/photo-1592663527359-cf6642f54cff?w=400&q=80']],
            ['name' => 'Iced Caramel Latte', 'base_price' => 38000, 'category_id' => $icedCoffee->id, 'description' => 'Iced latte with caramel syrup', 'tags' => 'sweet', 'photos' => ['https://images.unsplash.com/photo-1553909489-cd47e0907980?w=400&q=80']],
            ['name' => 'Vietnamese Coffee', 'base_price' => 28000, 'category_id' => $icedCoffee->id, 'description' => 'Strong coffee with condensed milk', 'tags' => 'sweet,strong', 'photos' => ['https://images.unsplash.com/photo-1514432324607-a09d9b4aefda?w=400&q=80']],
            ['name' => 'Es Kopi Susu', 'base_price' => 25000, 'category_id' => $icedCoffee->id, 'description' => 'Indonesian style iced milk coffee', 'tags' => 'local,recommended', 'photos' => ['https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400&q=80']],
            ['name' => 'Brown Sugar Latte', 'base_price' => 35000, 'category_id' => $icedCoffee->id, 'description' => 'Iced latte with brown sugar syrup', 'tags' => 'sweet,trending', 'photos' => ['https://images.unsplash.com/photo-1592663527359-cf6642f54cff?w=400&q=80']],
            
            // Non-Coffee
            ['name' => 'Hot Chocolate', 'base_price' => 28000, 'category_id' => $nonCoffee->id, 'description' => 'Rich hot chocolate drink', 'tags' => 'sweet,kids', 'photos' => ['https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=400&q=80']],
            ['name' => 'Iced Chocolate', 'base_price' => 30000, 'category_id' => $nonCoffee->id, 'description' => 'Chilled chocolate milk', 'tags' => 'sweet,kids,refreshing', 'photos' => ['https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&q=80']],
            ['name' => 'Matcha Latte', 'base_price' => 35000, 'category_id' => $nonCoffee->id, 'description' => 'Japanese green tea latte', 'tags' => 'healthy,trending', 'photos' => ['https://images.unsplash.com/photo-1515823064-d6e0c04616a7?w=400&q=80']],
            ['name' => 'Iced Matcha Latte', 'base_price' => 38000, 'category_id' => $nonCoffee->id, 'description' => 'Chilled matcha with milk', 'tags' => 'healthy,refreshing', 'photos' => ['https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=400&q=80']],
            
            // Tea
            ['name' => 'Hot Green Tea', 'base_price' => 18000, 'category_id' => $tea->id, 'description' => 'Traditional green tea', 'tags' => 'healthy,classic', 'photos' => ['https://images.unsplash.com/photo-1556881286-fc6915169721?w=400&q=80']],
            ['name' => 'Jasmine Tea', 'base_price' => 20000, 'category_id' => $tea->id, 'description' => 'Fragrant jasmine tea', 'tags' => 'healthy', 'photos' => ['https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=400&q=80']],
            ['name' => 'Iced Lemon Tea', 'base_price' => 22000, 'category_id' => $tea->id, 'description' => 'Refreshing iced tea with lemon', 'tags' => 'refreshing', 'photos' => ['https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&q=80']],
            ['name' => 'Thai Tea', 'base_price' => 28000, 'category_id' => $tea->id, 'description' => 'Creamy Thai-style iced tea', 'tags' => 'sweet,trending', 'photos' => ['https://images.unsplash.com/photo-1558857563-b371033873b8?w=400&q=80']],
            
            // Frappe & Blended
            ['name' => 'Coffee Frappe', 'base_price' => 38000, 'category_id' => $frappe->id, 'description' => 'Blended iced coffee', 'tags' => 'sweet,refreshing', 'photos' => ['https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&q=80']],
            ['name' => 'Mocha Frappe', 'base_price' => 42000, 'category_id' => $frappe->id, 'description' => 'Blended mocha with whipped cream', 'tags' => 'sweet', 'photos' => ['https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400&q=80']],
            ['name' => 'Caramel Frappe', 'base_price' => 42000, 'category_id' => $frappe->id, 'description' => 'Blended caramel coffee', 'tags' => 'sweet', 'photos' => ['https://images.unsplash.com/photo-1553909489-cd47e0907980?w=400&q=80']],
            ['name' => 'Matcha Frappe', 'base_price' => 40000, 'category_id' => $frappe->id, 'description' => 'Blended matcha green tea', 'tags' => 'healthy', 'photos' => ['https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=400&q=80']],
            
            // Snacks
            ['name' => 'French Fries', 'base_price' => 25000, 'category_id' => $snacks->id, 'description' => 'Crispy golden french fries', 'tags' => 'savory', 'photos' => ['https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=400&q=80']],
            ['name' => 'Cheese Fries', 'base_price' => 32000, 'category_id' => $snacks->id, 'description' => 'French fries with melted cheese', 'tags' => 'savory,recommended', 'photos' => ['https://images.unsplash.com/photo-1585109649139-366815a0d713?w=400&q=80']],
            ['name' => 'Toast Bread', 'base_price' => 20000, 'category_id' => $snacks->id, 'description' => 'Toasted bread with butter', 'tags' => 'simple', 'photos' => ['https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80']],
            
            // Pastry
            ['name' => 'Butter Croissant', 'base_price' => 28000, 'category_id' => $pastry->id, 'description' => 'Flaky buttery croissant', 'tags' => 'classic,recommended', 'photos' => ['https://images.unsplash.com/photo-1555507036-ab1f4038024a?w=400&q=80']],
            ['name' => 'Chocolate Croissant', 'base_price' => 32000, 'category_id' => $pastry->id, 'description' => 'Croissant filled with chocolate', 'tags' => 'sweet', 'photos' => ['https://images.unsplash.com/photo-1530610476181-d83430b64dcd?w=400&q=80']],
            ['name' => 'Ham & Cheese Croissant', 'base_price' => 35000, 'category_id' => $pastry->id, 'description' => 'Savory croissant with ham and cheese', 'tags' => 'savory', 'photos' => ['https://images.unsplash.com/photo-1549903072-7e6e8f06420a?w=400&q=80']],
        ];

        $productModels = [];
        foreach ($products as $data) {
            $productModels[$data['name']] = Product::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        // =====================
        // RECIPES
        // =====================
        $recipes = [
            // Hot Coffee Recipes
            'Espresso' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 30],
            ],
            'Americano' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Water', 'quantity' => 120],
            ],
            'Cappuccino' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 120],
            ],
            'Latte' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 180],
            ],
            'Mocha' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 150],
                ['ingredient' => 'Chocolate Sauce', 'quantity' => 30],
            ],
            'Vanilla Latte' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 180],
                ['ingredient' => 'Vanilla Syrup', 'quantity' => 20],
            ],
            'Caramel Macchiato' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 180],
                ['ingredient' => 'Vanilla Syrup', 'quantity' => 15],
                ['ingredient' => 'Caramel Syrup', 'quantity' => 15],
            ],
            'Hazelnut Latte' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 180],
                ['ingredient' => 'Hazelnut Syrup', 'quantity' => 20],
            ],
            
            // Iced Coffee Recipes
            'Iced Americano' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Water', 'quantity' => 100],
                ['ingredient' => 'Ice Cubes', 'quantity' => 150],
            ],
            'Iced Latte' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 180],
                ['ingredient' => 'Ice Cubes', 'quantity' => 150],
            ],
            'Iced Mocha' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 150],
                ['ingredient' => 'Chocolate Sauce', 'quantity' => 30],
                ['ingredient' => 'Ice Cubes', 'quantity' => 150],
                ['ingredient' => 'Whipped Cream', 'quantity' => 30],
            ],
            'Vietnamese Coffee' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Condensed Milk', 'quantity' => 40],
                ['ingredient' => 'Ice Cubes', 'quantity' => 150],
            ],
            'Es Kopi Susu' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 100],
                ['ingredient' => 'Brown Sugar Syrup', 'quantity' => 25],
                ['ingredient' => 'Ice Cubes', 'quantity' => 150],
            ],
            'Brown Sugar Latte' => [
                ['ingredient' => 'Espresso Shot', 'quantity' => 60],
                ['ingredient' => 'Fresh Milk', 'quantity' => 180],
                ['ingredient' => 'Brown Sugar Syrup', 'quantity' => 30],
                ['ingredient' => 'Ice Cubes', 'quantity' => 150],
            ],
            
            // Non-Coffee
            'Hot Chocolate' => [
                ['ingredient' => 'Fresh Milk', 'quantity' => 200],
                ['ingredient' => 'Chocolate Sauce', 'quantity' => 40],
            ],
            'Iced Chocolate' => [
                ['ingredient' => 'Fresh Milk', 'quantity' => 180],
                ['ingredient' => 'Chocolate Sauce', 'quantity' => 40],
                ['ingredient' => 'Ice Cubes', 'quantity' => 150],
            ],
            'Matcha Latte' => [
                ['ingredient' => 'Matcha Powder', 'quantity' => 5],
                ['ingredient' => 'Fresh Milk', 'quantity' => 200],
                ['ingredient' => 'Simple Syrup', 'quantity' => 15],
            ],
            'Iced Matcha Latte' => [
                ['ingredient' => 'Matcha Powder', 'quantity' => 5],
                ['ingredient' => 'Fresh Milk', 'quantity' => 180],
                ['ingredient' => 'Simple Syrup', 'quantity' => 15],
                ['ingredient' => 'Ice Cubes', 'quantity' => 150],
            ],
            
            // Tea
            'Hot Green Tea' => [
                ['ingredient' => 'Green Tea Leaves', 'quantity' => 3],
                ['ingredient' => 'Water', 'quantity' => 250],
            ],
            'Jasmine Tea' => [
                ['ingredient' => 'Jasmine Tea', 'quantity' => 3],
                ['ingredient' => 'Water', 'quantity' => 250],
            ],
            
            // Snacks & Pastry
            'French Fries' => [
                ['ingredient' => 'French Fries', 'quantity' => 150],
            ],
            'Cheese Fries' => [
                ['ingredient' => 'French Fries', 'quantity' => 150],
                ['ingredient' => 'Cheese', 'quantity' => 50],
            ],
            'Butter Croissant' => [
                ['ingredient' => 'Croissant', 'quantity' => 1],
                ['ingredient' => 'Butter', 'quantity' => 15],
            ],
            'Chocolate Croissant' => [
                ['ingredient' => 'Croissant', 'quantity' => 1],
                ['ingredient' => 'Chocolate Sauce', 'quantity' => 20],
            ],
            'Ham & Cheese Croissant' => [
                ['ingredient' => 'Croissant', 'quantity' => 1],
                ['ingredient' => 'Ham', 'quantity' => 30],
                ['ingredient' => 'Cheese', 'quantity' => 25],
            ],
        ];

        foreach ($recipes as $productName => $details) {
            if (!isset($productModels[$productName])) continue;
            
            $product = $productModels[$productName];
            $recipe = Recipe::firstOrCreate(['product_id' => $product->id]);
            
            foreach ($details as $detail) {
                if (!isset($ingredientModels[$detail['ingredient']])) continue;
                
                RecipeDetail::firstOrCreate(
                    [
                        'recipe_id' => $recipe->id,
                        'ingredient_id' => $ingredientModels[$detail['ingredient']]->id,
                    ],
                    ['quantity' => $detail['quantity']]
                );
            }
        }

        $this->command->info('☕ Coffee Shop data seeded successfully!');
        $this->command->info('   - ' . count($categories) . ' categories');
        $this->command->info('   - ' . count($ingredients) . ' ingredients');
        $this->command->info('   - ' . count($products) . ' products');
        $this->command->info('   - ' . count($recipes) . ' recipes');
    }
}
