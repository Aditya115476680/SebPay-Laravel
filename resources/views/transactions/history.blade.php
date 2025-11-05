@extends('layout.app')

@section('content')
<h3 class="fw-bold mb-4">Riwayat Transaksi</h3>

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table class="table table-striped shadow-sm bg-white">
    <thead class="table-primary">
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Total</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $i => $trx)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $trx->tr_date }}</td>
<td>Rp {{ number_format($trx->tr_total_amount, 0, ',', '.') }}</td>
<a href="{{ route('transactions.receipt', $trx->tr_id) }}" class="btn btn-sm btn-info">Lihat Struk</a>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
