<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::paginate(15);
        return view('dashboard.discounts.index', compact('discounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,nominal',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Discount::create($request->only(['name', 'type', 'value', 'is_active']));
        return back()->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,nominal',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $discount->update($request->only(['name', 'type', 'value', 'is_active']));
        return back()->with('success', 'Diskon berhasil diupdate.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return back()->with('success', 'Diskon berhasil dihapus.');
    }
}
