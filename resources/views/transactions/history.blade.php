@extends('layout.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="container mt-4 kelola-topping-container">
  <h4 class="section-title text-center mb-4">RIWAYAT TRANSAKSI</h4>

  {{-- === SEARCH & FILTER === --}}
  <div class="history-filters mb-4">
    <input type="text" id="searchInput" class="form-control search-input" placeholder="Cari Transaksi...">
    <div class="date-filter">
      <label for="fromDate" class="fw-semibold me-2">Filter tanggal</label>
      <input type="date" id="fromDate" class="form-control date-input">
    </div>
  </div>

  {{-- === TABEL RIWAYAT === --}}
  @if($transactions->isEmpty())
    <div class="text-center text-muted mt-5">Belum ada transaksi dilakukan.</div>
  @else
  <div class="table-responsive">
    <table class="custom-table text-center align-middle" id="riwayatTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal & Waktu</th>
          <th>Total</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($transactions as $i => $trx)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td class="tanggal">{{ \Carbon\Carbon::parse($trx->tr_date ?? $trx->created_at)->format('d/m/Y H:i') }}</td>
          <td>Rp {{ number_format($trx->tr_total_amount ?? 0, 0, ',', '.') }}</td>
          <td>
            <button class="btn btn-sm btn-primary lihat-struk-btn"
  E            data-id="{{ $trx->tr_id }}"
              data-date="{{ \Carbon\Carbon::parse($trx->tr_date ?? $trx->created_at)->format('d/m/Y H:i') }}"
              data-items='@json($trx->details)'
              data-total="{{ $trx->tr_total_amount }}"
              data-bayar="{{ $trx->tr_payment }}"
              data-kembali="{{ $trx->tr_change }}">
              Lihat
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- === PAGINATION === --}}
  <div class="pagination-container mt-3">
    <button id="prevPage" class="btn-pagination">&lt;</button>
    <span id="pageInfo"></span>
    <button id="nextPage" class="btn-pagination">&gt;</button>
  </div>
  @endif
</div>

{{-- === POPUP STRUK === --}}
<div id="popupStruk" class="popup-overlay">
  <div class="popup-box struk-box text-center">
    <h4 class="fw-bold mb-2 text-danger">Transaksi Struk</h4>
    <p id="strukDate" class="text-muted mb-4"></p>

    <div id="strukItems" class="mb-4 text-start px-2"></div>
    <hr class="my-4">

    <div class="text-start mb-4 px-2">
      <p class="fw-bold mb-3">SebPay Total Pembelian</p>

      <div class="d-flex justify-content-between mb-2">
        <span class="fw-semibold">TOTAL</span>
        <span id="strukTotal">Rp 0</span>
      </div>

      <div class="d-flex justify-content-between mb-2">
        <span class="fw-semibold">UANG BAYAR</span>
        <span id="strukBayar">Rp 0</span>
      </div>

      <div class="d-flex justify-content-between">
        <span class="fw-semibold">KEMBALIAN</span>
        <span id="strukKembali">Rp 0</span>
      </div>
    </div>

    <hr class="my-4">

    <div class="struk-footer">
      <p class="alamat">Jl. Rancamanyar / Kp. Cupu / RT 05 / RW 08 No.56</p>
      <p class="text-muted">Waroenk Seblak</p>
  </div>
  

    <button class="btn btn-red-long mt-3" id="closeStruk">Kembali</button>
  </div>
</div>


{{-- === TOAST === --}}
<div id="toast" class="toast"></div>
{{-- === SCRIPT === --}}
<script>
    const searchInput = document.getElementById('searchInput');
    const fromDate = document.getElementById('fromDate');
    const table = document.getElementById('riwayatTable');
    const rows = Array.from(table?.querySelectorAll('tbody tr') ?? []);
    
    let filteredRows = [...rows];
    let currentPage = 1;
    const rowsPerPage = 5;
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');
    
    function filterTable() {
      const search = searchInput.value.toLowerCase();
      const from = fromDate.value ? new Date(fromDate.value) : null;
    
      // Filter data sesuai input
      filteredRows = rows.filter(row => {
        const tanggal = new Date(row.querySelector('.tanggal').textContent);
        const text = row.textContent.toLowerCase();
        let match = text.includes(search);
    
        if (from && tanggal.toDateString() !== from.toDateString()) match = false;
        return match;
      });
    
      currentPage = 1;
      showPage(currentPage);
    }
    
    function showPage(page) {
      // sembunyikan semua baris dulu
      rows.forEach(row => row.style.display = 'none');
    
      const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;
    
      // tampilkan hanya baris yg sesuai halaman
      filteredRows.slice(start, end).forEach(row => row.style.display = '');
    
      // update info pagination
      pageInfo.textContent = `${totalPages === 0 ? 0 : page} / ${totalPages}`;
      prevBtn.disabled = page <= 1;
      nextBtn.disabled = page >= totalPages || totalPages === 0;
    }
    
    // event listener
    searchInput.addEventListener('input', filterTable);
    fromDate.addEventListener('change', filterTable);
    prevBtn.addEventListener('click', () => { if (currentPage > 1) showPage(--currentPage); });
    nextBtn.addEventListener('click', () => { if (currentPage < Math.ceil(filteredRows.length / rowsPerPage)) showPage(++currentPage); });
    
    // jalankan awal
    filterTable();
    
    
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

    strukDate.textContent = btn.dataset.date;

    strukItems.innerHTML = items.map(i => `
      <div class="struk-item">
        <span>${i.name}</span>
        <span>x${i.qty}</span>
        <span>Rp ${Number(i.subtotal).toLocaleString('id-ID')}</span>
      </div>
    `).join('');

    strukTotal.textContent  = "Rp " + Number(btn.dataset.total).toLocaleString('id-ID');
    strukBayar.textContent  = "Rp " + Number(btn.dataset.bayar).toLocaleString('id-ID');
    strukKembali.textContent = "Rp " + Number(btn.dataset.kembali).toLocaleString('id-ID');

    popupStruk.classList.add('show');
  });
});


    
    document.getElementById('closeStruk').addEventListener('click', () => popupStruk.classList.remove('show'));
    </script>
    
    @endsection