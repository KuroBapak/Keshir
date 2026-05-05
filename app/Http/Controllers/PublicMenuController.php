<?php

namespace App\Http\Controllers;

use App\Models\CashDrawer;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use App\Models\Transaction;
use App\Services\CartService;
use Illuminate\Http\Request;

class PublicMenuController extends Controller
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Check if the shop is open (any cashier has an active shift).
     */
    private function isShopOpen(): bool
    {
        // Any open shift (e.g., from owner or cashier) opens the shop globally
        return CashDrawer::where('status', 'open')->exists();
    }

    /**
     * Show public menu catalog.
     */
    public function index()
    {
        $isOpen = $this->isShopOpen();

        $categories = Category::has('products')->get();
        $products = Product::with(['variants', 'addons'])
            ->where('is_active', true)
            ->get();
            
        $cart = $this->cartService->getCart();
        $cartSummary = $this->cartService->getSummary();
        $tables = Table::where('status', 'available')->get();
        
        // Settings for Tax & Service
        $settings = \App\Models\Setting::pluck('value', 'key');
        $taxEnabled = filter_var($settings['tax_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $taxRate = $taxEnabled ? (float) ($settings['tax_rate'] ?? 0) : 0;
        $serviceEnabled = filter_var($settings['service_charge_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $serviceRate = $serviceEnabled ? (float) ($settings['service_charge_rate'] ?? 0) : 0;
        
        $taxAmount = ($cartSummary['subtotal'] * $taxRate) / 100;
        $serviceAmount = ($cartSummary['subtotal'] * $serviceRate) / 100;
        $grandTotal = $cartSummary['subtotal'] + $taxAmount + $serviceAmount;

        // Session order history count for badge
        $myOrderIds = session('my_orders', []);
        $myOrderCount = count($myOrderIds);
        
        return view('public.menu', compact('categories', 'products', 'cart', 'cartSummary', 'tables', 'taxRate', 'taxAmount', 'serviceRate', 'serviceAmount', 'grandTotal', 'isOpen', 'myOrderCount'));
    }

    /**
     * Show cart page.
     */
    public function cart()
    {
        $cart = $this->cartService->getCart();
        $cartSummary = $this->cartService->getSummary();
        
        // Settings for Tax & Service
        $settings = \App\Models\Setting::pluck('value', 'key');
        $taxEnabled = filter_var($settings['tax_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $taxRate = $taxEnabled ? (float) ($settings['tax_rate'] ?? 0) : 0;
        $serviceEnabled = filter_var($settings['service_charge_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $serviceRate = $serviceEnabled ? (float) ($settings['service_charge_rate'] ?? 0) : 0;
        
        $taxAmount = ($cartSummary['subtotal'] * $taxRate) / 100;
        $serviceAmount = ($cartSummary['subtotal'] * $serviceRate) / 100;
        $grandTotal = $cartSummary['subtotal'] + $taxAmount + $serviceAmount;

        return view('public.cart', compact('cart', 'cartSummary', 'taxRate', 'taxAmount', 'serviceRate', 'serviceAmount', 'grandTotal'));
    }

    /**
     * Add item to cart.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:product_addons,id',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $this->cartService->addItem(
            $request->product_id,
            $request->product_variant_id,
            $request->addons ?? [],
            $request->qty,
            $request->notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Ditambahkan ke keranjang',
            'summary' => $this->cartService->getSummary()
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(Request $request)
    {
        $request->validate(['cart_item_id' => 'required|string']);
        $this->cartService->removeItem($request->cart_item_id);
        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Update item quantity in cart.
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|string',
            'qty' => 'required|integer|min:1'
        ]);

        $this->cartService->updateQty($request->cart_item_id, $request->qty);

        return response()->json([
            'success' => true,
            'message' => 'Kuantitas diperbarui',
            'summary' => $this->cartService->getSummary()
        ]);
    }

    /**
     * Show checkout form (Dine Now / Booking)
     */
    public function checkout()
    {
        $cart = $this->cartService->getCart();
        if (empty($cart)) {
            return redirect()->route('public.menu')->with('error', 'Keranjang belanja kosong.');
        }

        // Check if shop is open (allow booking even if closed)
        $isOpen = $this->isShopOpen();

        $tables = Table::where('status', 'available')->get();
        return view('public.checkout', compact('tables', 'isOpen'));
    }

    /**
     * Show session-based order history.
     */
    public function orderHistory()
    {
        $orderIds = session('my_orders', []);
        $orders = collect();

        if (!empty($orderIds)) {
            $orders = Transaction::with(['details.product', 'details.variant', 'table', 'payment', 'booking'])
                ->whereIn('id', $orderIds)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('public.order-history', compact('orders'));
    }
}
