<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    // 🔹 Tampilkan daftar pemesanan
    public function index(Request $request)
    {
        $query = $request->input('q');

        $pemesanan = DB::table('pemesanan')
            ->when($query, function($q) use ($query) {
                $q->where('nama_pemesan', 'like', "%{$query}%")
                  ->orWhere('kasir', 'like', "%{$query}%")
                  ->orWhere('metode_pembayaran', 'like', "%{$query}%");
            })
            ->orderBy('tanggal_pemesanan', 'desc')
            ->get();

        return view('pemesanan.pemesanan', compact('pemesanan', 'query'));
    }

    // 🔹 Form tambah pemesanan
    public function create()
    {
        $products = DB::table('products')
            ->where('status', 'aktif')
            ->whereIn('id_produk', function ($query) {
                $query->select('id_produk')->from('resep_produk');
            })
            ->get();

        return view('pemesanan.pemesanan_create', compact('products'));
    }

    // 🔹 AJAX: Cek stok produk sebelum disubmit
    public function checkStok(Request $request)
    {
        $produkId = $request->produk_id;
        $jumlah = (int) $request->jumlah;

        $produk = DB::table('products')->where('id_produk', $produkId)->first();
        if (!$produk) {
            return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan.']);
        }

        $resep = DB::table('resep_produk')->where('id_produk', $produkId)->get();
        if ($resep->isEmpty()) {
            return response()->json(['status' => 'ok']);
        }

        foreach ($resep as $bahan) {
            $stok = DB::table('stok')->where('id_barang', $bahan->id_barang)->first();
            $dibutuhkan = $bahan->jumlah_per_porsi * $jumlah;

            if (!$stok) {
                return response()->json(['status' => 'error', 'message' => "Bahan ID {$bahan->id_barang} tidak ditemukan di stok."]);
            }

            if ($stok->jumlah < $dibutuhkan) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Stok bahan '{$stok->nama_barang}' tidak mencukupi."
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // 🔹 Simpan pemesanan baru + kurangi stok otomatis
    public function store(Request $request)
    {
        $request->validate([
            'nama_pemesan' => 'required|string',
            'produk' => 'required|array',
            'jumlah' => 'required|array',
            'metode_pembayaran' => 'required|string|in:Tunai,Transfer,QRIS'
        ]);

        DB::beginTransaction();

        try {
            // ========== GENERATE ID PEMESANAN UNIK ==========
            $today = now()->format('dmy'); // contoh: 271025
            $prefix = "WT" . $today;

            // ambil id terakhir yang dimulai dengan prefix hari ini
            $last = DB::table('pemesanan')
                ->where('id_pemesanan', 'like', $prefix . '%')
                ->orderBy('id_pemesanan', 'desc')
                ->first();

            if ($last) {
                $lastNumber = (int) substr($last->id_pemesanan, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = "0001";
            }

            $idPemesanan = $prefix . $newNumber;
            // =================================================

            $nama_barang = [];
            $jumlah_pemesanan = [];
            $harga_total = 0;

            foreach ($request->produk as $i => $id_produk) {
                $produk = DB::table('products')->where('id_produk', $id_produk)->first();
                if (!$produk) continue;

                $qty = (int) $request->jumlah[$i];
                $nama_barang[] = $produk->nama_produk;
                $jumlah_pemesanan[] = $qty;
                $harga_total += $produk->harga * $qty;

                $resep = DB::table('resep_produk')->where('id_produk', $id_produk)->get();

                foreach ($resep as $bahan) {
                    $stok = DB::table('stok')->where('id_barang', $bahan->id_barang)->first();
                    $total_dibutuhkan = $bahan->jumlah_per_porsi * $qty;

                    if ($stok->jumlah < $total_dibutuhkan) {
                        DB::rollBack();
                        return back()->withErrors([
                            'error' => "Stok bahan '{$stok->nama_barang}' tidak mencukupi untuk produk {$produk->nama_produk}."
                        ])->withInput();
                    }

                    // Kurangi stok
                    $stok_baru = max(0, round($stok->jumlah - $total_dibutuhkan, 2));
                    DB::table('stok')->where('id_barang', $bahan->id_barang)->update(['jumlah' => $stok_baru]);
                }
            }

            $kasir = Auth::user()->nama_lengkap ?? 'Tidak diketahui';

            DB::table('pemesanan')->insert([
                'id_pemesanan' => $idPemesanan,
                'nama_pemesan' => $request->nama_pemesan,
                'kasir' => $kasir,
                'nama_barang' => json_encode($nama_barang),
                'jumlah_pemesanan' => json_encode($jumlah_pemesanan),
                'metode_pembayaran' => $request->metode_pembayaran,
                'harga_total' => $harga_total,
                'tanggal_pemesanan' => now()
            ]);

            DB::commit();
            return redirect()
                ->route('pemesanan.show', ['id' => $idPemesanan])
                ->with('success', "Pemesanan #$idPemesanan berhasil disimpan dan ditampilkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // 🔹 Tampilkan nota / detail transaksi berdasarkan ID
    public function show($id)
    {
        $pemesanan = DB::table('pemesanan')->where('id_pemesanan', $id)->first();

        if (!$pemesanan) {
            return redirect()->route('pemesanan.index')->withErrors(['error' => 'Transaksi tidak ditemukan.']);
        }

        // Decode JSON
        $pemesanan->nama_barang = json_decode($pemesanan->nama_barang);
        $pemesanan->jumlah_pemesanan = json_decode($pemesanan->jumlah_pemesanan);

        // Siapkan array harga & subtotal
        $harga_satuan = [];
        $subtotal = [];

        foreach ($pemesanan->nama_barang as $i => $nama) {
            // Ambil harga dari tabel products
            $produk = DB::table('products')->where('nama_produk', $nama)->first();
            $harga = $produk ? (float)$produk->harga : 0;
            $jumlah = (int)($pemesanan->jumlah_pemesanan[$i] ?? 0);

            $harga_satuan[] = $harga;
            $subtotal[] = $harga * $jumlah;
        }

        // Tambahkan properti baru ke objek
        $pemesanan->harga_satuan = $harga_satuan;
        $pemesanan->subtotal = $subtotal;

        return view('pemesanan.pemesanan_show', compact('pemesanan'));
    }
}
