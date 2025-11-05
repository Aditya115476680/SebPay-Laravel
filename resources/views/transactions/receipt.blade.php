@extends('layout.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card shadow-lg border-0" style="width: 400px;">
        <div class="card-body text-center">
            <h4 class="fw-bold">SebPay Kasir</h4>
            <p class="text-muted">Jl. Transaksi Digital No.1</p>
            <hr>

            <div class="text-start mb-3">
                <p class="mb-0"><strong>No Transaksi:</strong> #{{ $trx->tr_id }}</p>
                <p class="mb-0"><strong>Tanggal:</strong> {{ $trx->tr_date }}</p>

            </div>

            <table class="table table-borderless text-start small">
                <thead>
                    <tr class="border-bottom">
                        <th>Item</th>
                        <th>Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $d)
                    <tr>
                        <td>{{ $d->tp_name }}</td>
                        <td>x{{ $d->qty }}</td>
                        <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>
            <h5 class="fw-bold">Total: Rp {{ number_format($trx->tr_total_amount, 0, ',', '.') }}</h5>

            <hr>

            <p class="text-muted small mb-1">Terima kasih telah berbelanja!</p>
            <p class="text-muted small">© SebPay {{ date('Y') }}</p>

            <a href="{{ route('transactions.history') }}" class="btn btn-secondary w-100 mt-3">Kembali</a>
        </div>
    </div>
</div>
@endsection
