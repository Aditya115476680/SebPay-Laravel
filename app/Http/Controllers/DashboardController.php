<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y')); // default tahun sekarang

        $totalTopping = DB::table('toppings')->count();

        $totalIn = DB::table('topping_movements')
            ->whereYear('tp_mv_date', $year)
            ->where('tp_mv_type', 'in')
            ->sum('tp_mv_qty');

        $totalOut = DB::table('topping_movements')
            ->whereYear('tp_mv_date', $year)
            ->where('tp_mv_type', 'out')
            ->sum('tp_mv_qty');

        $movements = DB::table('topping_movements')
            ->select('tp_mv_date', 'tp_mv_qty', 'tp_mv_type')
            ->whereYear('tp_mv_date', $year)
            ->orderBy('tp_mv_date', 'asc')
            ->get();

        // Ambil tahun unik yang tersedia di tabel movement
        $years = DB::table('topping_movements')
            ->selectRaw('YEAR(tp_mv_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('dashboard', compact(
            'totalTopping', 'totalIn', 'totalOut', 'movements', 'years', 'year'
        ));
    }
}
