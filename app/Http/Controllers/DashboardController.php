<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Stok;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Set lokal bahasa Indonesia
        Carbon::setLocale('id');

        // Total produk dari tabel menu
        $totalMenu = Menu::count();

        // Total stok item yang tersedia
        $totalStok = Stok::where('jumlah', '>', 0)
            ->distinct('id_barang')
            ->count('id_barang');

        // Total pendapatan bulan ini
        $totalOmzet = DB::table('pemesanan')
            ->whereMonth('tanggal_pemesanan', Carbon::now()->month)
            ->whereYear('tanggal_pemesanan', Carbon::now()->year)
            ->sum('harga_total');

        // Jumlah user aktif (hanya admin yang bisa lihat)
        $totalUserAktif = null;
        if (Auth::check() && Auth::user()->role === 'admin') {
            $totalUserAktif = User::where('status', 'aktif')->count();
        }

        // Cek stok menipis (<= 1.2)
        $stokMenipis = Stok::where('jumlah', '<=', 1.2)
            ->pluck('nama_barang')
            ->toArray();

        // Nama user yang login
        $namaLengkap = Auth::check() ? (Auth::user()->nama_lengkap ?? Auth::user()->username) : 'Pengguna';

        // 🟢 Tanggal dalam format Bahasa Indonesia
        $tanggalHariIni = Carbon::now()->translatedFormat('l, d F Y');

        // 🧾 Jumlah transaksi (penjualan) hari ini
        $penjualanHariIni = DB::table('pemesanan')
            ->whereDate('tanggal_pemesanan', Carbon::today())
            ->count();

        // 💰 Total pendapatan (uang) hari ini
        $pendapatanHariIni = DB::table('pemesanan')
            ->whereDate('tanggal_pemesanan', Carbon::today())
            ->sum('harga_total');

        // 📊 Data penjualan 7 hari terakhir
        $tanggalMulai = Carbon::now()->subDays(6); // 6 hari lalu sampai hari ini
        $penjualanMingguan = DB::table('pemesanan')
            ->select(
                DB::raw('DATE(tanggal_pemesanan) as tanggal'),
                DB::raw('COUNT(*) as total_transaksi')
            )
            ->whereBetween('tanggal_pemesanan', [$tanggalMulai, Carbon::now()])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Buat array tanggal & total transaksi untuk grafik
        $labelTanggal = [];
        $dataTransaksi = [];

        for ($i = 0; $i < 7; $i++) {
            $tgl = Carbon::now()->subDays(6 - $i);
            $labelTanggal[] = $tgl->format('Y-m-d'); // format standar ISO → 2025-10-27
            $dataTransaksi[] = $penjualanMingguan
                ->firstWhere('tanggal', $tgl->toDateString())
                ->total_transaksi ?? 0;
        }

        return view('dashboard', compact(
            'totalMenu',
            'totalStok',
            'totalOmzet',
            'totalUserAktif',
            'stokMenipis',
            'namaLengkap',
            'tanggalHariIni',
            'penjualanHariIni',
            'pendapatanHariIni',
            'labelTanggal',
            'dataTransaksi'
        ));
    }
}
