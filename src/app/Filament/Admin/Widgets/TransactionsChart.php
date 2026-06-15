<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TransactionsChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pengadaan Suku Cadang Gudang (2026)';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // 🚀 AMAN SEKARANG: Menggunakan SUM(subtotal) sesuai skema 20 kolom klien Pak!
        $monthlyTotals = Transaction::select(
            DB::raw('MONTH(date) as month'),
            DB::raw('SUM(subtotal) as total')
        )
        ->whereYear('date', 2026)
        ->groupBy(DB::raw('MONTH(date)'))
        ->orderBy(DB::raw('MONTH(date)'))
        ->pluck('total', 'month')
        ->toArray();

        // Siapkan susunan data untuk 12 bulan penuh
        $chartData = [];
        for ($month = 1; $month <= 12; $month++) {
            // Jika bulan terkait kosong, kita berikan fallback angka 0
            $chartData[] = $monthlyTotals[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Anggaran Keluar (Rp)',
                    'data' => $chartData,
                    'backgroundColor' => 'rgba(127, 90, 240, 0.2)',
                    'borderColor' => '#7f5af0',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4, // Membuat lekukan garis chart halus & premium
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}