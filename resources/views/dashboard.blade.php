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

    <!-- Grafik -->
    <div class="grafik-card">
        <div class="grafik-header">
            <h5>Grafik Stok Masuk & Keluar</h5>
            <form method="GET" action="{{ route('dashboard') }}">
                <select name="year" class="year-select" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <canvas id="stokChart" height="100"></canvas>
        <p class="note">Data per bulan tahun <strong>{{ $year }}</strong></p>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('stokChart').getContext('2d');
const labels = {!! json_encode($movements->pluck('tp_mv_date')) !!};
const dataQty = {!! json_encode($movements->pluck('tp_mv_qty')) !!};
const dataType = {!! json_encode($movements->pluck('tp_mv_type')) !!};

const colors = dataType.map(type => type === 'in' ? '#ffee88' : '#ff4444');

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
        plugins: {
            legend: {
                display: false,
                labels: {
                    color: '#fff' // warna teks putih di legend
                }
            }
        },
        scales: {
            x: {
                ticks: { color: '#fff' }, // label bulan putih
                grid: { color: 'rgba(255,255,255,0.2)' } // garis grid lembut
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#fff', precision: 0 },
                grid: { color: 'rgba(255,255,255,0.2)' }
            }
        }
    }
});

</script>

<style>
    .dashboard-container {
        padding: 30px;
        color: #fff;
    }
    
    .dashboard-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #4d0000;
        margin-bottom: 25px;
    }
    
    /* CARD STATISTIK */
    .dashboard-cards {
        display: flex;
        justify-content: space-between;
        gap: 25px;
        margin-bottom: 35px;
        flex-wrap: wrap;
    }
    
    /* Warna cards dibedakan */
    .dash-card:nth-child(1) {
        background: #7C0A02; /* maroon tua */
    }
    .dash-card:nth-child(2) {
        background: #A01515; /* merah elegan */
    }
    .dash-card:nth-child(3) {
        background: #C7372F; /* merah bata */
    }
    
    .dash-card {
        border-radius: 15px;
        padding: 25px;
        flex: 1;
        min-width: 250px;
        text-align: center;
        color: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: 0.3s;
    }
    
    .dash-card:hover {
        transform: translateY(-5px);
        filter: brightness(1.1);
    }
    
    .dash-card h4 {
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    
    .dash-card p {
        font-size: 1.4rem;
        font-weight: bold;
        margin: 0;
    }
    
    /* GRAFIK SECTION */
    .grafik-card {
        background: #8b0000;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    
    .grafik-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .grafik-header h5 {
        margin: 0;
        font-weight: 600;
        color: #ffffff
    }
    
    .year-select {
        padding: 5px 10px;
        border-radius: 8px;
        border: none;
        outline: none;
        background: #fff;
        color: #8b0000;
        font-weight: 600;
    }
    
    .note {
        margin-top: 15px;
        text-align: center;
        font-size: 0.9rem;
        color: #000000;
    }
    <style>
.dashboard-container {
    padding: 30px;
    color: #fff;
}

.dashboard-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #4d0000;
    margin-bottom: 25px;
}

/* CARD STATISTIK */
.dashboard-cards {
    display: flex;
    justify-content: space-between;
    gap: 25px;
    margin-bottom: 35px;
    flex-wrap: wrap;
}

/* Warna cards dibedakan */
.dash-card:nth-child(1) {
    background: #7C0A02; /* maroon tua */
}
.dash-card:nth-child(2) {
    background: #A01515; /* merah elegan */
}
.dash-card:nth-child(3) {
    background: #C7372F; /* merah bata */
}

.dash-card {
    border-radius: 15px;
    padding: 25px;
    flex: 1;
    min-width: 250px;
    text-align: center;
    color: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: 0.3s;
}

.dash-card:hover {
    transform: translateY(-5px);
    filter: brightness(1.1);
}

.dash-card h4 {
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 1.1rem;
}

.dash-card p {
    font-size: 1.4rem;
    font-weight: bold;
    margin: 0;
}

/* GRAFIK SECTION */
.grafik-card {
    background: #330000;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.grafik-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.grafik-header h5 {
    margin: 0;
    font-weight: 600;
}

.year-select {
    padding: 5px 10px;
    border-radius: 8px;
    border: none;
    outline: none;
    background: #fff;
    color: #8b0000;
    font-weight: 600;
    
}

.note {
    margin-top: 15px;
    text-align: center;
    font-size: 0.9rem;
    color: #ffffff;
}
</style>

    
@endsection
