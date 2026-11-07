<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Pdfcrowd\HtmlToPdfClient;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'harian');
        $startDate = null;
        $endDate = null;

        // Tentukan range tanggal
        if ($filter === 'harian') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($filter === 'bulanan') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        } elseif ($filter === 'custom') {
            $startDate = $request->input('start_date') ? date('Y-m-d 00:00:00', strtotime($request->input('start_date'))) : now()->startOfDay();
            $endDate = $request->input('end_date') ? date('Y-m-d 23:59:59', strtotime($request->input('end_date'))) : now()->endOfDay();
        }

        // Ambil data pemesanan
        $laporan = DB::table('pemesanan')
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('tanggal_pemesanan', [$startDate, $endDate]))
            ->orderBy('tanggal_pemesanan', 'desc')
            ->get();

        // Hitung total per metode
        $totalTunai = $laporan->where('metode_pembayaran', 'Tunai')->sum('harga_total');
        $totalTransfer = $laporan->where('metode_pembayaran', 'Transfer')->sum('harga_total');
        $totalQRIS = $laporan->where('metode_pembayaran', 'QRIS')->sum('harga_total');
        $totalPendapatan = $totalTunai + $totalTransfer + $totalQRIS;

        // Hitung pengeluaran
        $totalPengeluaran = DB::table('stok_movement')
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->where('status', 'Masuk')
            ->sum('harga');

        $labaBersih = $totalPendapatan - $totalPengeluaran;

        return view('laporan.laporan', compact(
            'laporan', 'totalTunai', 'totalTransfer', 'totalQRIS',
            'totalPendapatan', 'totalPengeluaran', 'labaBersih',
            'filter', 'startDate', 'endDate'
        ));
    }

    // 🔹 Export ke PDF
    public function exportPDF(Request $request)
{
    $data = $this->getLaporanData($request);
    $laporan = collect($data['laporan']);

    $totalTunai = $laporan->where('metode_pembayaran', 'Tunai')->sum('harga_total');
    $totalTransfer = $laporan->where('metode_pembayaran', 'Transfer')->sum('harga_total');
    $totalQRIS = $laporan->where('metode_pembayaran', 'QRIS')->sum('harga_total');
    $totalPemasukan = $totalTunai + $totalTransfer + $totalQRIS;

    // Render Blade view ke HTML
    $html = view('laporan.laporan_pdf', compact(
        'laporan',
        'totalTunai',
        'totalTransfer',
        'totalQRIS',
        'totalPemasukan'
    ))->render();

    try {
        // 🔹 Buat client PDFCrowd
        $client = new \Pdfcrowd\HtmlToPdfClient(
            env('andrecolle31'),
            env('fe78a3e7d645b653bcc5de19a5b227c5')
        );

        // 🔹 Konversi HTML ke PDF
        $pdfData = $client->convertString($html);

        // 🔹 Kembalikan file download ke browser
        return response($pdfData)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="laporan_keuangan.pdf"')
            ->header('Cache-Control', 'no-cache, must-revalidate');
    } catch (\Pdfcrowd\Error $why) {
        return response('Gagal membuat PDF: ' . $why->getMessage(), 500);
    }
}

    // 🔹 Export ke Excel
    public function exportExcel(Request $request)
    {
        $laporan = $this->getLaporanData($request);

        return Excel::download(
            new LaporanExport($laporan),
            'laporan_keuangan.xlsx'
        );
    }

    // 🔹 Helper ambil data
    private function getLaporanData(Request $request)
{
    $filter = $request->input('filter', 'harian');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $start = $startDate ? date('Y-m-d 00:00:00', strtotime($startDate)) : now()->startOfDay();
    $end = $endDate ? date('Y-m-d 23:59:59', strtotime($endDate)) : now()->endOfDay();

    $laporan = DB::table('pemesanan')
        ->when($start && $end, fn($q) => $q->whereBetween('tanggal_pemesanan', [$start, $end]))
        ->orderBy('tanggal_pemesanan', 'desc')
        ->get();

    return ['laporan' => $laporan]; // ✅ penting! supaya ['laporan'] bisa diakses
}
}
