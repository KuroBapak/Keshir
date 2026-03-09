<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('dashboard.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'tax_rate' => 'required|numeric|min:0|max:100',
            'service_charge_rate' => 'required|numeric|min:0|max:100',
        ]);

        $data = [
            'tax_enabled' => $request->has('tax_enabled') ? '1' : '0',
            'tax_rate' => $request->tax_rate,
            'service_charge_enabled' => $request->has('service_charge_enabled') ? '1' : '0',
            'service_charge_rate' => $request->service_charge_rate,
        ];

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
