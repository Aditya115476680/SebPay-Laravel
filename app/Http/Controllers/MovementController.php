<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    // ========== HALAMAN TOPPING IN ==========
    public function inIndex()
{
    $toppings = DB::table('toppings')->orderBy('tp_name')->get();

    $movements = DB::table('topping_movements')
        ->join('toppings', 'topping_movements.tp_tp_move_id', '=', 'toppings.tp_id')
        ->where('tp_mv_type', 'in')
        ->orderBy('tp_mv_date', 'desc')
        ->select(
            'topping_movements.*',
            'toppings.tp_name',
            'toppings.tp_image' // <— ambil gambar topping
        )
        ->get();

    return view('movements.in', compact('toppings', 'movements'));
}

    public function storeIn(Request $request)
    {
        $request->validate([
            'tp_id' => 'required|exists:toppings,tp_id',
            'tp_mv_qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // Simpan ke tabel movement
            DB::table('topping_movements')->insert([
                'tp_tp_move_id' => $request->tp_id,
                'tp_mv_type' => 'in',
                'tp_mv_qty' => $request->tp_mv_qty,
                'tp_mv_reason' => $request->tp_mv_reason,
                'tp_mv_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Tambah stok topping
            DB::table('toppings')->where('tp_id', $request->tp_id)
                ->increment('tp_stock', $request->tp_mv_qty);
        });

        return back()->with('success', 'Stok topping berhasil ditambahkan!');
    }

    // ========== HALAMAN TOPPING OUT ==========
    public function outIndex()
    {
        $toppings = DB::table('toppings')->orderBy('tp_name')->get();
        $movements = DB::table('topping_movements')
            ->join('toppings', 'topping_movements.tp_tp_move_id', '=', 'toppings.tp_id')
            ->where('tp_mv_type', 'out')
            ->orderBy('tp_mv_date', 'desc')
            ->select('topping_movements.*', 'toppings.tp_name')
            ->get();

        return view('movements.out', compact('toppings', 'movements'));
    }

    public function storeOut(Request $request)
    {
        $request->validate([
            'tp_id' => 'required|exists:toppings,tp_id',
            'tp_mv_qty' => 'required|integer|min:1',
        ]);

        $topping = DB::table('toppings')->where('tp_id', $request->tp_id)->first();

        // 🔒 Validasi stok sebelum melakukan transaksi
        if (!$topping) {
            return back()->with('error', 'Topping tidak ditemukan!');
        }

        if ($request->tp_mv_qty > $topping->tp_stock) {
            return back()->with('error', 'Stok tidak cukup! (Stok tersedia: ' . $topping->tp_stock . ')');
        }

        DB::transaction(function () use ($request) {
            // Simpan ke tabel movement
            DB::table('topping_movements')->insert([
                'tp_tp_move_id' => $request->tp_id,
                'tp_mv_type' => 'out',
                'tp_mv_qty' => $request->tp_mv_qty,
                'tp_mv_reason' => $request->tp_mv_reason,
                'tp_mv_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Kurangi stok topping
            DB::table('toppings')->where('tp_id', $request->tp_id)
                ->decrement('tp_stock', $request->tp_mv_qty);
        });

        return back()->with('success', 'Stok topping berhasil dikurangi!');
    }
}
