<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Courier, monospace;
            font-size: 12px;
        }
        .center { text-align:center; }
        .line { border-top:1px dashed #000; margin:6px 0; }
        .row { display:flex; justify-content:space-between; }
    </style>
</head>
<body>

    <h3 class="center">Waroenk Seblak</h3>
    <p class="center">Jl. Rancamanyar / Kp. Cupu / RT 05 / RW 08 No.56</p>
    <div class="line"></div>

    <p class="center">Tanggal: {{ \Carbon\Carbon::parse($trx->tr_date)->format('d/m/Y H:i') }}</p>

    <div class="line"></div>

    @foreach($details as $d)
    <div class="row">
        <span>{{ $d->tp_name }} x{{ $d->tr_dtl_qty }}</span>
        <span>Rp {{ number_format($d->tr_dtl_subtotal, 0, ',', '.') }}</span>

    </div>
    @endforeach

    <div class="line"></div>

    <div class="row"><strong>Total</strong> <strong>Rp {{ number_format($trx->tr_total_amount,0,',','.') }}</strong></div>
    <div class="row"><span>Bayar</span> <span>Rp {{ number_format($trx->tr_payment,0,',','.') }}</span></div>
    <div class="row"><span>Kembali</span> <span>Rp {{ number_format($trx->tr_change,0,',','.') }}</span></div>

    <div class="line"></div>

    <p class="center">Terima kasih!</p>

</body>
</html>
