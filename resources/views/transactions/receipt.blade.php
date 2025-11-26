@extends('layout.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="struk-card">

        {{-- JUDUL --}}
        <h4 class="struk-title">Transaksi Struk</h4>
<p class="struk-date">{{ \Carbon\Carbon::parse($trx->tr_date)->format('d/m/Y H:i') }}</p>

<div class="separator"></div>

<div class="struk-items">
    @foreach($details as $d)
        <div class="item-row">
            <span class="item-name">{{ $d->name }}</span>
            <span class="item-qty">x{{ $d->qty }}</span>
            <span class="item-price">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
        </div>
    @endforeach
</div>

<div class="separator"></div>

<div class="struk-summary">
    <div class="row-line">
        <span>TOTAL</span>
        <span>Rp {{ number_format($trx->tr_total_amount, 0, ',', '.') }}</span>
    </div>
    <div class="row-line">
        <span>UANG BAYAR</span>
        <span>Rp {{ number_format($trx->tr_payment, 0, ',', '.') }}</span>
    </div>
    <div class="row-line">
        <span>KEMBALIAN</span>
        <span>Rp {{ number_format($trx->tr_change, 0, ',', '.') }}</span>
    </div>
</div>

<div class="separator"></div>

<div class="struk-footer">
    <p>Jl. Rancamanyar / Kp. Cupu / RT 05 / RW 08 No.56</p>
    <p>Waroenk Seblak</p>
</div>


        <button class="btn-done" onclick="window.location='{{ route('transactions.history') }}'">
            Kembali
        </button>
    </div>
</div>

<style>
/* === TITLE & DATE === */
.struk-card {
  width: 320px;     /* biar kayak lebar struk thermal */
  background: #fff;
  padding: 22px;
  border-radius: 4px;
  box-shadow: 0 0 2px rgba(0,0,0,.35);
  font-family: "Courier New", monospace; /* FONT STRUK */
  color: #000;
}

.struk-title {
  text-align: center;
  font-weight: bold;
  font-size: 17px;
  margin-bottom: 2px;
}

.struk-date {
  text-align: center;
  font-size: 12px;
  margin-bottom: 12px;
}

.separator {
  border-top: 1px dashed #000;
  margin: 8px 0;
}

/* === ITEM LIST === */
.struk-items {
  margin-bottom: 20px;
  width: 100%;
}

.item-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  margin-bottom: 3px;
}

.item-name {
  max-width: 140px;
  display: inline-block;
}

.item-qty {
  width: 30px;
  text-align: center;
}

.item-price {
  text-align: right;
  width: 70px;
}


.row-line {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  font-weight: bold;
  margin-bottom: 4px;
}

/* === SUMMARY === */
.struk-summary {
  margin: 20px 0 25px 0;
}

.row-line {
  display: flex;
  justify-content: space-between;
  font-size: 15px;
  margin-bottom: 6px;
}

.label {
  font-weight: 700;
}

.value {
  font-weight: 500;
}

/* === FOOTER === */
.struk-footer {
  margin-top: 14px;
  text-align: center;
  font-size: 12px;
}

.struk-footer p {
  margin: 1px 0;
}

.struk-footer .alamat {
  font-weight: 600;
}

/* === BUTTON === */
.btn-done {
  margin-top: 18px;
  width: 100%;
  padding: 10px 0;
  background: #7b0000;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
</style>

@endsection
