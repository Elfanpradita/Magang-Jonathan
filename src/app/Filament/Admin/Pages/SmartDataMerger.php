<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;

class SmartDataMerger extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'Utilitas SaaS';
    protected static ?string $navigationLabel = 'Smart Data Merger';
    protected static string $view = 'filament.admin.pages.smart-data-merger';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Unggah Dokumen Gudang / Vendor')
                    ->description('Gabungkan banyak berkas CSV atau Excel ke dalam format skema master database secara otomatis.')
                    ->schema([
                        FileUpload::make('uploaded_files')
                            ->label('Pilih Berkas Berformat CSV / XLSX')
                            ->multiple()
                            ->directory('bulk-merger-temp')
                            ->acceptedFileTypes([
                                'text/csv', 
                                'application/vnd.ms-excel', 
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                            ])
                            ->maxSize(10240) // Batas 10MB
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function processMerge()
    {
        $this->validate();

        // Di sini nantinya ditaruh logic parser OpenSpout / Excel reader Anda Pak.
        // Untuk prototype, kita tampilkan notifikasi sukses langsung.

        Notification::make()
            ->title('Data Berhasil Digabungkan!')
            ->description('Berkas eksternal sukses dipetakan ke dalam tabel transaksi.')
            ->success()
            ->send();

        $this->form->fill(); // Kosongkan form kembali
    }
}