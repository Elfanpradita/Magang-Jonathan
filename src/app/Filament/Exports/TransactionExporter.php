<?php

namespace App\Filament\Exports;

use App\Models\Transaction;
use Filament\Actions\Exports\ExportColumn; // 🚀 NAMESPACE YANG BENAR PAK
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionExporter extends Exporter
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code_barang')->label('Code Barang'),
            ExportColumn::make('nama_barang')->label('Nama Barang'),
            ExportColumn::make('month')->label('Month'),
            ExportColumn::make('vendor')->label('Vendor'),
            ExportColumn::make('stock')->label('Stock'),
            ExportColumn::make('harga')->label('Harga'),
            ExportColumn::make('subtotal')->label('Subtotal'),
            ExportColumn::make('keterangan')->label('Keterangan'),
            ExportColumn::make('nomor_rak')->label('Nomor Rak'),
            ExportColumn::make('kategori_excel')->label('Kategori'),
            ExportColumn::make('type_excel')->label('Type'),
            ExportColumn::make('unit')->label('Unit'),
            ExportColumn::make('saldo_awal')->label('Saldo Awal'),
            ExportColumn::make('saldo_akhir')->label('Saldo Akhir'),
            ExportColumn::make('physical_stock')->label('Physical Stock'),
            ExportColumn::make('difference')->label('Difference'),
            ExportColumn::make('remark')->label('Remark'),
            ExportColumn::make('petugas_opname')->label('Petugas Opname'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('aksi')->label('Aksi'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export data transaksi Anda telah selesai dan ' . number_format($export->successful_rows) . ' baris berhasil diexport.';

        if ($failedRowsCount = $export->failed_rows) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diexport.';
        }

        return $body;
    }
}