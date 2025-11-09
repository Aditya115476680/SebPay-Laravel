<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if ($topping && $topping->tp_stock >= $item['qty']) {
            $total += $topping->tp_price * $item['qty'];
        } else {
            return back()->with('error', 'Stok topping ' . $topping->tp_name . ' tidak cukup!');
        }
    }

    DB::transaction(function () use ($items, $total, $request) {
        $tr_id = DB::table('transactions')->insertGetId([
            'tr_total_amount' => $total,
            'tr_payment' => $request->bayar,
            'tr_change' => $request->bayar - $total,
            'tr_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($items as $item) {
            $topping = DB::table('toppings')->where('tp_id', $item['id'])->first();

            DB::table('transaction_details')->insert([
                'tr_dtl_tr_id' => $tr_id,
                'tr_dtl_tp_id' => $item['id'],
                'tr_dtl_qty' => $item['qty'],
                'tr_dtl_subtotal' => $topping->tp_price * $item['qty'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('toppings')->where('tp_id', $item['id'])
                ->decrement('tp_stock', $item['qty']);
        }
    });

    return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan!');
}


    public function show($id)
    {
        $transaction = DB::table('transactions')->where('tr_id', $id)->first();
        $details = DB::table('transaction_details')
            ->join('toppings', 'transaction_details.tp_id', '=', 'toppings.tp_id')
            ->where('transaction_details.tr_id', $id)
            ->select('toppings.tp_name', 'transaction_details.*')
            ->get();

        return view('transactions.show', compact('transaction', 'details'));
    }

    public function history()
    {
        $transactions = DB::table('transactions')
            ->orderByDesc('created_at')
            ->get();
    
        // Ambil detail per transaksi
        $transactions->transform(function($trx) {
            $details = DB::table('transaction_details')
                ->join('toppings', 'transaction_details.tr_dtl_tp_id', '=', 'toppings.tp_id')
                ->where('transaction_details.tr_dtl_tr_id', $trx->tr_id)
                ->select(
                    'toppings.tp_name',
                    'transaction_details.tr_dtl_qty as qty',
                    'transaction_details.tr_dtl_subtotal as subtotal'
                )
                ->get();
    
            $trx->details = $details;
            return $trx;
        });
    
        return view('transactions.history', compact('transactions'));
    }
    

    public function receipt($id)
    {
        $trx = DB::table('transactions')->where('tr_id', $id)->first();
        $details = DB::table('transaction_details')
    ->join('toppings', 'transaction_details.tr_dtl_tp_id', '=', 'toppings.tp_id')
    ->where('transaction_details.tr_dtl_tr_id', $id)
    ->select(
        'toppings.tp_name',
        'toppings.tp_price',
        'transaction_details.tr_dtl_qty as qty',
        'transaction_details.tr_dtl_subtotal as subtotal'
    )
    ->get();

        if (!$trx) {
            return redirect()->route('transactions.history')->with('error', 'Transaksi tidak ditemukan!');
        }

        return view('transactions.receipt', compact('trx', 'details'));
    }
}
