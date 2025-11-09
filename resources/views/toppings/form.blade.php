@csrf
@if(isset($topping))
    @method('PUT')
@endif

{{-- === Nama Topping === --}}
<div class="mb-3">
    <label class="form-label fw-semibold text-dark">Nama Topping</label>
    <input type="text" name="tp_name" class="form-control"
           value="{{ old('tp_name', $topping->tp_name ?? '') }}" required>
</div>

{{-- === Harga === --}}
<div class="mb-3">
    <label class="form-label fw-semibold text-dark">Harga (Rp)</label>
    <input type="number" name="tp_price" class="form-control"
           value="{{ old('tp_price', $topping->tp_price ?? '') }}" min="0" required>
</div>

{{-- === Stok === --}}
<div class="mb-3">
    <label class="form-label fw-semibold text-dark">Stok</label>
    @if(isset($topping))
        <input type="number" class="form-control" value="{{ $topping->tp_stock }}" disabled>
        <small class="text-muted">
            Gunakan menu <strong>Topping In</strong> / <strong>Topping Out</strong> untuk ubah stok.
        </small>
    @else
        <input type="number" name="tp_stock" class="form-control"
               value="{{ old('tp_stock', 0) }}" min="0" required>
    @endif
</div>

{{-- === Upload Gambar === --}}
<div class="mb-3">
    <label>Gambar</label>
    <input type="file" name="tp_image" id="tp_image" class="form-control" accept="image/*">

    {{-- Preview Gambar --}}
    <div class="text-center mt-3">
        <img id="preview"
             src="{{ isset($topping) && $topping->tp_image 
                 ? asset('images/' . $topping->tp_image)
                 : asset('images/') }}"
             alt="Preview Gambar"
             style="max-width: 160px; border-radius: 10px; border: 2px solid #ddd; padding: 4px;">
    </div>
</div>

{{-- Script Preview Otomatis --}}
<script>
document.getElementById('tp_image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');

    if (file) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(file);
    } else {
        // Kalau batal pilih file
        preview.src = "{{ asset('images/no-image.png') }}";
    }
});
</script>


<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('preview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
