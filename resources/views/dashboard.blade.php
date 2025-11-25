@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    <h2 class="dashboard-title">Dashboard</h2>

    <!-- Statistik Cards -->
    <div class="dashboard-cards">
        <div class="dash-card">
            <h4>Total Topping</h4>
            <p>{{ $totalTopping }}</p>
        </div>
        <div class="dash-card">
            <h4>Total Transaksi</h4>
            <p>{{ $totalTransaksi }}</p>
        </div>
        <div class="dash-card">
            <h4>Profit Bulanan</h4>
            <p>Rp {{ number_format($profitBulanan, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Grafik 7 Hari Terakhir -->
    <div class="grafik-card mt-4 p-3">
        <h5 class="mb-3">Grafik Stok 7 Hari Terakhir</h5>
        <canvas id="stokChart7" height="120"></canvas>
        <p class="note">Update otomatis setiap hari</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const labels = {!! json_encode($labels) !!};
const inData  = {!! json_encode($inData) !!};
const outData = {!! json_encode($outData) !!};

new Chart(document.getElementById('stokChart7'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: "Stok Masuk",
                data: inData,
                backgroundColor: "#ffee77",
                borderRadius: 8
            },
            {
                label: "Stok Keluar",
                data: outData,
                backgroundColor: "#ff5555",
                borderRadius: 8
            }
        ]
    },
    options: {
    responsive: true,
    scales: {
        x: {
            stacked: true,
            ticks: { color: "#fff" },
            grid: {
                color: "rgba(255,255,255,0.25)",   // <-- GARIS GRID PUTIH
                borderColor: "rgba(255,255,255,0.4)"
            }
        },
        y: {
            beginAtZero: true,
            stacked: true,
            ticks: { color: "#fff", precision: 0 },
            grid: {
                color: "rgba(255,255,255,0.25)",   // <-- GARIS GRID PUTIH
                borderColor: "rgba(255,255,255,0.4)"
            }
        }
    },
    plugins: {
        legend: {
            labels: { color: "#fff" }
        }
    }
}

});
</script>

@endsection
