<?php

namespace App\Filament\Admin\Pages;

use App\Models\Category;
use App\Models\ThresholdRule;
use App\Models\Transaction;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

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
     * Memuat aturan threshold dan mendeteksi barang riil hasil import excel yang melanggar batas
     */
    public function loadData(): void
    {
        // 1. Ambil semua aturan threshold kategori master
        $this->rules = ThresholdRule::with('category')->get();
        
        $this->violations = [];
        $this->criticalCount = 0;
        $this->warningCount = 0;

        // 2. AMAN SEKARANG: Menggunakan kolom 'status' sesuai skema 20 kolom klien Pak!
        $badTransactions = Transaction::with('category')
            ->whereIn('status', ['STOK HABIS / RE-ORDER', 'URGENT/HARUS SEGERA ORDER'])
            ->latest()
            ->get();

        foreach ($badTransactions as $txn) {
            $type = ($txn->status === 'URGENT/HARUS SEGERA ORDER') ? 'CRITICAL' : 'WARNING';
            
            // Ambil limit rule statis sesuai kategori barang tersebut untuk visualisasi data
            $rule = $this->rules->firstWhere('category_id', $txn->category_id);
            $limitStock = $type === 'CRITICAL' 
                ? ($rule->min_stock_critical ?? 5) 
                : ($rule->min_stock_warning ?? 12);

            $this->violations[] = [
                'item_name'     => $txn->nama_barang ?? 'Sparepart Unit', // Menggunakan nama_barang sesuai model baru
                'category'      => $txn->category->name ?? 'SPARE PART LOKAL',
                'item_code'     => $txn->code_barang ?? '-', // Menggunakan code_barang sesuai model baru
                'current_stock' => (int) $txn->physical_stock,
                'limit'         => $limitStock,
                'type'          => $type
            ];

            if ($type === 'CRITICAL') {
                $this->criticalCount++;
            } else {
                $this->warningCount++;
            }
        }
    }

    /**
     * Tombol Aksi Kustom "+ Tambah Rule" modal popup
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
                        ->default(true),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Rule Aktif')
                        ->helperText('Aktifkan atau nonaktifkan rule ini')
                        ->default(true),
                ])
                ->action(function (array $data) {
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