<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $bestSellerData = [
            'Es Kopi Susu' => 85, 'Iced Latte' => 72, 'Cappuccino' => 68,
            'Caramel Macchiato' => 55, 'Matcha Latte' => 50, 'Americano' => 48,
            'Brown Sugar Latte' => 45, 'Latte' => 42, 'Iced Americano' => 40,
            'Mocha' => 38, 'Iced Mocha' => 35, 'Vietnamese Coffee' => 32,
            'Hot Chocolate' => 28, 'Iced Chocolate' => 25, 'Coffee Frappe' => 22,
            'Cheese Fries' => 20, 'Butter Croissant' => 18, 'Hot Green Tea' => 15,
            'Thai Tea' => 14, 'French Fries' => 12, 'Vanilla Latte' => 10,
            'Hazelnut Latte' => 8, 'Jasmine Tea' => 6, 'Toast Bread' => 5,
        ];

        $n = 1;
        foreach ($bestSellerData as $productName => $totalQty) {
            $product = Product::where('name', $productName)->first();
            if (!$product) continue;

            $remaining = $totalQty;
            while ($remaining > 0) {
                $qty = min($remaining, rand(1, 3));
                $transaction = Transaction::create([
                    'order_type' => collect(['dine_in', 'take_away'])->random(),
                    'source' => 'pos',
                    'customer_name' => 'Customer ' . $n,
                    'subtotal' => $product->base_price * $qty,
                    'grand_total' => $product->base_price * $qty,
                    'payment_status' => 'paid',
                    'payment_method' => collect(['cash', 'qris'])->random(),
                ]);
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'price' => $product->base_price,
                    'status' => 'done',
                ]);
                $remaining -= $qty;
                $n++;
            }
        }

        $this->command->info('🧾 Transaction dummy data seeded!');
    }
}
