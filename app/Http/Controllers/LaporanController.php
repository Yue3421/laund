<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['outlet', 'member', 'user', 'detailTransaksis.paket']);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tgl', [$request->start_date, $request->end_date]);
        }

        if ($request->has('outlet_id')) {
            $query->where('id_outlet', $request->outlet_id);
        }

        $transaksis = $query->get();

        // Calculate totals, etc.
        $totalPendapatan = $transaksis->sum(function ($transaksi) {
            $subtotal = $transaksi->detailTransaksis->sum(function ($detail) {
                return $detail->qty * $detail->paket->harga;
            });
            return $subtotal + $transaksi->biaya_tambahan + $transaksi->pajak - ($subtotal * $transaksi->diskon);
        });

        return view('admin.laporans.index', compact('transaksis', 'totalPendapatan'));
    }
}