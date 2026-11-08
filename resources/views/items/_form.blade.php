@php $item = $item ?? null; @endphp

<div class="form-group">
    <label for="title"><i class="fas fa-heading"></i> Judul Barang *</label>
    <input type="text" id="title" name="title" required maxlength="200"
           value="{{ old('title', $item?->title) }}" placeholder="Contoh: Hoodie Oversize Hitam, iPad 9th Gen, Buku Kalkulus…">
</div>

<div class="form-group">
    <label for="category"><i class="fas fa-shapes"></i> Kategori *</label>
    <select id="category" name="category" required>
        <option value="">Pilih kategori…</option>
        @foreach (\App\Models\Item::CATEGORIES as $cat)
            <option value="{{ $cat }}" @selected(old('category', $item?->category) === $cat)>{{ $cat }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label><i class="fas fa-wand-magic-sparkles"></i> Kondisi *</label>
    <div class="segmented" role="radiogroup" aria-label="Kondisi barang">
        @foreach (\App\Models\Item::CONDITIONS as $cond)
            <label>
                <input type="radio" name="condition_item" value="{{ $cond }}" required @checked(old('condition_item', $item?->condition_item ?? 'Baik') === $cond)>
                <span>{{ $cond }}</span>
            </label>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label for="description"><i class="fas fa-align-left"></i> Deskripsi *</label>
    <textarea id="description" name="description" rows="6" required maxlength="5000" data-counter="descCounter"
              placeholder="Ceritain kondisinya, kelebihan &amp; kekurangan, plus barang apa yang lagi kamu cari.">{{ old('description', $item?->description) }}</textarea>
    <small id="descCounter" class="char-counter"></small>
</div>

@if ($item)
    <div class="form-group">
        <label><i class="fas fa-image"></i> Foto Saat Ini</label>
        <div class="current-photo"><img src="{{ $item->photo_url }}" alt="Foto saat ini"></div>
    </div>
@endif

<div class="form-group">
    <label for="photo"><i class="fas fa-camera"></i> {{ $item ? 'Ganti Foto (opsional)' : 'Foto Barang *' }}</label>
    <div class="dropzone">
        <i class="fas fa-cloud-arrow-up"></i>
        <strong data-dropzone-label data-default="Klik atau seret foto ke sini">Klik atau seret foto ke sini</strong>
        <span>JPG, PNG, GIF, WEBP · maks. 5MB{{ $item ? ' · kosongkan jika tidak diganti' : '' }}</span>
        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" @required(!$item)>
    </div>
    <div id="imagePreview" class="image-preview"></div>
</div>
