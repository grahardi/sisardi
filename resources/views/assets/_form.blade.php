@php $a = $asset ?? null; @endphp
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Kode Barang <small class="text-muted">(auto, bisa diedit)</small></label>
        <input type="text" name="kode_barang" class="form-control" value="{{ old('kode_barang', $a->kode_barang ?? ($nextKode ?? '')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Kode Umum <small class="text-muted">(mis. LPX)</small></label>
        <input type="text" name="kode_umum" class="form-control" value="{{ old('kode_umum', $a->kode_umum ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Kode Aset <small class="text-muted">(unik per Kode Umum)</small></label>
        <input type="text" name="kode_aset" class="form-control" value="{{ old('kode_aset', $a->kode_aset ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $a->nama_barang ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select">
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ old('category_id', $a->category_id ?? '') == $c->id ? 'selected' : '' }}>
                    {{ $c->parent ? $c->parent->name.' > ' : '' }}{{ $c->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tempat</label>
        <select name="location_id" class="form-select">
            <option value="">-- Pilih Tempat --</option>
            @foreach($locations as $l)
                <option value="{{ $l->id }}" {{ old('location_id', $a->location_id ?? '') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Tahun Pembelian</label>
        <input type="number" name="tahun_pembelian" class="form-control" value="{{ old('tahun_pembelian', $a->tahun_pembelian ?? '') }}" min="1990" max="{{ date('Y')+1 }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Dana Pembelian</label>
        <select name="funding_source_id" class="form-select">
            <option value="">-- Pilih Sumber Dana --</option>
            @foreach($fundingSources as $f)
                <option value="{{ $f->id }}" {{ old('funding_source_id', $a->funding_source_id ?? '') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
            @endforeach
        </select>
    </div>
    @if($a)
    <div class="col-md-5">
        <label class="form-label">Status</label>
        <input type="text" class="form-control" value="{{ str_replace('_',' ',$a->status) }}" disabled>
        <small class="text-muted">Status diperbarui otomatis lewat menu History Perbaikan.</small>
    </div>
    @endif

    <div class="col-12">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $a->keterangan ?? '') }}</textarea>
    </div>
</div>
