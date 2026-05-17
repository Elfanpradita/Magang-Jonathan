<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;

class DowntimeTrend extends ChartWidget
{
    // 1. Tempatkan di urutan ke-3 tepat di bawah grafik tren keuangan Pak!
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Tren Downtime Mesin Gudang (Menit) - 2026';
    
    protected static ?string $pollingInterval = '30s'; // Auto-refresh tiap 30 detik

    // Mengatur lebar grafik agar seimbang (bisa diset 'full' atau dibiarkan grid default)
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Data simulasi menit downtime bulanan sepanjang tahun 2026 untuk keperluan prototype
        $downtimeData = [45, 20, 60, 15, 30, 0, 85, 40, 25, 10, 5, 12]; 

        return [
            'datasets' => [
                [
                    'label' => 'Durasi Gangguan Mesin (Menit)',
                    'data' => $downtimeData,
                    // Kita gunakan warna merah cerah (#ff5555) agar kontras dengan warna keuangan kemarin
                    'backgroundColor' => 'rgba(255, 85, 85, 0.2)',
                    'borderColor' => '#ff5555',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        // Kita gunakan jenis 'bar' (grafik batang) sesuai dengan mock-up halaman maintenance Anda Pak!
        return 'bar';
    }
}