@extends('layout.app')

@section('content')
<div class="container mt-4 kelola-topping-container">
    <h4 class="section-title">KELOLA TOPPING</h4>

    <button id="openPopup" class="btn btn-add">
        + Tambah Topping
    </button>

    <table class="custom-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Topping</th>
                <th>Harga (Rp)</th>
                <th>Stok</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($toppings as $i => $tp)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $tp->tp_name }}</td>
                <td>Rp {{ number_format($tp->tp_price, 0, ',', '.') }}</td>
                <td>{{ $tp->tp_stock }}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-edit" onclick="openPopup('{{ $tp->tp_id }}')">Edit</button>
                        <form action="{{ route('toppings.destroy', $tp->tp_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete" onclick="return confirm('Hapus topping ini?')">Hapus</button>
                        </form>
                    </div>
                </td>
                
            </tr>   

            {{-- Popup Edit --}}
            <div id="popupEdit{{ $tp->tp_id }}" class="popup-overlay">
                <div class="popup-box">
                    <h5>Edit Topping</h5>
                    <form action="{{ route('toppings.update', $tp->tp_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('toppings.form', ['topping' => $tp])
                        <div class="popup-actions">
                            <button type="button" class="btn btn-secondary" onclick="closePopup('{{ $tp->tp_id }}')">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
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
</script>

<style>
/* === Layout & Title === */
.kelola-topping-container {
    padding: 20px;
}
.section-title {
    font-weight: 700;
    color: #6b0000;
    margin-bottom: 15px;
}

/* === Button Tambah === */
.btn-add {
    background-color: #6b0000;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 600;
    margin-bottom: 25px;
    transition: 0.2s;
}
.btn-add:hover {
    background-color: #a00000;
}

/* === Table Styling === */
.custom-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.custom-table th {
    background-color: #600000;
    color: #fff;
    text-transform: uppercase;
    font-size: 14px;
    padding: 12px 15px;
}

.custom-table td {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    color: #333;
    font-size: 15px;
}

.custom-table tr:last-child td {
    border-bottom: none;
}

.action-buttons {
  display: flex;
  justify-content: flex-end; /* sejajarkan ke kanan */
  align-items: center;       /* posisi vertikal sejajar */
  gap: 10px;                 /* jarak antar tombol */
}

/* Align Aksi ke kanan */
.text-end {
    text-align: center;
}

.custom-table td .action-btn {
    display: flex;
    justify-content: flex-end;
    gap: 10px; /* jarak antar tombol */
}
/* === Tombol Edit === */
.btn-edit {
    background-color: #c46a00;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: 0.2s;
}
.btn-edit:hover { background-color: #e08100; }

.btn-delete {
    background-color: #a00000;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: 0.2s;
}
.btn-delete:hover { background-color: #d40000; }

/* === Popup === */
.popup-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.popup-overlay.show {
    display: flex;
    animation: fadeIn 0.2s ease-in;
}

.popup-box {
    background: #fff;
    padding: 20px;
    width: 420px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
    animation: scaleUp 0.25s ease-in-out;
}

.popup-box h5 {
    font-weight: 600;
    color: #6b0000;
    margin-bottom: 15px;
    text-align: center;
}

.popup-actions {
    display: flex;
    justify-content: end;
    gap: 10px;
    margin-top: 20px;
}

/* Animasi */
@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1;}
}
@keyframes scaleUp {
    from {transform: scale(0.8);}
    to {transform: scale(1);}
}
</style>
@endsection
