<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanExport implements FromView, WithColumnFormatting
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function view(): View
    {
        return view('laporan.laporan_excel', [
            'laporan' => $this->laporan
        ]);
    }

    // Format kolom angka agar Excel baca sebagai numeric dengan ribuan
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // kolom Tunai
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // kolom Transfer
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // kolom QRIS
        ];
    }
}
