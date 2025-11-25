<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\Paket;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['outlet', 'member', 'user'])->get();
        return view('admin.transaksis.index', compact('transaksis'));
    }

    public function create()
    {
        $members = Member::all();
        $outlets = Outlet::all();
        $pakets = Paket::all();
        return view('admin.transaksis.create', compact('members', 'outlets', 'pakets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_outlet' => 'required|exists:un_tb_outlet,id',
            'kode_invoice' => 'required|string|max:100|unique:un_tb_transaksi',
            'id_member' => 'required|exists:un_tb_member,id',
            'tgl' => 'required|date',
            'batas_waktu' => 'required|date|after_or_equal:tgl',
            'biaya_tambahan' => 'required|integer|min:0',
            'diskon' => 'required|numeric|min:0|max:1',
            'pajak' => 'required|integer|min:0',
            'status' => 'required|in:baru,proses,selesai,diambil',
            'dibayar' => 'required|in:dibayar,belum_dibayar',
            // For details
            'paket_ids' => 'required|array',
            'paket_ids.*' => 'exists:un_tb_paket,id',
            'qtys' => 'required|array',
            'qtys.*' => 'numeric|min:0',
            'keterangans' => 'array',
        ]);

        $transaksi = Transaksi::create([
            'id_outlet' => $request->id_outlet,
            'kode_invoice' => $request->kode_invoice,
            'id_member' => $request->id_member,
            'tgl' => $request->tgl,
            'batas_waktu' => $request->batas_waktu,
            'tgl_bayar' => $request->dibayar === 'dibayar' ? now() : null,
            'biaya_tambahan' => $request->biaya_tambahan,
            'diskon' => $request->diskon,
            'pajak' => $request->pajak,
            'status' => $request->status,
            'dibayar' => $request->dibayar,
            'id_user' => Auth::id(),
        ]);

        // Add details
        foreach ($request->paket_ids as $index => $paket_id) {
            DetailTransaksi::create([
                'id_transaksi' => $transaksi->id,
                'id_paket' => $paket_id,
                'qty' => $request->qtys[$index],
                'keterangan' => $request->keterangans[$index] ?? '',
            ]);
        }

        return redirect()->route('transaksis.index')->with('success', 'Transaksi created successfully.');
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['outlet', 'member', 'user', 'detailTransaksis.paket']);
        return view('admin.transaksis.show', compact('transaksi'));
    }

    public function edit(Transaksi $transaksi)
    {
        $members = Member::all();
        $outlets = Outlet::all();
        $pakets = Paket::all();
        $transaksi->load('detailTransaksis');
        return view('admin.transaksis.edit', compact('transaksi', 'members', 'outlets', 'pakets'));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'id_outlet' => 'required|exists:un_tb_outlet,id',
            'kode_invoice' => 'required|string|max:100|unique:un_tb_transaksi,kode_invoice,' . $transaksi->id,
            'id_member' => 'required|exists:un_tb_member,id',
            'tgl' => 'required|date',
            'batas_waktu' => 'required|date|after_or_equal:tgl',
            'biaya_tambahan' => 'required|integer|min:0',
            'diskon' => 'required|numeric|min:0|max:1',
            'pajak' => 'required|integer|min:0',
            'status' => 'required|in:baru,proses,selesai,diambil',
            'dibayar' => 'required|in:dibayar,belum_dibayar',
            // For details
            'paket_ids' => 'required|array',
            'paket_ids.*' => 'exists:un_tb_paket,id',
            'qtys' => 'required|array',
            'qtys.*' => 'numeric|min:0',
            'keterangans' => 'array',
        ]);

        $data = $request->all();
        $data['tgl_bayar'] = $request->dibayar === 'dibayar' ? ($transaksi->tgl_bayar ?? now()) : null;

        $transaksi->update($data);

        // Update details: delete old, add new
        $transaksi->detailTransaksis()->delete();
        foreach ($request->paket_ids as $index => $paket_id) {
            DetailTransaksi::create([
                'id_transaksi' => $transaksi->id,
                'id_paket' => $paket_id,
                'qty' => $request->qtys[$index],
                'keterangan' => $request->keterangans[$index] ?? '',
            ]);
        }

        return redirect()->route('transaksis.index')->with('success', 'Transaksi updated successfully.');
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->detailTransaksis()->delete();
        $transaksi->delete();
        return redirect()->route('transaksis.index')->with('success', 'Transaksi deleted successfully.');
    }
}