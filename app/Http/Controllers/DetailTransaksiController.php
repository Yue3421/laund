<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Models\Paket;
use Illuminate\Http\Request;

class DetailTransaksiController extends Controller
{
    /**
     * Display a listing of the detail transaksi (bisa difilter per transaksi atau global)
     */
    public function index(Request $request)
    {
        $query = DetailTransaksi::with(['transaksi.member', 'transaksi.outlet', 'paket']);

        // Optional: filter berdasarkan transaksi (untuk laporan/detail per invoice)
        if ($request->filled('transaksi_id')) {
            $query->where('id_transaksi', $request->transaksi_id);
        }

        // Optional: filter tanggal dari transaksi
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('transaksi', function ($q) use ($request) {
                $q->whereBetween('tgl', [$request->start_date, $request->end_date]);
            });
        }

        $details = $query->latest()->paginate(20);

        // Hitung total pendapatan untuk laporan (sama seperti laporan sebelumnya)
        $totalPendapatan = $details->sum(function ($detail) {
            $hargaPaket = $detail->paket->harga ?? 0;
            $subtotal = $detail->qty * $hargaPaket;

            // Ambil data transaksi untuk diskon, pajak, biaya tambahan
            $transaksi = $detail->transaksi;
            $diskon = $transaksi->diskon ?? 0;
            $pajak = $transaksi->pajak ?? 0;
            $biayaTambahan = $transaksi->biaya_tambahan ?? 0;

            return ($subtotal - ($subtotal * $diskon)) + $pajak + $biayaTambahan;
        });

        $transaksis = Transaksi::select('id', 'kode_invoice')->get(); // untuk filter dropdown

        return view('admin.detail_transaksis.index', compact('details', 'totalPendapatan', 'transaksis'));
    }

    /**
     * Show the form for creating a new detail transaksi
     */
    public function create()
    {
        $transaksis = Transaksi::select('id', 'kode_invoice', 'id_member')->with('member')->get();
        $pakets = Paket::all();
        return view('admin.detail_transaksis.create', compact('transaksis', 'pakets'));
    }

    /**
     * Store a newly created detail transaksi
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_transaksi' => 'required|exists:un_tb_transaksi,id',
            'id_paket' => 'required|exists:un_tb_paket,id',
            'qty' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string',
        ]);

        DetailTransaksi::create($request->all());

        return redirect()->route('detail-transaksis.index')
            ->with('success', 'Detail transaksi berhasil ditambahkan.');
    }

    /**
     * Display the specified detail transaksi
     */
    public function show(DetailTransaksi $detailTransaksi)
    {
        $detailTransaksi->load(['transaksi.member', 'transaksi.outlet', 'transaksi.user', 'paket']);
        return view('admin.detail_transaksis.show', compact('detailTransaksi'));
    }

    /**
     * Show the form for editing the specified detail transaksi
     */
    public function edit(DetailTransaksi $detailTransaksi)
    {
        $transaksis = Transaksi::select('id', 'kode_invoice')->get();
        $pakets = Paket::all();
        return view('admin.detail_transaksis.edit', compact('detailTransaksi', 'transaksis', 'pakets'));
    }

    /**
     * Update the specified detail transaksi
     */
    public function update(Request $request, DetailTransaksi $detailTransaksi)
    {
        $request->validate([
            'id_transaksi' => 'required|exists:un_tb_transaksi,id',
            'id_paket' => 'required|exists:un_tb_paket,id',
            'qty' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string',
        ]);

        $detailTransaksi->update($request->all());

        return redirect()->route('detail-transaksis.index')
            ->with('success', 'Detail transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified detail transaksi
     */
    public function destroy(DetailTransaksi $detailTransaksi)
    {
        $detailTransaksi->delete();

        return redirect()->route('detail-transaksis.index')
            ->with('success', 'Detail transaksi berhasil dihapus.');
    }
}