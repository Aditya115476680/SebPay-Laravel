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
    <table class="custom-table text-left align-middle" id="riwayatTable">
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
                data-id="{{ $trx->tr_id }}"
                data-date="{{ \Carbon\Carbon::parse($trx->tr_date ?? $trx->created_at)->format('d/m/Y H:i') }}"
                data-items='@json($trx->details)'
                data-total="{{ $trx->tr_total_amount }}"
                data-bayar="{{ $trx->tr_payment }}"
                data-kembali="{{ $trx->tr_change }}">
                Lihat
              </button>
          
              <a href="{{ route('transaksi.cetak', $trx->tr_id) }}" target="_blank"
                 class="btn btn-sm btn-warning mt-1">
                 Cetak
              </a>
          
          
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
  <div class="popup-box struk-box">

    <h4>Transaksi Struk</h4>
    <p id="strukDate"></p>

    <div class="struk-separator"></div>

    <div id="strukItems"></div>

    <div class="struk-separator"></div>

    <div class="struk-summary px-2">
      <div class="row">
        <span>TOTAL</span>
        <span id="strukTotal"></span>
      </div>
      <div class="row">
        <span>UANG BAYAR</span>
        <span id="strukBayar"></span>
      </div>
      <div class="row">
        <span>KEMBALIAN</span>
        <span id="strukKembali"></span>
      </div>
    </div>

    <div class="struk-separator"></div>

    <div class="struk-footer">
      <p class="alamat">Jl. Rancamanyar / Kp. Cupu / RT 05 / RW 08 No.56</p>
      <p>Waroenk Seblak</p>
    </div>

    <button class="btn btn-red-long" id="closeStruk">Kembali</button>
  </div>
</div>

<style>

/* POPUP STRUK STYLE */
.struk-box {
  width: 320px; /* seperti kertas struk thermal */
  background: #fff;
  padding: 22px;
  border-radius: 4px;
  box-shadow: 0 0 3px rgba(0,0,0,.4);
  font-family: "Courier New", monospace; /* FONT PRINTER STRUK */
  color: #000;
  text-align: left !important;
}



/* Judul & Tanggal */
.struk-box h4 {
  font-size: 18px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 4px;
  color: #000 !important;
}
#strukDate {
  text-align: center;
  font-size: 12px;
  margin-bottom: 12px;
}

/* Separator garis */
.struk-separator {
  border-top: 1px dashed #000;
  margin: 8px 0;
}

/* LIST BARANG */
#strukItems .item-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 3px;
}

/* BAGIAN SUMMARY */
.struk-summary .row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  margin-bottom: 5px;
  font-weight: bold;
}

/* Footer */
.struk-footer {
  text-align: center;
  font-size: 12px;
  margin-top: 12px;
}
.struk-footer .alamat {
  font-weight: 600;
  margin-bottom: 2px;
}

/* Tombol */
#closeStruk {
  margin-top: 18px;
  width: 100%;
  padding: 10px 0;
  background: #7b0000;
  border-radius: 6px;
  font-family: Arial;
}


</style>

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