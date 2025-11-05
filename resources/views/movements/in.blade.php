@extends('layout.app')

@section('content')
<h3 class="fw-bold mb-3">Topping Masuk</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('topping.in.store') }}" method="POST" class="card shadow-sm p-3 mb-4">
    @csrf
    <div class="row">
        <div class="col-md-5">
            <label class="form-label">Pilih Topping</label>
            <select name="tp_id" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach($toppings as $tp)
                    <option value="{{ $tp->tp_id }}">{{ $tp->tp_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jumlah Masuk</label>
            <input type="number" name="tp_mv_qty" class="form-control" min="1" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-success w-100">Simpan</button>
        </div>
    </div>
</form>

<h5 class="mb-3">Riwayat Masuk</h5>
<table class="table table-striped shadow-sm bg-white">
    <thead class="table-success">
        <tr>
            <th>No</th>
            <th>Nama Topping</th>
            <th>Jumlah</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($movements as $i => $mv)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $mv->tp_name }}</td>
            <td>{{ $mv->tp_mv_qty }}</td>
            <td>{{ $mv->tp_mv_date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
