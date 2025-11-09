@extends('layout.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="container mt-4 kelola-topping-container">
    <h4 class="section-title text-center mb-4">RIWAYAT TRANSAKSI</h4>

    {{-- === SEARCH & FILTER === --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <input type="text" id="searchInput" class="form-control w-50" placeholder="Cari tanggal, topping, atau harga...">
        <div class="d-flex align-items-center gap-2">
            <label class="fw-semibold">Dari:</label>
            <input type="date" id="fromDate" class="form-control">
            <label class="fw-semibold">Sampai:</label>
            <input type="date" id="toDate" class="form-control">
        </div>
    </div>

    {{-- === TABEL RIWAYAT === --}}
    @if($transactions->isEmpty())
        <div class="text-center text-muted mt-5">Belum ada transaksi dilakukan.</div>
    @else
    <div class="table-responsive">
        <table class="table table-striped align-middle shadow-sm" id="riwayatTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Item</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Bayar</th>
                    <th class="text-end">Kembali</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $i => $trx)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="tanggal">{{ \Carbon\Carbon::parse($trx->tr_date ?? $trx->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="item">
                            @if(!empty($trx->details) && $trx->details->count())
                                <ul class="mb-0 ps-3">
                                    @foreach($trx->details as $item)
                                        <li>{{ $item->tp_name }} (x{{ $item->qty }}) — Rp {{ number_format($item->subtotal, 0, ',', '.') }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end total">Rp {{ number_format($trx->tr_total_amount ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end bayar">Rp {{ number_format($trx->tr_payment ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end kembali">Rp {{ number_format($trx->tr_change ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <button class="btn btn-sm btn-danger lihat-struk-btn"
                                data-id="{{ $trx->tr_id }}"
                                data-date="{{ \Carbon\Carbon::parse($trx->tr_date ?? $trx->created_at)->format('d/m/Y H:i') }}"
                                data-items='@json($trx->details)'
                                data-total="{{ $trx->tr_total_amount }}"
                                data-bayar="{{ $trx->tr_payment }}"
                                data-kembali="{{ $trx->tr_change }}">
                                Lihat Struk
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- === POPUP STRUK === --}}
<div id="popupStruk" class="popup-overlay">
    <div class="popup-box struk-box text-center">
        <h4 class="fw-bold mb-1 text-danger">Transaksi Struk</h4>
        <p id="strukDate" class="text-muted mb-3"></p>

        <div id="strukItems" class="mb-3 text-start px-2"></div>

        <hr class="my-3">

        <div class="text-start mb-3 px-2">
            <p><strong>SebPay Total Pembelian</strong></p>
            <p><strong>TOTAL:</strong> <span class="float-end" id="strukTotal">Rp 0</span></p>
            <p><strong>UANG BAYAR:</strong> <span class="float-end" id="strukBayar">Rp 0</span></p>
            <p><strong>KEMBALIAN:</strong> <span class="float-end" id="strukKembali">Rp 0</span></p>
        </div>

        <hr class="my-3">

        <p class="fw-semibold text-dark mb-1">Jl. Rancamanyar / Kp. Cupu / RT 05 / RW 08 No.56</p>
        <p class="text-muted mb-3">Waroenk Seblak</p>

        <button class="btn btn-red-long" id="closeStruk">Kembali</button>
    </div>
</div>

{{-- === TOAST === --}}
<div id="toast" class="toast"></div>

{{-- === SCRIPT === --}}
<script>
const searchInput = document.getElementById('searchInput');
const fromDate = document.getElementById('fromDate');
const toDate = document.getElementById('toDate');
const table = document.getElementById('riwayatTable');
const rows = table?.querySelectorAll('tbody tr') ?? [];

function filterTable() {
    const search = searchInput.value.toLowerCase();
    const from = fromDate.value ? new Date(fromDate.value) : null;
    const to = toDate.value ? new Date(toDate.value) : null;

    rows.forEach(row => {
        const tanggal = new Date(row.querySelector('.tanggal').textContent);
        const text = row.textContent.toLowerCase();
        let match = text.includes(search);

        if (from && tanggal < from) match = false;
        if (to && tanggal > to) match = false;

        row.style.display = match ? '' : 'none';
    });
}

searchInput.addEventListener('input', filterTable);
fromDate.addEventListener('change', filterTable);
toDate.addEventListener('change', filterTable);

// === Popup Struk ===
const popupStruk = document.getElementById('popupStruk');
const strukDate = document.getElementById('strukDate');
const strukItems = document.getElementById('strukItems');
const strukTotal = document.getElementById('strukTotal');
const strukBayar = document.getElementById('strukBayar');
const strukKembali = document.getElementById('strukKembali');

document.querySelectorAll('.lihat-struk-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const items = JSON.parse(btn.dataset.items || '[]');
        const date = btn.dataset.date;
        const total = parseInt(btn.dataset.total || 0);
        const bayar = parseInt(btn.dataset.bayar || 0);
        const kembali = parseInt(btn.dataset.kembali || 0);

        strukDate.textContent = date;
        strukItems.innerHTML = items.map(i => `
            <div><span>${i.tp_name}</span><span>x${i.qty}</span><span>Rp ${i.subtotal.toLocaleString('id-ID')}</span></div>
        `).join('');
        strukTotal.textContent = "Rp " + total.toLocaleString('id-ID');
        strukBayar.textContent = "Rp " + bayar.toLocaleString('id-ID');
        strukKembali.textContent = "Rp " + kembali.toLocaleString('id-ID');

        popupStruk.classList.add('show');
    });
});

document.getElementById('closeStruk').addEventListener('click', () => popupStruk.classList.remove('show'));

// === Toast ===
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => toast.classList.remove('show'), 2500);
}

@if (session('success'))
    showToast("✅ {{ session('success') }}", 'success');
@endif
@if (session('error'))
    showToast("❌ {{ session('error') }}", 'error');
@endif
</script>
@endsection
