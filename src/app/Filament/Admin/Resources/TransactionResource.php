<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
// 🚀 PERBAIKAN NAMESPACE ABSOLUT UNTUK EXPORT & IMPORT FILAMENT V3 Pak!
use App\Filament\Exports\TransactionExporter;
use App\Filament\Imports\TransactionImporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ExportBulkAction;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Data Transaksi (Excel)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Suku Cadang')
                    ->schema([
                        Forms\Components\TextInput::make('code_barang')->label('Code Barang')->maxLength(255),
                        Forms\Components\TextInput::make('nama_barang')->label('Nama Barang')->required()->maxLength(255),
                        Forms\Components\TextInput::make('nomor_rak')->label('Nomor Rak'),
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori Sistem Master')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Data Kuantitas & Catatan Finansial')
                    ->schema([
                        Forms\Components\TextInput::make('unit')->label('Unit Satuan')->default('PCE'),
                        Forms\Components\TextInput::make('harga')->label('Harga Satuan (Rp)')->numeric()->prefix('Rp')->default(0),
                        Forms\Components\TextInput::make('subtotal')->label('Subtotal (Rp)')->numeric()->prefix('Rp')->default(0),
                        Forms\Components\TextInput::make('stock')->label('Stock Awal Sistem')->numeric()->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Data Hasil Stock Opname Lapangan')
                    ->schema([
                        Forms\Components\TextInput::make('saldo_awal')->label('Saldo Awal')->numeric()->default(0),
                        Forms\Components\TextInput::make('saldo_akhir')->label('Saldo Akhir')->numeric()->default(0),
                        Forms\Components\TextInput::make('physical_stock')->label('Physical Stock (Stok Fisik)')->numeric()->default(0),
                        Forms\Components\TextInput::make('difference')->label('Difference (Selisih)')->numeric()->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Metadata Tambahan Klien')
                    ->schema([
                        Forms\Components\TextInput::make('month')->label('Month')->default('December 2025'),
                        Forms\Components\TextInput::make('vendor')->label('Vendor')->default('-'),
                        Forms\Components\Select::make('status')
                            ->label('Status Opname')
                            ->options([
                                'AMAN' => 'AMAN',
                                'STOK HABIS / RE-ORDER' => 'STOK HABIS / RE-ORDER',
                                'URGENT/HARUS SEGERA ORDER' => 'URGENT/HARUS SEGERA ORDER',
                            ])->default('AMAN')->required(),
                        Forms\Components\TextInput::make('petugas_opname')->label('Petugas Opname'),
                        Forms\Components\Textarea::make('keterangan')->label('Keterangan')->columnSpan(2),
                        Forms\Components\TextInput::make('remark')->label('Remark'),
                        Forms\Components\TextInput::make('aksi')->label('Aksi (Template Excel)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code_barang')
                    ->label('Code Barang')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (Transaction $record): string => "Rak: " . ($record->nomor_rak ?? '-')),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori Master')
                    ->badge()
                    ->color(fn ($record) => match($record->category?->name) {
                        'SPARE PART IMPORT' => 'purple',
                        'SPARE PART LOKAL' => 'success',
                        'BARANG REPAIR BC26' => 'danger',
                        'CONSUMABLE & UTILITY' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('physical_stock')
                    ->label('Stok Fisik')
                    ->numeric(0, ',', '.')
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status Opname')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'AMAN' => 'success',
                        'STOK HABIS / RE-ORDER' => 'warning',
                        'URGENT/HARUS SEGERA ORDER' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            // 🚀 PERBAIKAN DI SINI PAK: Menggunakan ImportAction dan ExportAction bawaan Core Filament Tables
            ->headerActions([
                ImportAction::make()
                    ->label('Import CSV')
                    ->importer(TransactionImporter::class)
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning'),
                
                ExportAction::make()
                    ->label('Export CSV / Excel')
                    ->exporter(TransactionExporter::class)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori Master')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Opname')
                    ->options([
                        'AMAN' => 'AMAN',
                        'STOK HABIS / RE-ORDER' => 'STOK HABIS / RE-ORDER',
                        'URGENT/HARUS SEGERA ORDER' => 'URGENT/HARUS SEGERA ORDER',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->label('Export Baris Terpilih')
                        ->exporter(TransactionExporter::class),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Rincian Logistik 20 Kolom Excel Klien')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('code_barang')->label('1. Code Barang')->weight('bold'),
                                TextEntry::make('nama_barang')->label('2. Nama Barang')->columnSpan(2),
                                TextEntry::make('month')->label('3. Month'),
                                TextEntry::make('vendor')->label('4. Vendor'),
                                TextEntry::make('stock')->label('5. Stock Awal Sistem'),
                                TextEntry::make('harga')->label('6. Harga Satuan')->money('IDR'),
                                TextEntry::make('subtotal')->label('7. Subtotal Keuangan')->money('IDR'),
                                TextEntry::make('keterangan')->label('8. Keterangan')->columnSpan(2),
                                TextEntry::make('nomor_rak')->label('9. Nomor Rak')->badge()->color('purple'),
                                TextEntry::make('kategori_excel')->label('10. Kategori (Excel)'),
                                TextEntry::make('type_excel')->label('11. Type (Excel)'),
                                TextEntry::make('unit')->label('12. Unit Satuan'),
                                TextEntry::make('saldo_awal')->label('13. Saldo Awal'),
                                TextEntry::make('saldo_akhir')->label('14. Saldo Akhir'),
                                TextEntry::make('physical_stock')->label('15. Physical Stock')->weight('bold'),
                                TextEntry::make('difference')->label('16. Difference (Selisih)'),
                                TextEntry::make('remark')->label('17. Remark'),
                                TextEntry::make('petugas_opname')->label('18. Petugas Opname'),
                                TextEntry::make('status')->label('19. Status Batas')->badge()->color(fn ($state) => $state === 'AMAN' ? 'success' : 'danger'),
                                TextEntry::make('aksi')->label('20. Aksi (Template Excel)'),
                            ])
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}