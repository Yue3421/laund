<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\Paket;

class PaketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paket = paket::with('outlet')->get();
        return view('admin.pakets.index', compact('paket'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = \app\Models\Outlet::all();
        return view('admin.pakets.create', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_outlet' => 'required|exists:un_tb_outlet,id',
            'jenis' => 'required|in:kiloan,selimut,bed_cover,kaos,lain',
            'nama_paket' => 'required|string|max:100',
            'harga' => 'required|integer',
        ]);

        Paket::create($request->all());
        return redirect()->route('pakets.index')->with('success', 'Paket created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paket $paket)
    {
        $paket->load('outlet');
        return view('admin.pakets.show', compact('paket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paket $paket)
    {
        $outlets = \App\Models\Outlet::all();
        return view('admin.pakets.edit', compact('paket', 'outlets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paket $paket)
    {
        $request->validate([
            'id_outlet' => 'required|exists:un_tb_outlet,id',
            'jenis' => 'required|in:kiloan,selimut,bed_cover,kaos,lain',
            'nama_paket' => 'required|string|max:100',
            'harga' => 'required|integer',
        ]);

        $paket->update($request->all());
        return redirect()->route('pakets.index')->with('success', 'Paket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paket $paket)
    {
        $paket->delete();
        return redirect()->route('pakets.index')->with('success', 'Paket deleted successfully.');
    }
}