@extends('layout.app')

@section('title', 'Topping Masuk')

@section('content')
<div class="container mt-4 kelola-topping-container">
    <h4 class="section-title">TOPPING MASUK</h4>

    {{-- Tombol Tambah Stok --}}
    <button id="openPopup" class="btn btn-add">
        + Tambah Stok Topping
    </button>

    {{-- Popup Form Tambah --}}
    <div id="popupOverlay" class="popup-overlay">
        <div class="popup-box">
            <h5>Tambah Stok Topping</h5>
            <form action="{{ route('topping.in') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="tp_id" class="form-label">Pilih Topping</label>
                    <select name="tp_id" id="tp_id" class="form-select" required>
                        <option value="">-- Pilih Topping --</option>
                        @foreach($toppings as $tp)
                            <option value="{{ $tp->tp_id }}">{{ $tp->tp_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="tp_mv_qty" class="form-label">Jumlah Masuk</label>
                    <input type="number" name="tp_mv_qty" id="tp_mv_qty" class="form-control" min="1" required placeholder="Masukkan jumlah">
                </div>

                <div class="form-group mb-3">
                    <label for="tp_mv_reason" class="form-label">Alasan Masuk</label>
                    <input type="text" name="tp_mv_reason" id="tp_mv_reason" class="form-control" placeholder="Contoh: Restock dari supplier" required>
                </div>

                <div class="popup-actions">
                    <button type="button" id="closePopup" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Grid Card Riwayat --}}
    <div class="cards-grid mt-4">
        @forelse($movements as $mv)
        <div class="topping-card">
            @php
                $topping = $toppings->firstWhere('tp_id', $mv->tp_tp_move_id);
                $imagePath = $topping && $topping->tp_image 
                    ? asset('images/' . $topping->tp_image) 
                    : asset('images/no-image.png');
            @endphp
            <img src="{{ $imagePath }}" class="topping-image" alt="{{ $mv->tp_name }}">
            <div class="topping-info">
                <h5>{{ $mv->tp_name }}</h5>
                <p><strong>Jumlah Masuk:</strong> {{ $mv->tp_mv_qty }}</p>
                <p><strong>Alasan:</strong> {{ $mv->tp_mv_reason }}</p>
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($mv->tp_mv_date)->format('Y-m-d') }}</p>
            </div>
        </div>
        @empty
            <p class="text-center text-muted mt-3">Belum ada data topping masuk.</p>
        @endforelse
    </div>
</div>

{{-- === TOAST NOTIFICATION === --}}
<div id="toast" class="toast"></div>

{{-- === SCRIPT === --}}
<script>
document.getElementById('openPopup').addEventListener('click', () => {
    document.getElementById('popupOverlay').classList.add('show');
});
document.getElementById('closePopup').addEventListener('click', () => {
    document.getElementById('popupOverlay').classList.remove('show');
});
document.getElementById('popupOverlay').addEventListener('click', (e) => {
  if (e.target.id === 'popupOverlay') {
    document.getElementById('popupOverlay').classList.remove('show');
  }
});

// === TOAST FUNCTION ===
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

// === FLASH MESSAGE dari Laravel ===
@if (session('success'))
    showToast("✅ {{ session('success') }}", 'success');
@endif
@if (session('error'))
    showToast("❌ {{ session('error') }}", 'error');
@endif
</script>
@endsection
