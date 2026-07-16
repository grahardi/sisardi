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

                <hr>
                <button type="button" id="btnToggleCamera" class="btn btn-outline-primary">
                    <i class="bi bi-camera-fill"></i> Scan Pakai Kamera HP
                </button>
                <div id="cameraWrap" class="mt-2 d-none">
                    <div id="cameraReader" style="max-width:340px;"></div>
                    <div id="cameraHint" class="small text-muted mt-1">Arahkan kamera ke QR code barang. Butuh izin akses kamera dan koneksi HTTPS.</div>
                </div>
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

// ==== Scan pakai kamera HP (html5-qrcode) ====
const btnToggleCamera = document.getElementById('btnToggleCamera');
const cameraWrap = document.getElementById('cameraWrap');
let html5QrCode = null;
let cameraRunning = false;
let lastScannedCode = null;
let lastScannedAt = 0;

btnToggleCamera.addEventListener('click', function () {
    if (cameraRunning) {
        stopCamera();
    } else {
        startCamera();
    }
});

function startCamera() {
    cameraWrap.classList.remove('d-none');
    btnToggleCamera.innerHTML = '<i class="bi bi-camera-video-off"></i> Matikan Kamera';

    html5QrCode = new Html5Qrcode('cameraReader');
    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 220, height: 220 } },
        (decodedText) => {
            const now = Date.now();
            // Cegah barang yang sama kescan berulang-ulang dalam waktu berdekatan
            if (decodedText === lastScannedCode && (now - lastScannedAt) < 3000) return;
            lastScannedCode = decodedText;
            lastScannedAt = now;
            processScan(decodedText.trim());
        },
        () => {} // diamkan error per-frame (biasanya cuma "QR tidak terdeteksi di frame ini")
    ).then(() => { cameraRunning = true; })
    .catch((err) => {
        cameraWrap.classList.add('d-none');
        btnToggleCamera.innerHTML = '<i class="bi bi-camera-fill"></i> Scan Pakai Kamera HP';
        scanStatus.innerHTML = `<div class="alert alert-danger py-2 mb-0">Tidak bisa mengakses kamera: ${err}. Pastikan mengizinkan akses kamera dan situs diakses lewat HTTPS.</div>`;
    });
}

function stopCamera() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
            cameraRunning = false;
            cameraWrap.classList.add('d-none');
            btnToggleCamera.innerHTML = '<i class="bi bi-camera-fill"></i> Scan Pakai Kamera HP';
        });
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endsection
