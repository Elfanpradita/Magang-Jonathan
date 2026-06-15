<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

// 🚀 NAMA KELAS DISESUAIKAN 100% DENGAN FILE DI SERVER ANDA Pak!
class WarehouseStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Hitung Total Pengguna Sistem
        $totalUsers = User::count();

        // 2. Hitung Total Kategori Master Aktif
        $totalCategories = Category::where('is_active', true)->count();

        // 3. Hitung SUM(subtotal) dari 4.105 data Excel tahun 2026
        $totalDanaGudang = Transaction::whereYear('date', 2026)->sum('subtotal');

        // 4. Hitung baris berstatus URGENT sesuai kolom baru klien
        $stokKritisCount = Transaction::where('status', 'URGENT/HARUS SEGERA ORDER')->count();

        return [
            Stat::make('Users', $totalUsers)
                ->description('Pengguna sistem aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Kategori Aktif', $totalCategories)
                ->description('Kategori master dalam sistem')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('success'),

            Stat::make('Total Anggaran Gudang', 'Rp ' . number_format($totalDanaGudang, 0, ',', '.'))
                ->description('Akumulasi nilai subtotal aset gudang (2026)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 4, 10, 3, 15, 4, 17])
                ->color('primary'),

            Stat::make('Stok Kritis (Urgent)', $stokKritisCount . ' Item')
                ->description('Butuh tindakan order segera')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart($stokKritisCount > 0 ? [5, 10, 15, 20, 25] : [0, 0, 0])
                ->color($stokKritisCount > 0 ? 'danger' : 'success'),
        ];
    }
}