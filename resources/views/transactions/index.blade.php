@extends('layout.app')

@section('title', 'Transaksi')

@section('content')
<div class="container mt-4 kelola-topping-container">
    <h4 class="section-title text-center">TRANSAKSI</h4>

    <form action="{{ route('transactions.store') }}" method="POST" id="formTransaksi">
        @csrf

        <div class="transaksi-container">
            {{-- === DAFTAR TOPPING (KIRI) === --}}
            <div class="cards-grid">
                @foreach($toppings as $tp)
                <div class="topping-card {{ $tp->tp_stock == 0 ? 'disabled-card' : '' }}"
                    data-id="{{ $tp->tp_id }}"
                    data-name="{{ $tp->tp_name }}"
                    data-price="{{ $tp->tp_price }}"
                    data-stock="{{ $tp->tp_stock }}">
               
                    
                    <img src="{{ asset('images/' . ($tp->tp_image ?? 'no-image.png')) }}" 
                         class="topping-image" alt="{{ $tp->tp_name }}">
                    <div class="card-body">
                        <h6>{{ $tp->tp_name }}</h6>
                        @if($tp->tp_stock == 0)
    <p class="text-danger fw-bold">Stok Habis</p>
@else
    <p>Stok: <strong>{{ $tp->tp_stock }}</strong></p>
@endif

                        <div class="qty-buttons">
                            <button type="button" class="btn-qty minus">−</button>
                            <span class="qty">0</span>
                            <button type="button" class="btn-qty plus">+</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- === DAFTAR PESANAN (KANAN) === --}}
            <div class="order-panel">
                <h5>Daftar Pesanan</h5>
                <div id="orderList" class="order-list"></div>

                <div class="order-total mt-2">
                    <strong>TOTAL</strong>
                    <span id="totalHarga">Rp 0</span>
                </div>

                <input type="hidden" name="items_json" id="itemsJson">

                <button type="button" id="btnBayar" class="btn btn-pay mt-3" disabled>Bayar</button>
            </div>
        </div>

        {{-- === POPUP KONFIRMASI PEMBAYARAN === --}}
        <div id="popupOverlay" class="popup-overlay">
            <div class="popup-box">
                <h5>Konfirmasi Pembayaran</h5>

                <div class="mb-2 d-flex justify-content-between">
                    <label>Total Harga</label>
                    <span id="popupTotalHarga">Rp 0</span>
                </div>

                <div class="mb-2">
                    <label>Uang Bayar</label>
                    <input type="number" id="uangBayar" name="bayar" class="form-control" placeholder="Masukkan nominal" min="0" required>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <label>Kembalian</label>
                    <span id="popupKembalian">Rp 0</span>
                </div>

                <div class="popup-actions">
                    <button type="button" id="closePopup" class="btn btn-secondary">Batal</button>
                    <button type="submit" id="btnSelesai" class="btn btn-success">Selesai</button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- === TOAST NOTIFICATION === --}}
<div id="toast" class="toast"></div>

<style>
/* === TOAST STYLE === */
.toast {
  position: fixed;
  top: 25px;
  right: 25px;
  background: #fff;
  color: #222;
  padding: 14px 20px;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.15);
  font-weight: 600;
  font-size: 14px;
  z-index: 10000;
  display: none;
  align-items: center;
  gap: 10px;
  border-left: 6px solid #7b0000;
  animation: slideIn 0.3s ease;
}
.toast.show { display: flex; }
.toast.success { border-left-color: #1fa83d; color: #1fa83d; }
.toast.error { border-left-color: #b00000; color: #b00000; }
@keyframes slideIn {
  from { opacity: 0; transform: translateX(50px); }
  to { opacity: 1; transform: translateX(0); }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const orderList = document.getElementById("orderList");
    const totalHargaEl = document.getElementById("totalHarga");
    const btnBayar = document.getElementById("btnBayar");
    const popup = document.getElementById("popupOverlay");
    const popupTotal = document.getElementById("popupTotalHarga");
    const popupKembalian = document.getElementById("popupKembalian");
    const uangBayar = document.getElementById("uangBayar");
    const itemsJson = document.getElementById("itemsJson");
    const formTransaksi = document.getElementById("formTransaksi");

    let orders = {};

    // === Fungsi Toast ===
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast show ' + type;
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2500);
    }

    // === Event + dan - ===
    document.querySelectorAll(".topping-card").forEach(card => {
        const id = card.dataset.id;
        const name = card.dataset.name;
        const price = parseInt(card.dataset.price);
        const stock = parseInt(card.dataset.stock);
        const qtyDisplay = card.querySelector(".qty");
        const plus = card.querySelector(".plus");
        const minus = card.querySelector(".minus");

        plus.addEventListener("click", () => updateOrder(id, name, price, 1, qtyDisplay, stock));
        minus.addEventListener("click", () => updateOrder(id, name, price, -1, qtyDisplay, stock));
    });

    // === Update order ===
    function updateOrder(id, name, price, delta, qtyDisplay, stock) {
        if (!orders[id]) orders[id] = { id, name, qty: 0, price, stock };
        orders[id].qty += delta;
        if (orders[id].qty > stock) {
            orders[id].qty = stock;
            showToast(`❌ Stok ${name} hanya ${stock}!`, 'error');
        }
        if (orders[id].qty <= 0) delete orders[id];
        renderOrders();
        qtyDisplay.textContent = orders[id]?.qty || 0;
    }

    // === Render daftar pesanan ===
    function renderOrders() {
        orderList.innerHTML = "";
        let total = 0;

        Object.values(orders).forEach(item => {
            const subtotal = item.qty * item.price;
            total += subtotal;

            const el = document.createElement("div");
            el.classList.add("order-item");
            el.innerHTML = `
                <span>${item.name}</span>
                <span>x${item.qty}</span>
                <span>Rp ${subtotal.toLocaleString("id-ID")}</span>
            `;
            orderList.appendChild(el);
        });

        totalHargaEl.textContent = "Rp " + total.toLocaleString("id-ID");
        popupTotal.textContent = totalHargaEl.textContent;
        btnBayar.disabled = total === 0;
        itemsJson.value = JSON.stringify(Object.values(orders));
    }

    // === Popup ===
    btnBayar.addEventListener("click", () => popup.classList.add("show"));
    document.getElementById("closePopup").addEventListener("click", () => popup.classList.remove("show"));

    uangBayar.addEventListener("input", () => {
        const total = parseInt(totalHargaEl.textContent.replace(/\D/g, "")) || 0;
        const bayar = parseInt(uangBayar.value) || 0;
        const kembali = Math.max(bayar - total, 0);
        popupKembalian.textContent = "Rp " + kembali.toLocaleString("id-ID");
    });

    // === Saat klik Selesai ===
    // === Saat klik Selesai ===
// HAPUS TOAST OTOMATIS DI SUBMIT
// Biar Laravel yg ngasih notifikasi dari session

// Tampilkan toast jika session ada
@if (session('success'))
    showToast("{{ session('success') }}", "success");
@endif
@if (session('error'))
    showToast("{{ session('error') }}", "error");
@endif

});
</script>
@endsection
