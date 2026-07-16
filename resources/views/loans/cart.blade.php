@extends('layouts.app')
@section('title', 'Buat Peminjaman')
@section('content')

<div class="row g-3">
    <!-- Kolom Kiri: Pilih Peminjam & Cari Barang -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">1. Pilih Peminjam (Guru/Siswa)</div>
            <div class="card-body">
                @if($borrower)
                    <div class="d-flex justify-content-between align-items-center border rounded p-2 bg-light">
                        <div>
                            <b>{{ $borrower->name }}</b> <span class="badge bg-secondary">{{ ucfirst($borrower->type) }}</span><br>
                            <small class="text-muted">{{ $borrower->identity_number }} - {{ $borrower->unit }}</small>
                        </div>
                        <div class="text-end">
                            <div class="text-success mb-1"><i class="bi bi-check-circle-fill"></i> Terpilih</div>
                            <form action="{{ route('loans.cart.remove_borrower') }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-arrow-repeat"></i> Ganti Peminjam</button>
                            </form>
                        </div>
                    </div>
                @else
                    <input type="text" id="borrowerSearch" class="form-control mb-2" placeholder="Ketik nama atau NIP/NIS...">
                    <div id="borrowerResults" class="list-group"></div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">2. Scan / Cari & Tambah Barang</div>
            <div class="card-body">
                <label class="form-label small text-muted mb-1">Scan Kode Barang / QR Code (tersambung langsung dengan barcode scanner)</label>
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white"><i class="bi bi-upc-scan"></i></span>
                    <input type="text" id="scanCodeInput" class="form-control" placeholder="Scan atau ketik kode barang, lalu Enter..." autocomplete="off">
                </div>
                <label class="form-label small text-muted mb-1">Atau cari manual</label>
                <input type="text" id="assetSearch" class="form-control mb-2" placeholder="Ketik nama barang / kode barang...">
                <div id="assetResults" class="list-group"></div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Keranjang -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span>Keranjang Peminjaman</span>
                @if(count($assets))
                <form action="{{ route('loans.cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan keranjang?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">Kosongkan</button>
                </form>
                @endif
            </div>
            <ul class="list-group list-group-flush">
                @forelse($assets as $asset)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div>{{ $asset->nama_barang }}</div>
                        <small class="text-muted">{{ $asset->kode_barang }} - {{ $asset->kode_umum }}/{{ $asset->kode_aset }}</small>
                    </div>
                    <form action="{{ route('loans.cart.remove') }}" method="POST">
                        @csrf
                        <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                    </form>
                </li>
                @empty
                <li class="list-group-item text-muted">Belum ada barang dipilih.</li>
                @endforelse
            </ul>
        </div>

        @if($borrower && count($assets))
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white fw-semibold">3. Selesaikan Peminjaman</div>
            <div class="card-body">
                <form method="POST" action="{{ route('loans.checkout') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Rencana Kembali</label>
                        <input type="date" name="tanggal_kembali_rencana" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <button class="btn btn-success w-100"><i class="bi bi-check2-circle"></i> Checkout Peminjaman</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal QR Code Barang -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h6 class="modal-title" id="qrModalLabel">QR Code Barang</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="qrModalCode" class="d-flex justify-content-center mb-2"></div>
                <div class="fw-semibold" id="qrModalKode"></div>
                <small class="text-muted">Tempel/cetak label ini pada barang untuk memudahkan scan.</small>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

// ==== Pencarian Peminjam ====
const borrowerSearchEl = document.getElementById('borrowerSearch');
if (borrowerSearchEl) {
    let borrowerTimeout;
    borrowerSearchEl.addEventListener('input', function () {
        clearTimeout(borrowerTimeout);
        const q = this.value;
        borrowerTimeout = setTimeout(() => {
            fetch(`{{ route('borrowers.search') }}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('borrowerResults');
                    box.innerHTML = '';
                    data.forEach(b => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item d-flex justify-content-between align-items-center';
                        item.innerHTML = `<div><b>${b.name}</b> <span class="badge bg-secondary">${b.type}</span><br><small class="text-muted">${b.identity_number ?? ''} - ${b.unit ?? ''}</small></div>
                            <button class="btn btn-sm btn-primary">Pilih</button>`;
                        item.querySelector('button').addEventListener('click', () => chooseBorrower(b.id));
                        box.appendChild(item);
                    });
                });
        }, 300);
    });
}

function chooseBorrower(id) {
    fetch(`{{ route('loans.cart.choose_borrower') }}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ borrower_id: id })
    }).then(() => location.reload());
}

// ==== Scan Kode Barang (peminjaman cepat) ====
const scanCodeInput = document.getElementById('scanCodeInput');
if (scanCodeInput) {
    scanCodeInput.focus();
    scanCodeInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const kode = this.value.trim();
            if (!kode) return;
            fetch(`{{ route('loans.cart.scan') }}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ kode: kode })
            }).then(() => location.reload());
        }
    });
}

// ==== Pencarian Barang ====
const assetSearchEl = document.getElementById('assetSearch');
if (assetSearchEl) {
    let assetTimeout;
    assetSearchEl.addEventListener('input', function () {
        clearTimeout(assetTimeout);
        const q = this.value;
        assetTimeout = setTimeout(() => {
            fetch(`{{ route('loans.search_assets') }}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('assetResults');
                    box.innerHTML = '';
                    data.forEach(a => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item d-flex justify-content-between align-items-center';
                        item.innerHTML = `<div>${a.nama_barang}<br><small class="text-muted">${a.kode_barang} - ${a.kode_umum}/${a.kode_aset}</small></div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-secondary btn-qr" title="Lihat QR"><i class="bi bi-qr-code"></i></button>
                                <button class="btn btn-sm btn-success btn-add">Tambah</button>
                            </div>`;
                        item.querySelector('.btn-add').addEventListener('click', () => addAsset(a.id));
                        item.querySelector('.btn-qr').addEventListener('click', () => showQr(a.kode_barang, a.nama_barang));
                        box.appendChild(item);
                    });
                });
        }, 300);
    });
}

function addAsset(id) {
    fetch(`{{ route('loans.cart.add') }}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ asset_id: id })
    }).then(() => location.reload());
}

// ==== QR Code ====
let qrModalInstance;
function showQr(kode, nama) {
    document.getElementById('qrModalKode').textContent = kode + ' - ' + nama;
    const qrContainer = document.getElementById('qrModalCode');
    qrContainer.innerHTML = '';
    new QRCode(qrContainer, { text: kode, width: 160, height: 160 });
    qrModalInstance = qrModalInstance || new bootstrap.Modal(document.getElementById('qrModal'));
    qrModalInstance.show();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
@endsection
