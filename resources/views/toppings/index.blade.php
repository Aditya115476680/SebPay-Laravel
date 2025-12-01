@extends('layout.app')

@section('content')
<div class="container mt-4 kelola-topping-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="section-title">KELOLA TOPPING</h4>
        <button id="openPopup" class="btn btn-add">+ Tambah Topping</button>
    </div>

    {{-- Grid Card --}}
    <div class="row g-4">
        @forelse($toppings as $tp)
        <div class="col-md-3 col-sm-6">
            @php
    $borderClass = '';
    if ($tp->tp_stock == 0) {
        $borderClass = 'border-empty';
    } elseif ($tp->tp_stock <= 5) {
        $borderClass = 'border-low';
    }
@endphp

<div class="topping-card shadow-sm {{ $borderClass }}">


                {{-- Gambar --}}
                <div class="topping-img">
                    @if($tp->tp_image && file_exists(public_path('images/' . $tp->tp_image)))
                        <img src="{{ asset('images/' . $tp->tp_image) }}" alt="{{ $tp->tp_name }}" class="topping-image">
                    @else
                        <img src="{{ asset('images/no-image.png') }}" alt="{{ $tp->tp_name }}" class="topping-image">
                    @endif
                </div>

                {{-- Detail --}}
                <div class="topping-info">
                    <h6 class="topping-name">{{ $tp->tp_name }}</h6>
                    @if($tp->tp_stock == 0)
                         <p class="topping-stock text-danger fw-bold">Stok Habis</p>
                    @else
                          <p class="topping-stock">Stok: <span>{{ $tp->tp_stock }}</span></p>
                    @endif

                    <p class="topping-price">Rp {{ number_format($tp->tp_price, 0, ',', '.') }}</p>
                </div>

                {{-- Tombol --}}
                <div class="topping-actions">
                    <button class="btn btn-edit" onclick="openPopup('{{ $tp->tp_id }}')">Ubah</button>

                    {{-- Hapus pakai popup konfirmasi custom --}}
                    <button type="button" class="btn btn-delete" onclick="confirmDelete('{{ $tp->tp_id }}', '{{ $tp->tp_name }}')">Hapus</button>
                    
                    <form id="deleteForm{{ $tp->tp_id }}" action="{{ route('toppings.destroy', $tp->tp_id) }}" method="POST" style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

        {{-- Popup Edit --}}
        <div id="popupEdit{{ $tp->tp_id }}" class="popup-overlay">
            <div class="popup-box">
                <h5>Edit Topping</h5>
                <form action="{{ route('toppings.update', $tp->tp_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('toppings.form', ['topping' => $tp])
                    <div class="popup-actions">
                        <button type="button" class="btn btn-secondary" onclick="closePopup('{{ $tp->tp_id }}')">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
            <div class="text-center text-muted mt-5">Belum ada topping ditambahkan</div>
        @endforelse
    </div>
</div>

<!-- Popup Tambah -->
<div id="popupOverlay" class="popup-overlay">
    <div class="popup-box">
        <h5>Tambah Topping</h5>
        <form action="{{ route('toppings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('toppings.form')
            <div class="popup-actions">
                <button type="button" id="closePopup" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-success">Tambah</button>
            </div>
        </form>
    </div>
</div>

{{-- Popup Konfirmasi Hapus --}}
<div id="popupDelete" class="popup-overlay">
    <div class="popup-box text-center">
        <h5>Konfirmasi Hapus</h5>
        <p id="deleteMessage"></p>
        <div class="popup-actions mt-3">
            <button id="confirmDeleteBtn" class="btn btn-danger">Hapus</button>
            <button type="button" id="cancelDeleteBtn" class="btn btn-secondary">Batal</button>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="toast" class="toast"></div>

{{-- === SCRIPT === --}}
<script>
function openPopup(id) {
    document.getElementById('popupEdit' + id).classList.add('show');
}
function closePopup(id) {
    document.getElementById('popupEdit' + id).classList.remove('show');
}
document.getElementById('openPopup').addEventListener('click', () => {
    document.getElementById('popupOverlay').classList.add('show');
});
document.getElementById('closePopup').addEventListener('click', () => {
    document.getElementById('popupOverlay').classList.remove('show');
});

// === Konfirmasi Hapus Custom ===
let deleteId = null;
function confirmDelete(id, name) {
    deleteId = id;
    const popup = document.getElementById('popupDelete');
    document.getElementById('deleteMessage').innerHTML = `Apakah kamu yakin ingin menghapus topping <strong>“${name}”</strong>?`;
    popup.classList.add('show');
}
document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
    if (deleteId) {
        document.getElementById('deleteForm' + deleteId).submit();
    }
});
document.getElementById('cancelDeleteBtn').addEventListener('click', () => {
    document.getElementById('popupDelete').classList.remove('show');
});

// === Toast Notification ===
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
@if ($errors->any())
    showToast("❌ {{ $errors->first() }}", 'error');
@endif



</script>
@endsection
