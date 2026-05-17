<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\ThresholdRule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use OpenSpout\Reader\XLSX\Reader;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan membaca ribuan data langsung dari file XLSX asli.
     */
    public function run(): void
    {
        // 1. BUAT AKUN USER LOGIN
        $admin = User::updateOrCreate(
            ['email' => 'jonathanch@gmail.com'],
            ['name' => 'John Cafe', 'password' => Hash::make('p455w0rd')]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@dwi.com'],
            ['name' => 'Operator Gudang', 'password' => Hash::make('p455w0rd')]
        );

        // 2. SIAPKAN KATEGORI INDUK
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

        // 3. BACA OTOMATIS RIBUAN DATA DARI: laporan_sparepart.xlsx
        $sparepartXlsxFile = database_path('seeders/xlsx/laporan_sparepart.xlsx');
        
        if (file_exists($sparepartXlsxFile)) {
            $reader = new Reader();
            $reader->open($sparepartXlsxFile);

            $transactionIndex = 1;

            foreach ($reader->getSheetIterator() as $sheet) {
                $isHeader = true;

                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->toArray();

                    if ($isHeader) {
                        $isHeader = false;
                        continue;
                    }

                    // PENGAMAN: Cek jika data berupa objek tanggal, ubah ke string format Y-m-d
                    $itemName = $cells[0] ?? null;
                    if ($itemName instanceof \DateTimeInterface) {
                        $itemName = $itemName->format('Y-m-d');
                    } else {
                        $itemName = (string) $itemName;
                    }

                    $vendorName = $cells[1] ?? 'Vendor General';
                    if ($vendorName instanceof \DateTimeInterface) {
                        $vendorName = $vendorName->format('Y-m-d');
                    } else {
                        $vendorName = (string) $vendorName;
                    }

                    $price = isset($cells[2]) ? (float)$cells[2] : 150000;

                    if (blank($itemName)) continue;

                    $randomCategory = array_rand($categories);
                    $randomMonth = rand(1, 12);
                    $transactionDate = Carbon::create(2026, $randomMonth, rand(1, 28))->format('Y-m-d');
                    $qty = rand(1, 5);

                    Transaction::create([
                        'category_id' => $categories[$randomCategory]->id,
                        'vendor' => $vendorName,
                        'transaction_number' => 'TXN-2026' . sprintf('%02d', $randomMonth) . '-' . sprintf('%04d', $transactionIndex),
                        'type' => 'expense',
                        'description' => 'Pengadaan Suku Cadang: ' . $itemName . ' (Qty: ' . $qty . ' pcs)',
                        'amount' => $price * $qty,
                        'date' => $transactionDate,
                        'status' => 'approved',
                        'created_by_id' => $staff->id,
                        'approved_by_id' => $admin->id,
                    ]);

                    $transactionIndex++;
                }
            }
            $reader->close();
        }

        // 4. BACA OTOMATIS DATA DARI: limit_stock.xlsx UNTUK THRESHOLD RULES
        $limitXlsxFile = database_path('seeders/xlsx/limit_stock.xlsx');
        if (file_exists($limitXlsxFile)) {
            $reader = new Reader();
            $reader->open($limitXlsxFile);

            foreach ($reader->getSheetIterator() as $sheet) {
                $isHeader = true;
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->toArray();

                    if ($isHeader) {
                        $isHeader = false;
                        continue;
                    }

                    // PENGAMAN KRUSIAL: Ubah objek DateTime atau tipe apa pun menjadi String murni
                    $catName = $cells[0] ?? null;
                    if ($catName instanceof \DateTimeInterface) {
                        $catName = $catName->format('Y-m-d');
                    } else {
                        $catName = (string) $catName;
                    }

                    $minCritical = isset($cells[1]) ? (int)$cells[1] : 10;
                    $minWarning = isset($cells[2]) ? (int)$cells[2] : 20;

                    if (blank($catName)) continue;

                    // Sekarang aman dari error Object to String Conversion Pak!
                    $matchedCategory = Category::where('name', 'LIKE', '%' . $catName . '%')->first();
                    if ($matchedCategory) {
                        ThresholdRule::updateOrCreate(
                            ['category_id' => $matchedCategory->id],
                            [
                                'min_stock_critical' => $minCritical,
                                'min_stock_warning' => $minWarning,
                                'auto_reorder' => true,
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }
            $reader->close();
        }
    }
}