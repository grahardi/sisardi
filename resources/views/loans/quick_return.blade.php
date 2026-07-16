@extends('layouts.app')
@section('title', 'Pengembalian Cepat (Scan)')
@section('content')

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-upc-scan me-1"></i> Scan / Ketik Kode Barang
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">
                    Arahkan scanner barcode/QR ke label barang, atau ketik manual Kode Barang lalu tekan <b>Enter</b>.
                    Kalau barang sedang berstatus dipinjam, langsung otomatis dikembalikan.
                </p>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white"><i class="bi bi-upc-scan"></i></span>
                    <input type="text" id="scanInput" class="form-control" placeholder="Scan atau ketik kode barang di sini..." autocomplete="off">
                </div>
                <div id="scanStatus" class="mt-2"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span><i class="bi bi-clock-history me-1"></i> Riwayat Scan Sesi Ini</span>
                <span class="badge bg-success" id="counterBadge">0 barang</span>
            </div>
            <ul class="list-group list-group-flush" id="scanLog" style="max-height:420px; overflow-y:auto;">
                <li class="list-group-item text-muted" id="emptyLog">Belum ada barang yang discan.</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
const scanInput = document.getElementById('scanInput');
const scanStatus = document.getElementById('scanStatus');
const scanLog = document.getElementById('scanLog');
const emptyLog = document.getElementById('emptyLog');
const counterBadge = document.getElementById('counterBadge');
let count = 0;

scanInput.focus();

scanInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const kode = this.value.trim();
        if (!kode) return;
        processScan(kode);
        this.value = '';
    }
});

function processScan(kode) {
    scanStatus.innerHTML = `<div class="alert alert-secondary py-2 mb-0"><span class="spinner-border spinner-border-sm me-1"></span> Memproses "${kode}"...</div>`;

    fetch(`{{ route('loans.quick_return.scan') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ kode: kode }),
    })
    .then(async (r) => ({ status: r.status, body: await r.json() }))
    .then(({ status, body }) => {
        if (body.success) {
            scanStatus.innerHTML = `<div class="alert alert-success py-2 mb-0"><i class="bi bi-check-circle-fill me-1"></i>${body.message}</div>`;
            addLogEntry(body.data, true);
        } else {
            scanStatus.innerHTML = `<div class="alert alert-danger py-2 mb-0"><i class="bi bi-x-circle-fill me-1"></i>${body.message}</div>`;
            addLogEntry({ kode_barang: kode, nama_barang: body.message }, false);
        }
    })
    .catch(() => {
        scanStatus.innerHTML = `<div class="alert alert-danger py-2 mb-0">Terjadi kesalahan koneksi.</div>`;
    })
    .finally(() => scanInput.focus());
}

function addLogEntry(data, success) {
    if (emptyLog) emptyLog.remove();

    if (success) {
        count++;
        counterBadge.textContent = count + ' barang';
    }

    const li = document.createElement('li');
    li.className = 'list-group-item';
    if (success) {
        li.innerHTML = `<div class="d-flex justify-content-between">
                <span><i class="bi bi-check-circle-fill text-success me-1"></i><b>${data.nama_barang}</b> (${data.kode_barang})</span>
                <small class="text-muted">${data.waktu}</small>
            </div>
            <small class="text-muted">Dikembalikan oleh: ${data.peminjam} | Transaksi: ${data.transaction_code}</small>`;
    } else {
        li.innerHTML = `<div class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>${data.nama_barang}</div>`;
    }
    scanLog.prepend(li);
}
</script>
@endsection
