<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function index()
    {
        $outlets = Outlet::all();
        return view('admin.outlets.index', compact('outlets'));
    }

    public function create()
    {
        return view('admin.outlets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string',
            'tlp' => 'required|string|max:15',
        ]);

        Outlet::create($request->all());
        return redirect()->route('outlets.index')->with('success', 'Outlet created successfully.');
    }

    public function show(Outlet $outlet)
    {
        return view('admin.outlets.show', compact('outlet'));
    }

    public function edit(Outlet $outlet)
    {
        return view('admin.outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string',
            'tlp' => 'required|string|max:15',
        ]);

        $outlet->update($request->all());
        return redirect()->route('outlets.index')->with('success', 'Outlet updated successfully.');
    }

    public function destroy(Outlet $outlet)
    {
        $outlet->delete();
        return redirect()->route('outlets.index')->with('success', 'Outlet deleted successfully.');
    }
}