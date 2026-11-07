<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    // === TAMPILKAN SEMUA RESEP ===
    public function index()
    {
        $resep = DB::table('resep_produk')
            ->join('products', 'resep_produk.id_produk', '=', 'products.id_produk')
            ->join('stok', 'resep_produk.id_barang', '=', 'stok.id_barang')
            ->select('resep_produk.*', 'products.nama_produk', 'stok.nama_barang')
            ->orderBy('products.nama_produk')
            ->get();

        return view('resep.resep', compact('resep'));
    }

    // === FORM TAMBAH RESEP ===
    public function create()
    {
        $products = DB::table('products')->where('status', 'aktif')->get();
        $stok = DB::table('stok')->get();

        return view('resep.resep_create', compact('products', 'stok'));
    }

    // === HAPUS RESEP ===
    public function destroy($id)
    {
        DB::table('resep_produk')->where('id_resep', $id)->delete();
        return redirect()->route('resep.index')->with('success', 'Resep berhasil dihapus.');
    }

    // === SIMPAN DATA RESEP ===
    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|integer',
            'id_barang' => 'required|array',
            'jumlah_per_porsi' => 'required|array'
        ]);

        $id_produk = $request->id_produk;
        $id_barang_list = $request->id_barang;

        // ✅ CEK DUPLIKAT BAHAN DALAM SATU INPUT FORM
        if (count($id_barang_list) !== count(array_unique($id_barang_list))) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terdapat bahan yang sama di dalam satu resep. Gunakan bahan yang berbeda untuk setiap baris.');
        }

        // ✅ CEK APAKAH PRODUK & BAHAN SUDAH ADA DI DATABASE
        foreach ($id_barang_list as $id_barang) {
            $duplikat = DB::table('resep_produk')
                ->where('id_produk', $id_produk)
                ->where('id_barang', $id_barang)
                ->exists();

            if ($duplikat) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Kombinasi produk dan bahan yang sama sudah ada. Tidak dapat menambahkan resep duplikat.');
            }
        }

        // ✅ SIMPAN RESEP BARU
        foreach ($request->id_barang as $i => $id_barang) {
            $jumlahInput = trim($request->jumlah_per_porsi[$i]);

            // Parsing "3/12" → 0.25
            if (str_contains($jumlahInput, '/')) {
                [$a, $b] = explode('/', $jumlahInput);
                $a = floatval($a);
                $b = floatval($b ?: 1);
                $jumlahPerPorsi = $b != 0 ? $a / $b : 0;
            } else {
                $jumlahPerPorsi = floatval($jumlahInput);
            }

            DB::table('resep_produk')->insert([
                'id_produk' => $id_produk,
                'id_barang' => $id_barang,
                'jumlah_per_porsi' => $jumlahPerPorsi,
            ]);
        }

        return redirect()->route('resep.index')->with('success', 'Resep berhasil ditambahkan!');
    }
}
