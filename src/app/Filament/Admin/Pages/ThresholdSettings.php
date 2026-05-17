<?php

namespace App\Filament\Admin\Pages;

use App\Models\Category;
use App\Models\ThresholdRule;
use App\Models\Transaction; // Menggunakan data transaksi untuk kalkulasi sisa stok
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ThresholdSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Threshold Settings';
    protected static string $view = 'filament.admin.pages.threshold-settings';

    public Collection $rules;
    public array $violations = [];
    public int $criticalCount = 0;
    public int $warningCount = 0;

    public function mount(): void
    {
        $this->loadData();
    }

    /**
     * Memuat aturan threshold dan mendeteksi barang yang melanggar batas stok
     */
    public function loadData(): void
    {
        $this->rules = ThresholdRule::with('category')->get();
        $this->violations = [];
        $this->criticalCount = 0;
        $this->warningCount = 0;

        // Simulasi Kalkulasi Stok Produk per Kategori dari tabel Transaksi untuk Prototype
        foreach ($this->rules as $rule) {
            if (!$rule->is_active) continue;

            // Hitung sisa stok berdasarkan tipe transaksi (Inflow/Pemasukan dikurangi Expense/Pengeluaran)
            $inflow = Transaction::where('category_id', $rule->category_id)->where('type', 'income')->sum('amount');
            $expense = Transaction::where('category_id', $rule->category_id)->where('type', 'expense')->sum('amount');
            
            // Contoh simulasi default angka stok riil jika database Anda masih baru kosong:
            $currentStock = ($inflow - $expense) <= 0 ? 66 : ($inflow - $expense); 

            // Cek kondisi stok terhadap rule threshold kritis & warning
            if ($currentStock <= $rule->min_stock_critical) {
                $this->violations[] = [
                    'item_name' => 'Circuit Breaker 16A', // Contoh dummy item simulasi dari gambar Anda
                    'category' => $rule->category->name,
                    'current_stock' => $currentStock,
                    'limit' => $rule->min_stock_critical,
                    'type' => 'CRITICAL'
                ];
                $this->criticalCount++;
            } elseif ($currentStock <= $rule->min_stock_warning) {
                $this->violations[] = [
                    'item_name' => 'Kabel Tembaga Roll',
                    'category' => $rule->category->name,
                    'current_stock' => $currentStock,
                    'limit' => $rule->min_stock_warning,
                    'type' => 'WARNING'
                ];
                $this->warningCount++;
            }
        }
    }

    /**
     * Tombol Aksi Kustom "+ Tambah Rule" yang memunculkan form modal persis sesuai gambar Anda
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('addRule')
                ->label('Tambah Rule')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->modalHeading('Tambah Threshold Rule')
                ->modalSubmitActionLabel('Tambah Rule')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Forms\Components\Select::make('category_id')
                        ->label('KATEGORI')
                        ->options(Category::where('is_active', true)->pluck('name', 'id'))
                        ->placeholder('Pilih Kategori...')
                        ->required(),
                    
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('min_stock_critical')
                                ->label('🔴 STOK MINIMUM (KRITIS)')
                                ->numeric()
                                ->default(5)
                                ->required(),
                            
                            Forms\Components\TextInput::make('min_stock_warning')
                                ->label('🟡 STOK WARNING')
                                ->numeric()
                                ->default(10)
                                ->required(),
                        ]),

                    Forms\Components\Toggle::make('auto_reorder')
                        ->label('Auto Reorder')
                        ->helperText('Tandai untuk reorder otomatis saat stok di bawah minimum')
                        ->default(false),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Rule Aktif')
                        ->helperText('Aktifkan atau nonaktifkan rule ini')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    // Simpan data aturan baru ke dalam MySQL
                    ThresholdRule::create($data);

                    Notification::make()
                        ->title('Threshold Rule Berhasil Ditambahkan')
                        ->success()
                        ->send();

                    $this->loadData();
                })
        ];
    }
}