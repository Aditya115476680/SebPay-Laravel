@csrf
@if(isset($topping))
    @method('PUT')
@endif

<div class="mb-3">
    <label>Nama Topping</label>
    <input type="text" name="tp_name" class="form-control"
           value="{{ old('tp_name', $topping->tp_name ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Harga (Rp)</label>
    <input type="number" name="tp_price" class="form-control"
           value="{{ old('tp_price', $topping->tp_price ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Stok</label>
    @if(isset($topping))
        {{-- Kalau edit, stok tidak bisa diubah langsung --}}
        <input type="number" class="form-control" value="{{ $topping->tp_stock }}" disabled>
        <small class="text-muted">
            Gunakan menu <strong>Topping In</strong> / <strong>Out</strong> untuk ubah stok.
        </small>
    @else
        {{-- Kalau tambah, stok bisa diisi --}}
        <input type="number" name="tp_stock" class="form-control"
               value="{{ old('tp_stock', 0) }}" required>
    @endif
</div>

<div class="mb-3">
    <label>Gambar</label>
    <input type="file" name="tp_image" class="form-control">

    {{-- Kalau edit, tampilkan preview gambar --}}
    @if(isset($topping) && $topping->tp_image)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $topping->tp_image) }}" alt="Gambar Topping"
                 style="max-width: 100px; border-radius: 8px;">
        </div>
    @endif
</div>

        
