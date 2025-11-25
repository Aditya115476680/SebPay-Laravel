@extends('layout.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="struk-card">

        {{-- JUDUL --}}
        <h4 class="struk-title">Transaksi Struk</h4>
        <p class="struk-date">{{ \Carbon\Carbon::parse($trx->tr_date)->format('d/m/Y H:i') }}</p>

        {{-- LIST ITEM --}}
        <div class="struk-items">
            @foreach($details as $d)
            <div class="item-row">
                <span class="item-name">{{ $d->name }}</span>
                <span class="item-qty">x{{ $d->qty }}</span>
                <span class="item-price">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <hr>

        {{-- TOTAL BAYAR KEMBALIAN --}}
        <div class="struk-summary">

            <div class="row-line">
                <span class="label">TOTAL</span>
                <span class="value">Rp {{ number_format($trx->tr_total_amount, 0, ',', '.') }}</span>
            </div>

            <div class="row-line">
                <span class="label">UANG BAYAR</span>
                <span class="value">Rp {{ number_format($trx->tr_payment, 0, ',', '.') }}</span>
            </div>

            <div class="row-line">
                <span class="label">KEMBALIAN</span>
                <span class="value">Rp {{ number_format($trx->tr_change, 0, ',', '.') }}</span>
            </div>

        </div>

        <hr>

        {{-- FOOTER ALAMAT --}}
        <div class="struk-footer">
            <p class="alamat">Jl. Rancamanyar / Kp. Cupu / RT 05 / RW 08 No.56</p>
            <p>Waroenk Seblak</p>
        </div>

        <button class="btn-done" onclick="window.location='{{ route('transactions.history') }}'">
            Kembali
        </button>
    </div>
</div>

<style>
/* === TITLE & DATE === */
.struk-title {
  font-weight: 700;
  color: #7b0000;
  margin: 0 0 4px 0;
  font-size: 20px;
  text-align: center;
}

.struk-date {
  color: #444;
  font-size: 14px;
  margin-bottom: 20px;
  text-align: center;
}

/* === ITEM LIST === */
.struk-items {
  margin-bottom: 20px;
  width: 100%;
}

.item-row {
  display: grid;
  grid-template-columns: 1fr auto auto;
  font-size: 14px;
  margin-bottom: 8px;
}

.item-name {
  text-align: left;
}

.item-qty {
  text-align: center;
  width: 40px;
}

.item-price {
  text-align: right;
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
  margin-top: 20px;
  text-align: center;
}

.struk-footer p {
  margin: 3px 0;
}

.struk-footer .alamat {
  font-weight: 600;
}

/* === BUTTON === */
.btn-done {
  width: 100%;
  background: #7b0000;
  color: #fff;
  padding: 12px 0;
  border-radius: 10px;
  font-weight: 600;
  border: none;
  margin-top: 15px;
  cursor: pointer;
}
</style>

@endsection
