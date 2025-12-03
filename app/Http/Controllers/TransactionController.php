<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index()
    {
        $toppings = DB::table('toppings')->orderBy('tp_name')->get();
        $transactions = DB::table('transactions')->orderBy('tr_date', 'desc')->get();

        return view('transactions.index', compact('toppings', 'transactions'));
    }

    public function store(Request $request)
{
    $items = json_decode($request->items_json, true);

    if (!$items || count($items) === 0) {
        return back()->with('error', 'Tidak ada item dalam transaksi!');
    }

    $total = 0;

    foreach ($items as $item) {
        $topping = DB::table('toppings')->where('tp_id', $item['id'])->first();
        if (!$topping || $topping->tp_stock < $item['qty']) {
            return back()->with('error', 'Stok topping tidak cukup!');
        }
        $total += $topping->tp_price * $item['qty'];
    }

    // ============================
    // VALIDASI UANG BAYAR
    // ============================
    $bayar = $request->bayar;

    // jika tidak diisi atau kurang dari 1
    if ($bayar === null || $bayar === "" || $bayar <= 0) {
        return back()->with('error', 'Uang bayar harus diisi dan tidak boleh 0!');
    }

    // jika uang tidak cukup
    if ($bayar < $total) {
        $kurang = $total - $bayar;
        return back()->with('error', 'Uang bayar kurang Rp ' . number_format($kurang, 0, ',', '.'));
    }

    // ================================
    // SIMPAN TRANSAKSI
    // ================================
    DB::transaction(function () use ($items, $total, $request) {

        $tr_id = DB::table('transactions')->insertGetId([
            'tr_total_amount' => $total,
            'tr_payment'      => $request->bayar,
            'tr_change'       => $request->bayar - $total,
            'tr_date'         => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        foreach ($items as $item) {

            $topping = DB::table('toppings')->where('tp_id', $item['id'])->first();

            DB::table('transaction_details')->insert([
                'tr_dtl_tr_id'    => $tr_id,
                'tr_dtl_tp_id'    => $item['id'],
                'tp_name'         => $topping->tp_name,
                'tp_price'        => $topping->tp_price,
                'tr_dtl_qty'      => $item['qty'],
                'tr_dtl_subtotal' => $topping->tp_price * $item['qty'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            DB::table('toppings')
                ->where('tp_id', $item['id'])
                ->decrement('tp_stock', $item['qty']);
        }
    });

    return redirect()->route('transactions.index')
        ->with('success', 'Transaksi berhasil disimpan!');
}



    public function history()
    {
        $transactions = DB::table('transactions')
            ->orderByDesc('created_at')
            ->get();

        // ambil detail TANPA join toppings
        $transactions->transform(function ($trx) {
            $trx->details = DB::table('transaction_details')
                ->where('tr_dtl_tr_id', $trx->tr_id)
                ->select(
                    'tp_name as name',
                    'tp_price as price',
                    'tr_dtl_qty as qty',
                    'tr_dtl_subtotal as subtotal'
                )
                ->get();
            return $trx;
        });

        return view('transactions.history', compact('transactions'));
    }


    public function receipt($id)
    {
        $trx = DB::table('transactions')->where('tr_id', $id)->first();

        if (!$trx)
            return redirect()->route('transactions.history')->with('error', 'Transaksi tidak ditemukan!');

        $details = DB::table('transaction_details')
            ->where('tr_dtl_tr_id', $id)
            ->select(
                'tr_dtl_name as name',
                'tr_dtl_price as price',
                'tr_dtl_qty as qty',
                'tr_dtl_subtotal as subtotal'
            )
            ->get();

        return view('transactions.receipt', compact('trx', 'details'));
    }

    public function cetakStruk($id)
    {
        // Ambil transaksi
        $trx = DB::table('transactions')->where('tr_id', $id)->first();
    
        if (!$trx) {
            abort(404);
        }
    
        // Ambil detail transaksi (pakai kolom yg benar)
        $details = DB::table('transaction_details')
            ->where('tr_dtl_tr_id', $id)
            ->get();
    
        // Load view PDF
        $pdf = Pdf::loadView('pdf.struk', [
            'trx' => $trx,
            'details' => $details
        ])->setPaper([0, 0, 226.77, 600], 'portrait');
    
        return $pdf->stream("struk-$id.pdf");
    }
    
}
