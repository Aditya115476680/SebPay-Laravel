<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // Total topping
        $totalTopping = DB::table('toppings')->count();

        // Total transaksi
        $totalTransaksi = DB::table('transactions')->count();

        // Profit bulanan (ambil dari subtotal per bulan)
        $profitBulanan = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.tr_dtl_tr_id', '=', 'transactions.tr_id')
            ->whereYear('transactions.tr_date', $year)
            ->sum('transaction_details.tr_dtl_subtotal');

        // Data grafik stok masuk / keluar
        $movements = DB::table('topping_movements')
            ->select('tp_mv_date', 'tp_mv_qty', 'tp_mv_type')
            ->whereYear('tp_mv_date', $year)
            ->orderBy('tp_mv_date', 'asc')
            ->get();

        // Ambil daftar tahun dari data movement
        $years = DB::table('topping_movements')
            ->selectRaw('YEAR(tp_mv_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('dashboard', compact(
            'totalTopping',
            'totalTransaksi',
            'profitBulanan',
            'movements',
            'years',
            'year'
        ));
    }
}
