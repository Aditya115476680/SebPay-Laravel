@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>

    <div class="cards">
        <div class="card">
            <h3>Total Topping</h3>
            <p>{{ $totalTopping }}</p>
        </div>
        <div class="card">
            <h3>Topping Masuk</h3>
            <p>{{ $totalIn }}</p>
        </div>
        <div class="card">
            <h3>Topping Keluar</h3>
            <p>{{ $totalOut }}</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold">Grafik Stok Masuk & Keluar</h5>

                <!-- Dropdown Tahun -->
                <form method="GET" action="{{ route('dashboard') }}">
                    <select name="year" class="form-select" style="width: 120px;" onchange="this.form.submit()">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <canvas id="stokChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('stokChart').getContext('2d');
const labels = {!! json_encode($movements->pluck('tp_mv_date')) !!};
const dataQty = {!! json_encode($movements->pluck('tp_mv_qty')) !!};
const dataType = {!! json_encode($movements->pluck('tp_mv_type')) !!};

const colors = dataType.map(type => type === 'in' ? 'rgba(242, 255, 161, 0.8)' : 'rgba(13, 0, 114, 0.8)');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Stok',
            data: dataQty,
            backgroundColor: colors,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});
</script>
@endsection

