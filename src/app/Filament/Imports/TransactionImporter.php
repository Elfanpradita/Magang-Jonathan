<?php

namespace App\Filament\Imports;

use App\Models\Transaction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TransactionImporter extends Importer
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code_barang')->label('Code Barang'),
            ImportColumn::make('nama_barang')->label('Nama Barang')->requiredMapping(),
            ImportColumn::make('month')->label('Month'),
            ImportColumn::make('vendor')->label('Vendor'),
            ImportColumn::make('stock')->label('Stock')->numeric(),
            ImportColumn::make('harga')->label('Harga')->numeric(),
            ImportColumn::make('subtotal')->label('Subtotal')->numeric(),
            ImportColumn::make('keterangan')->label('Keterangan'),
            ImportColumn::make('nomor_rak')->label('Nomor Rak'),
            ImportColumn::make('kategori_excel')->label('Kategori'),
            ImportColumn::make('type_excel')->label('Type'),
            ImportColumn::make('unit')->label('Unit'),
            ImportColumn::make('saldo_awal')->label('Saldo Awal')->numeric(),
            ImportColumn::make('saldo_akhir')->label('Saldo Akhir')->numeric(),
            ImportColumn::make('physical_stock')->label('Physical Stock')->numeric(),
            ImportColumn::make('difference')->label('Difference')->numeric(),
            ImportColumn::make('remark')->label('Remark'),
            ImportColumn::make('petugas_opname')->label('Petugas Opname'),
            ImportColumn::make('status')->label('Status'),
            ImportColumn::make('aksi')->label('Aksi'),
        ];
    }

    /**
     * 🚀 FIX DI SINI PAK: Menghilangkan kata 'static' agar sinkron dengan Core Filament v3
     */
    public function resolveRecord(): ?Transaction
    {
        return new Transaction();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import data transaksi Anda telah selesai dan ' . number_format($import->successful_rows) . ' baris data berhasil dimasukkan.';

        if ($failedRowsCount = $import->failed_rows) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris data gagal diproses.';
        }

        return $body;
    }
}