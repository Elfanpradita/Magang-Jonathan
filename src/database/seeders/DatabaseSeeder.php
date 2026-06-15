<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\ThresholdRule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan memetakan 20 kolom asli klien secara utuh.
     */
    public function run(): void
    {
        // 1. BUAT AKUN LOGIN UTAMA
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);
        
        $admin = User::updateOrCreate(
            ['email' => 'jonathanch@gmail.com'],
            ['name' => 'John Cafe', 'password' => Hash::make('p455w0rd')]
        );
        $admin->assignRole($superAdminRole);

        $staff = User::updateOrCreate(
            ['email' => 'staff@dwi.com'],
            ['name' => 'Operator Gudang Utama', 'password' => Hash::make('p455w0rd')]
        );

        // 2. SIAPKAN KATEGORI MASTER SISTEM
        $categoriesData = [
            'SPARE PART IMPORT' => ['type' => 'capex', 'color' => '#7f5af0', 'icon' => 'heroicon-o-arrow-down-tray'],
            'SPARE PART LOKAL' => ['type' => 'operational', 'color' => '#2cb67d', 'icon' => 'heroicon-o-shopping-bag'],
            'BARANG REPAIR BC26' => ['type' => 'essential', 'color' => '#ff5555', 'icon' => 'heroicon-o-wrench'],
            'CONSUMABLE & UTILITY' => ['type' => 'variable', 'color' => '#fdba74', 'icon' => 'heroicon-o-bolt'],
        ];

        $categories = [];
        foreach ($categoriesData as $name => $meta) {
            $categories[$name] = Category::updateOrCreate(
                ['name' => $name],
                ['type' => $meta['type'], 'icon' => $meta['icon'], 'color' => $meta['color'], 'is_active' => true]
            );
        }

        // Buat Aturan Batas Minimum Kontrol Alarm
        foreach ($categories as $name => $cat) {
            ThresholdRule::updateOrCreate(
                ['category_id' => $cat->id],
                [
                    'min_stock_critical' => $name === 'SPARE PART IMPORT' ? 15 : 5,
                    'min_stock_warning' => $name === 'SPARE PART IMPORT' ? 30 : 12,
                    'auto_reorder' => true,
                    'is_active' => true,
                ]
            );
        }

        // 3. SINKRONISASI TOTAL 20 KOLOM DARI FILE EXCEL DI FOLDER xlsx
        $csvFilePath = database_path('seeders/xlsx/Laporan_Fix_Siap_Upload (5).csv');
        
        if (file_exists($csvFilePath)) {
            $fileHandle = fopen($csvFilePath, 'r');
            fgetcsv($fileHandle); // Lewati header

            $transactionIndex = 1;

            while (($cells = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
                
                // Ekstraksi pemetaan 20 kolom asli secara urut indeks array (0-19)
                $codeBarang    = $cells[0] ?? null;
                $namaBarang    = $cells[1] ?? null;
                $month         = $cells[2] ?? 'December 2025';
                $vendor        = $cells[3] ?? '-';
                $stock         = isset($cells[4]) ? (float)$cells[4] : 0;
                $harga         = isset($cells[5]) ? (float)$cells[5] : 0;
                $subtotal      = isset($cells[6]) ? (float)$cells[6] : 0;
                $keterangan    = $cells[7] ?? null;
                $nomorRak      = $cells[8] ?? null;
                $kategoriExcel = $cells[9] ?? null;
                $typeExcel     = $cells[10] ?? null;
                $unit          = $cells[11] ?? 'PCE';
                $saldoAwal     = isset($cells[12]) ? (float)$cells[12] : 0;
                $saldoAkhir    = isset($cells[13]) ? (float)$cells[13] : 0;
                $physicalStock = isset($cells[14]) ? (float)$cells[14] : 0;
                $difference    = isset($cells[15]) ? (float)$cells[15] : 0;
                $remark        = $cells[16] ?? null;
                $petugasOpname = $cells[17] ?? null;
                $statusLabel   = $cells[18] ?? 'AMAN';
                $aksi          = $cells[19] ?? null;

                if (blank($namaBarang)) continue;

                // 🤖 AI Engine Klasifikasi Kategori Master
                $lowercaseName = strtolower($namaBarang);
                $categoryName = 'SPARE PART LOKAL';

                if (str_contains($lowercaseName, 'repair') || str_contains($lowercaseName, 'bc26')) {
                    $categoryName = 'BARANG REPAIR BC26';
                } elseif (
                    str_contains($lowercaseName, 'smc') || str_contains($lowercaseName, 'omron') || 
                    str_contains($lowercaseName, 'siemens') || str_contains($lowercaseName, 'festo') || 
                    str_contains($lowercaseName, 'inverter') || str_contains($lowercaseName, 'sensor')
                ) {
                    $categoryName = 'SPARE PART IMPORT';
                } elseif (
                    str_contains($lowercaseName, 'cat ') || str_contains($lowercaseName, 'lem ') || 
                    str_contains($lowercaseName, 'cleaner') || str_contains($lowercaseName, 'vixal') || 
                    str_contains($lowercaseName, 'loctite') || str_contains($lowercaseName, 'grease')
                ) {
                    $categoryName = 'CONSUMABLE & UTILITY';
                }

                // Kalkulasi data tanggal simulasi grafik 2026
                $randomMonth = rand(1, 12);
                $transactionDate = Carbon::create(2026, $randomMonth, rand(1, 28))->format('Y-m-d');

                Transaction::create([
                    'category_id' => $categories[$categoryName]->id,
                    'transaction_number' => 'OPN-2026' . sprintf('%02d', $randomMonth) . '-' . sprintf('%04d', $transactionIndex),
                    'type' => 'expense',
                    'date' => $transactionDate,
                    'created_by_id' => $staff->id,
                    'approved_by_id' => $admin->id,

                    // ✨ PENGISIAN DATA 20 KOLOM ASLI EXCEL KLIEN
                    'code_barang' => $codeBarang,
                    'nama_barang' => $namaBarang,
                    'month' => $month,
                    'vendor' => $vendor,
                    'stock' => $stock,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'keterangan' => $keterangan,
                    'nomor_rak' => $nomorRak,
                    'kategori_excel' => $kategoriExcel,
                    'type_excel' => $typeExcel,
                    'unit' => $unit,
                    'saldo_awal' => $saldoAwal,
                    'saldo_akhir' => $saldoAkhir,
                    'physical_stock' => $physicalStock,
                    'difference' => $difference,
                    'remark' => $remark,
                    'petugas_opname' => $petugasOpname,
                    'status' => $statusLabel,
                    'aksi' => $aksi,
                ]);

                $transactionIndex++;
            }
            fclose($fileHandle);
        }
    }
}