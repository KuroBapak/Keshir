<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AnalyticsDemoSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // 1. SHIFTS
        // =====================
        $shiftPagi = Shift::firstOrCreate(['name' => 'Pagi'], [
            'start_time' => '07:00', 'end_time' => '14:00', 'late_threshold' => 15, 'color' => '#3b82f6',
        ]);
        $shiftSiang = Shift::firstOrCreate(['name' => 'Siang'], [
            'start_time' => '14:00', 'end_time' => '21:00', 'late_threshold' => 15, 'color' => '#f59e0b',
        ]);

        // =====================
        // 2. ASSIGN SHIFTS TO EXISTING STAFF
        // =====================
        $staffUsers = [];
        $existingStaff = User::whereHas('role', fn($q) => $q->whereIn('name', ['cashier', 'kitchen_staff', 'manager']))->get();

        foreach ($existingStaff as $user) {
            $shift = in_array($user->role->name, ['cashier', 'kitchen_staff']) ? $shiftPagi : $shiftSiang;
            if (!$user->default_shift_id) {
                $user->update(['default_shift_id' => $shift->id]);
            }
            $staffUsers[] = ['user' => $user, 'shift' => $user->defaultShift ?? $shift];
        }

        // =====================
        // 3. ATTENDANCE LOGS (3 months)
        // =====================
        $this->command->info('📋 Seeding attendance logs...');
        $products = Product::all();

        for ($monthOffset = 2; $monthOffset >= 0; $monthOffset--) {
            $startOfMonth = now()->subMonths($monthOffset)->startOfMonth();
            $endOfMonth = now()->subMonths($monthOffset)->endOfMonth();
            if ($endOfMonth->isAfter(now())) $endOfMonth = now()->copy()->subDay();

            $currentDate = $startOfMonth->copy();
            while ($currentDate->lte($endOfMonth)) {
                // Skip Sundays
                if ($currentDate->dayOfWeek === Carbon::SUNDAY) {
                    $currentDate->addDay();
                    continue;
                }

                foreach ($staffUsers as $sd) {
                    $shift = $sd['shift'];
                    $user = $sd['user'];
                    $shiftStart = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $shift->start_time);

                    // 75% chance present, 25% absent
                    if (rand(1, 100) <= 75) {
                        // Determine lateness: 20% chance of being late
                        $isLate = rand(1, 100) <= 20;
                        $lateMinutes = $isLate ? rand(5, 45) : 0;
                        $checkIn = $shiftStart->copy()->addMinutes($lateMinutes - rand(0, 5));
                        if (!$isLate) $checkIn = $shiftStart->copy()->subMinutes(rand(1, 10)); // early

                        $shiftEnd = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $shift->end_time);
                        $checkOut = $shiftEnd->copy()->addMinutes(rand(-10, 30));

                        AttendanceLog::firstOrCreate([
                            'user_id' => $user->id,
                            'date' => $currentDate->format('Y-m-d'),
                            'shift_id' => $shift->id,
                        ], [
                            'check_in' => $checkIn,
                            'check_out' => $checkOut,
                            'source' => 'web',
                            'status_in' => $isLate ? 'late' : 'on_time',
                            'status_out' => 'normal',
                        ]);
                    }
                }
                $currentDate->addDay();
            }
        }

        // =====================
        // 4. TRANSACTIONS (3 months of sales data)
        // =====================
        $this->command->info('💰 Seeding transaction history...');
        if ($products->isEmpty()) {
            $this->command->warn('⚠️ No products found. Run CoffeeShopSeeder or DemoDataSeeder first.');
            return;
        }

        $orderTypes = ['dine_in', 'dine_in', 'dine_in', 'take_away', 'take_away', 'booking'];
        $cashierId = User::whereHas('role', fn($q) => $q->where('name', 'cashier'))->first()?->id;

        for ($monthOffset = 2; $monthOffset >= 0; $monthOffset--) {
            $startOfMonth = now()->subMonths($monthOffset)->startOfMonth();
            $endOfMonth = now()->subMonths($monthOffset)->endOfMonth();
            if ($endOfMonth->isAfter(now())) $endOfMonth = now()->copy()->subDay();

            $currentDate = $startOfMonth->copy();
            while ($currentDate->lte($endOfMonth)) {
                // Generate 3-12 orders per day (increasing trend)
                $baseOrders = 3 + (2 - $monthOffset) * 2; // more orders in recent months
                $dailyOrders = rand($baseOrders, $baseOrders + 6);

                // Weekend boost
                if ($currentDate->isWeekend()) $dailyOrders += rand(2, 5);

                for ($o = 0; $o < $dailyOrders; $o++) {
                    $orderType = $orderTypes[array_rand($orderTypes)];
                    $hour = rand(8, 20);
                    $minute = rand(0, 59);
                    $createdAt = $currentDate->copy()->setHour($hour)->setMinute($minute);

                    // Pick 1-4 random products
                    $itemCount = rand(1, 4);
                    $selectedProducts = $products->random(min($itemCount, $products->count()));
                    $subtotal = 0;
                    $items = [];

                    foreach ($selectedProducts as $prod) {
                        $qty = rand(1, 3);
                        $lineTotal = $prod->base_price * $qty;
                        $subtotal += $lineTotal;
                        $items[] = ['product' => $prod, 'qty' => $qty, 'price' => $prod->base_price, 'total' => $lineTotal];
                    }

                    $tax = round($subtotal * 0.11);
                    $service = round($subtotal * 0.05);
                    $grand = $subtotal + $tax + $service;

                    // 90% paid, 5% void, 5% open
                    $statusRoll = rand(1, 100);
                    $paymentStatus = $statusRoll <= 90 ? 'paid' : ($statusRoll <= 95 ? 'void' : 'open');
                    $paymentMethod = $paymentStatus === 'paid' ? (rand(1, 3) <= 2 ? 'cash' : 'digital') : null;

                    $tableId = $orderType === 'dine_in' ? rand(1, 10) : null;

                    $tx = Transaction::create([
                        'order_type' => $orderType,
                        'source' => rand(1, 5) <= 4 ? 'pos' : 'qr',
                        'customer_name' => $orderType === 'booking' ? 'Customer ' . rand(1, 50) : null,
                        'table_id' => $tableId,
                        'subtotal' => $subtotal,
                        'tax_total' => $tax,
                        'service_total' => $service,
                        'grand_total' => $grand,
                        'payment_status' => $paymentStatus,
                        'payment_method' => $paymentMethod,
                        'cashier_id' => $cashierId,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    foreach ($items as $item) {
                        TransactionDetail::create([
                            'transaction_id' => $tx->id,
                            'product_id' => $item['product']->id,
                            'qty' => $item['qty'],
                            'price' => $item['price'],
                            'status' => 'done',
                        ]);
                    }
                }
                $currentDate->addDay();
            }
            $this->command->info("  ✅ Month -$monthOffset done");
        }

        $txCount = Transaction::count();
        $attCount = AttendanceLog::count();
        $this->command->info("🎉 Analytics demo data seeded: $txCount transactions, $attCount attendance logs");
    }
}
