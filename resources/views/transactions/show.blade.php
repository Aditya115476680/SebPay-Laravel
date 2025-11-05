@extends('layout.app')

@section('content')
<h3 class="fw-bold mb-3">Detail Transaksi</h3>

<div class="card shadow-sm p-3 mb-4">
    <h5>Tanggal: {{ $transaction->trx_date }}</h5>
    <h5>Total: Rp {{ number_format($transaction->trx_total, 0, ',', '.') }}</h5>
</div>

<table class="table table-striped shadow-sm bg-white">
    <thead class="table-secondary">
        <tr>
            <th>No</th>
            <th>Nama Topping</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($details as $i => $d)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $d->tp_name }}</td>
            <td>{{ $d->qty }}</td>
            <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
@endsection
