@extends('layout.app')

@section('content')
<h3 class="fw-bold mb-4">Transaksi Kasir</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- Form transaksi -->
<form id="trxForm" action="{{ route('transactions.store') }}" method="POST">
    @csrf
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-semibold mb-3">Tambah Transaksi</h5>

            <div id="itemList">
                <div class="row mb-2 itemRow">
                    <div class="col-md-5">
                        <select name="items[0][tp_id]" class="form-select" required>
                            <option value="">-- Pilih Topping --</option>
                            @foreach($toppings as $tp)
                                <option value="{{ $tp->tp_id }}">{{ $tp->tp_name }} (Rp {{ number_format($tp->tp_price, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="items[0][qty]" class="form-control" placeholder="Qty" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger removeRow w-100">Hapus</button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-primary" id="addRow">+ Tambah Item</button>
            <button type="submit" class="btn btn-success float-end">Simpan Transaksi</button>
        </div>
    </div>
</form>

<!-- Daftar Transaksi -->
<h5 class="mb-3">Riwayat Transaksi</h5>
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

<script>
let index = 1;
document.getElementById('addRow').addEventListener('click', () => {
    const div = document.createElement('div');
    div.classList.add('row', 'mb-2', 'itemRow');
    div.innerHTML = `
        <div class="col-md-5">
            <select name="items[${index}][tp_id]" class="form-select" required>
                <option value="">-- Pilih Topping --</option>
                @foreach($toppings as $tp)
                    <option value="{{ $tp->tp_id }}">{{ $tp->tp_name }} (Rp {{ number_format($tp->tp_price, 0, ',', '.') }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" name="items[${index}][qty]" class="form-control" placeholder="Qty" min="1" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger removeRow w-100">Hapus</button>
        </div>
    `;
    document.getElementById('itemList').appendChild(div);
    index++;
});

document.addEventListener('click', e => {
    if (e.target.classList.contains('removeRow')) {
        e.target.closest('.itemRow').remove();
    }
});
</script>
@endsection
