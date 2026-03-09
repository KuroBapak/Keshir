<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::paginate(20);
        return view('dashboard.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|max:10|unique:tables',
            'capacity' => 'required|integer|min:1|max:20',
        ]);

        Table::create($request->only(['table_number', 'capacity']));
        return back()->with('success', 'Meja berhasil ditambahkan.');
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|string|max:10|unique:tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:20',
        ]);

        $table->update($request->only(['table_number', 'capacity']));
        return back()->with('success', 'Meja berhasil diupdate.');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return back()->with('success', 'Meja berhasil dihapus.');
    }
}
