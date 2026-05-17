<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class InFlowDemandTrend extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'In-Flow Demand & Expense Trend (Bulanan)';
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full'; // Grafik melebar penuh di dashboard

    protected function getData(): array
    {
        // Mengambil data agregasi bulanan untuk tahun berjalan
        $currentYear = date('Y');
        
        $monthlyExpenses = Transaction::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('type', 'expense')
            ->whereYear('date', $currentYear)
            ->groupBy(DB::raw('MONTH(date)'))
            ->pluck('total', 'month')
            ->toArray();

        // Menyusun data ke dalam susunan array 12 bulan (Jan - Des)
        $dataValues = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataValues[] = $monthlyExpenses[$i] ?? 0; // default 0 jika bulan terkait belum ada transaksi
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pengeluaran (IDR)',
                    'data' => $dataValues,
                    'backgroundColor' => '#7f5af0',
                    'borderColor' => '#7f5af0',
                    'fill' => false,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Jenis grafik garis modern
    }
}