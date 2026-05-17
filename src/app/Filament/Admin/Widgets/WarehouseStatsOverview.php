<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Category;
use App\Models\Transaction;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WarehouseStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s'; // Auto-refresh data tiap 10 detik

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Ambil data agregasi real-time dari database
        $totalCategories = Category::where('is_active', true)->count();
        $totalTransactions = Transaction::count();
        
        // Ambil jumlah transaksi yang statusnya masih pending (perlu approval)
        $pendingApproval = Transaction::where('status', 'pending')->count();

        return [
            Stat::make('Total Kategori Aktif', $totalCategories)
                ->description('Kategori master dalam sistem')
                ->descriptionIcon('heroicon-m-tag', IconPosition::Before)
                ->chart([3, 5, 4, 7, 6, 9, $totalCategories > 0 ? $totalCategories : 5])
                ->color('info'),

            Stat::make('Total Transaksi', $totalTransactions)
                ->description('Akumulasi aliran dana gudang')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart([10, 22, 18, 25, 30, 42, $totalTransactions > 0 ? $totalTransactions : 15])
                ->color('success'),

            Stat::make('Pending Approval / Stok Kritis', $pendingApproval)
                ->description($pendingApproval > 0 ? 'Perlu tindakan verifikasi!' : 'Semua aman terkendali')
                ->descriptionIcon('heroicon-m-exclamation-triangle', IconPosition::Before)
                ->chart([1, 4, 2, 5, 3, 6, $pendingApproval])
                ->color($pendingApproval > 0 ? 'danger' : 'gray'),
        ];
    }
}