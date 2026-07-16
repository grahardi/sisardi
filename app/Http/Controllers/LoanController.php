<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Borrower;
use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    const CART_SESSION_KEY = 'loan_cart';

    public function index(Request $request)
    {
        $query = Loan::with(['borrower', 'petugas', 'items.asset']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('q')) {
            $query->where('transaction_code', 'like', "%{$search}%")
                ->orWhereHas('borrower', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $loans = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('loans.index', compact('loans'));
    }

    public function show(Loan $loan)
    {
        $loan->load(['borrower', 'petugas', 'items.asset']);
        return view('loans.show', compact('loan'));
    }

    // Halaman keranjang peminjaman (bisa mulai dari cari aset ATAU cari peminjam)
    public function cart()
    {
        $cart = session(self::CART_SESSION_KEY, ['borrower_id' => null, 'asset_ids' => []]);

        $borrower = $cart['borrower_id'] ? Borrower::find($cart['borrower_id']) : null;
        $assets = Asset::whereIn('id', $cart['asset_ids'])->get();

        return view('loans.cart', compact('cart', 'borrower', 'assets'));
    }

    // Cari aset yang tersedia (AJAX), dipakai dari alur "cari barang dulu"
    public function searchAssets(Request $request)
    {
        $search = $request->get('q', '');

        $assets = Asset::where('status', 'baik')
            ->whereDoesntHave('loanItems', fn($q) => $q->where('is_returned', false))
            ->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('kode_umum', 'like', "%{$search}%")
                  ->orWhere('kode_aset', 'like', "%{$search}%");
            })
            ->limit(15)
            ->get(['id', 'kode_barang', 'kode_umum', 'kode_aset', 'nama_barang']);

        return response()->json($assets);
    }

    public function addToCart(Request $request)
    {
        $request->validate(['asset_id' => 'required|exists:assets,id']);

        $asset = Asset::findOrFail($request->asset_id);
        if (! $asset->isAvailable()) {
            return back()->withErrors(['error' => 'Aset ini tidak tersedia untuk dipinjam.']);
        }

        $cart = session(self::CART_SESSION_KEY, ['borrower_id' => null, 'asset_ids' => []]);
        if (! in_array($asset->id, $cart['asset_ids'])) {
            $cart['asset_ids'][] = $asset->id;
        }
        session([self::CART_SESSION_KEY => $cart]);

        return back()->with('success', 'Barang ditambahkan ke keranjang peminjaman.');
    }

    // Tambah ke keranjang lewat scan kode barang / QR code (cocok untuk barcode scanner fisik)
    public function addToCartByCode(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $kode = trim($request->kode);
        $asset = Asset::where('kode_barang', $kode)->first();

        if (! $asset) {
            return back()->withErrors(['error' => "Kode Barang '{$kode}' tidak ditemukan."]);
        }
        if (! $asset->isAvailable()) {
            return back()->withErrors(['error' => "Barang '{$asset->nama_barang}' ({$kode}) tidak tersedia untuk dipinjam."]);
        }

        $cart = session(self::CART_SESSION_KEY, ['borrower_id' => null, 'asset_ids' => []]);
        if (! in_array($asset->id, $cart['asset_ids'])) {
            $cart['asset_ids'][] = $asset->id;
        }
        session([self::CART_SESSION_KEY => $cart]);

        return back()->with('success', "'{$asset->nama_barang}' ({$kode}) ditambahkan ke keranjang.");
    }

    public function removeFromCart(Request $request)
    {
        $request->validate(['asset_id' => 'required|integer']);

        $cart = session(self::CART_SESSION_KEY, ['borrower_id' => null, 'asset_ids' => []]);
        $cart['asset_ids'] = array_values(array_diff($cart['asset_ids'], [$request->asset_id]));
        session([self::CART_SESSION_KEY => $cart]);

        return back()->with('success', 'Barang dihapus dari keranjang.');
    }

    public function chooseBorrower(Request $request)
    {
        $request->validate(['borrower_id' => 'required|exists:borrowers,id']);

        $cart = session(self::CART_SESSION_KEY, ['borrower_id' => null, 'asset_ids' => []]);
        $cart['borrower_id'] = (int) $request->borrower_id;
        session([self::CART_SESSION_KEY => $cart]);

        return back()->with('success', 'Peminjam dipilih.');
    }

    // Batal/ganti peminjam yang sudah dipilih, tanpa mengosongkan barang yang sudah ditambahkan
    public function removeBorrower()
    {
        $cart = session(self::CART_SESSION_KEY, ['borrower_id' => null, 'asset_ids' => []]);
        $cart['borrower_id'] = null;
        session([self::CART_SESSION_KEY => $cart]);

        return back()->with('success', 'Pilihan peminjam dibatalkan, silakan pilih ulang.');
    }

    public function clearCart()
    {
        session()->forget(self::CART_SESSION_KEY);
        return redirect()->route('loans.cart')->with('success', 'Keranjang dikosongkan.');
    }

    // Finalisasi peminjaman (checkout)
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'keterangan' => 'nullable|string',
        ]);

        $cart = session(self::CART_SESSION_KEY, ['borrower_id' => null, 'asset_ids' => []]);

        if (! $cart['borrower_id']) {
            return back()->withErrors(['error' => 'Pilih peminjam (guru/siswa) terlebih dahulu.']);
        }
        if (empty($cart['asset_ids'])) {
            return back()->withErrors(['error' => 'Keranjang masih kosong, tambahkan barang terlebih dahulu.']);
        }

        $loan = DB::transaction(function () use ($cart, $data) {
            $loan = Loan::create([
                'borrower_id' => $cart['borrower_id'],
                'user_id' => Auth::id(),
                'tanggal_pinjam' => $data['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $data['tanggal_kembali_rencana'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'status' => 'dipinjam',
            ]);

            foreach ($cart['asset_ids'] as $assetId) {
                $asset = Asset::find($assetId);
                if ($asset && $asset->isAvailable()) {
                    LoanItem::create([
                        'loan_id' => $loan->id,
                        'asset_id' => $assetId,
                        'kondisi_pinjam' => 'baik',
                    ]);
                }
            }

            return $loan;
        });

        session()->forget(self::CART_SESSION_KEY);

        return redirect()->route('loans.show', $loan)->with('success', 'Peminjaman berhasil dicatat: ' . $loan->transaction_code);
    }

    // Pengembalian seluruh item dalam satu transaksi peminjaman
    public function returnAll(Loan $loan)
    {
        foreach ($loan->items()->where('is_returned', false)->get() as $item) {
            $item->update([
                'is_returned' => true,
                'returned_at' => now()->toDateString(),
                'kondisi_kembali' => $item->kondisi_kembali ?: 'baik',
            ]);
        }
        $loan->refreshStatus();

        return back()->with('success', 'Semua barang telah dikembalikan.');
    }

    // Pengembalian per-item
    public function returnItem(Request $request, LoanItem $loanItem)
    {
        $data = $request->validate([
            'kondisi_kembali' => 'nullable|string|max:100',
        ]);

        $loanItem->update([
            'is_returned' => true,
            'returned_at' => now()->toDateString(),
            'kondisi_kembali' => $data['kondisi_kembali'] ?? 'baik',
        ]);

        $loanItem->loan->refreshStatus();

        return back()->with('success', 'Barang berhasil dikembalikan.');
    }

    // ================= PENGEMBALIAN CEPAT (SCAN) =================

    public function quickReturnForm()
    {
        return view('loans.quick_return');
    }

    // Endpoint AJAX: scan/ketik kode barang -> langsung dikembalikan kalau sedang dipinjam
    public function quickReturnScan(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $kode = trim($request->kode);
        $asset = Asset::where('kode_barang', $kode)->first();

        if (! $asset) {
            return response()->json(['success' => false, 'message' => "Kode Barang '{$kode}' tidak ditemukan."], 404);
        }

        $loanItem = $asset->loanItems()->where('is_returned', false)->latest()->first();

        if (! $loanItem) {
            return response()->json([
                'success' => false,
                'message' => "'{$asset->nama_barang}' ({$kode}) sedang tidak dalam status dipinjam.",
            ], 422);
        }

        $loanItem->update([
            'is_returned' => true,
            'returned_at' => now()->toDateString(),
            'kondisi_kembali' => $loanItem->kondisi_kembali ?: 'baik',
        ]);
        $loanItem->loan->refreshStatus();

        return response()->json([
            'success' => true,
            'message' => "'{$asset->nama_barang}' berhasil dikembalikan.",
            'data' => [
                'kode_barang' => $asset->kode_barang,
                'nama_barang' => $asset->nama_barang,
                'peminjam' => $loanItem->loan->borrower->name ?? '-',
                'transaction_code' => $loanItem->loan->transaction_code,
                'waktu' => now()->format('d/m/Y H:i'),
            ],
        ]);
    }
}
