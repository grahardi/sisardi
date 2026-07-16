@php $r = $repair ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Barang</label>
        <select name="asset_id" class="form-select" required>
            <option value="">-- Pilih Barang --</option>
            @foreach($assets as $a)
                <option value="{{ $a->id }}" {{ old('asset_id', $r->asset_id ?? '') == $a->id ? 'selected' : '' }}>
                    {{ $a->nama_barang }} ({{ $a->kode_barang }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Tanggal Kerusakan</label>
        <input type="date" name="tanggal_kerusakan" class="form-control" value="{{ old('tanggal_kerusakan', $r->tanggal_kerusakan ?? '') }}" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Tanggal Perbaikan</label>
        <input type="date" name="tanggal_perbaikan" class="form-control" value="{{ old('tanggal_perbaikan', $r->tanggal_perbaikan ?? '') }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai_perbaikan" class="form-control" value="{{ old('tanggal_selesai_perbaikan', $r->tanggal_selesai_perbaikan ?? '') }}">
    </div>
    <div class="col-12">
        <label class="form-label">Keterangan Kerusakan</label>
        <textarea name="keterangan_kerusakan" class="form-control" rows="3">{{ old('keterangan_kerusakan', $r->keterangan_kerusakan ?? '') }}</textarea>
    </div>
</div>
<div class="alert alert-info mt-3 small mb-0">
    <b>Catatan status otomatis:</b> Jika hanya <i>Tanggal Kerusakan</i> diisi &rarr; status <b>Rusak</b>.
    Jika <i>Tanggal Perbaikan</i> diisi &rarr; status <b>Dalam Perbaikan</b>.
    Jika <i>Tanggal Selesai</i> diisi &rarr; status <b>Selesai</b> dan aset kembali <b>Baik</b>.
</div>
