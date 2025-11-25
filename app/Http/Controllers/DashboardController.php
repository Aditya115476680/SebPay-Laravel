<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // === Total topping ===
        $totalTopping = DB::table('toppings')->count();

        // === Total transaksi ===
        $totalTransaksi = DB::table('transactions')->count();

        // === Profit bulan ini (bulan berjalan) ===
        $profitBulanan = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.tr_dtl_tr_id', '=', 'transactions.tr_id')
            ->whereYear('transactions.tr_date', date('Y'))
            ->whereMonth('transactions.tr_date', date('m'))
            ->sum('transaction_details.tr_dtl_subtotal');

        // === GRAFIK 7 HARI TERAKHIR ===
        $today = now();
        $startDate = $today->copy()->subDays(6)->startOfDay();

        // Ambil stok masuk/keluar dalam 7 hari
        $rows = DB::table('topping_movements')
            ->selectRaw('DATE(tp_mv_date) as date, tp_mv_type, SUM(tp_mv_qty) as total')
            ->whereBetween('tp_mv_date', [
                $startDate->toDateString() . ' 00:00:00',
                $today->toDateString() . ' 23:59:59'
            ])
            ->groupBy('date', 'tp_mv_type')
            ->orderBy('date', 'asc')
            ->get();

        // Generate tanggal harian
        $period = [];
        $periodLabels = [];

        $dateCursor = $startDate->copy();
        while ($dateCursor->lte($today)) {
            $key = $dateCursor->toDateString();

            $period[$key] = ['in' => 0, 'out' => 0];
            $periodLabels[$key] = $dateCursor->format('d M');

            $dateCursor->addDay();
        }

        foreach ($rows as $r) {
            $d     = $r->date ?? $r['date'];
            $type  = $r->tp_mv_type ?? $r['tp_mv_type'];
            $total = (int) ($r->total ?? $r['total']);
        
            if (!isset($period[$d])) {
                $period[$d] = ['in' => 0, 'out' => 0];
                $periodLabels[$d] = \Carbon\Carbon::parse($d)->format('d M');
            }
        
            $period[$d][$type] = $total;
        }
        
        

        // Extract array ke Chart.js
        $labels = [];
        $inData = [];
        $outData = [];

        foreach ($period as $date => $vals) {
            $labels[] = $periodLabels[$date];
            $inData[] = $vals['in'];
            $outData[] = $vals['out'];
        }

        return view('dashboard', compact(
            'totalTopping',
            'totalTransaksi',
            'profitBulanan',
            'labels',
            'inData',
            'outData'
        ));
    }
}
