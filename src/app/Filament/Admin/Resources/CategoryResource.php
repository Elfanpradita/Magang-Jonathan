<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kategori')
                    ->description('Kelola metadata kategori transaksi dan batasannya.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g., Logistik, Operasional Pabrik'),

                        Forms\Components\Select::make('type')
                            ->label('Tipe Pengeluaran')
                            ->options([
                                'essential' => 'Essential (Penting)',
                                'operational' => 'Operational (Operasional)',
                                'variable' => 'Variable (Berubah-ubah)',
                                'capex' => 'Capital Expenditure (Aset)',
                            ])
                            ->required(),

                        Forms\Components\Select::make('parent_id')
                            ->label('Kategori Induk (Parent)')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->placeholder('Pilih jika ini adalah sub-kategori'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Visual & Tampilan')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('Nama Heroicon')
                            ->placeholder('e.g., heroicon-o-wallet')
                            ->helperText('Gunakan format nama dari heroicons.com'),

                        Forms\Components\ColorPicker::make('color')
                            ->label('Warna Label')
                            ->default('#7f5af0'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Tampilan')
                            ->numeric()
                            ->default(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->inline(false),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon(fn ($record) => $record->icon ?? 'heroicon-o-squares-2x2')
                    ->iconColor('primary'),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Induk')
                    ->placeholder('— (Kategori Utama)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'essential' => 'danger',
                        'operational' => 'warning',
                        'variable' => 'info',
                        'capex' => 'success',
                        default => 'gray',
                    }),

                // 🚀 KOLOM HITUNG BERJALAN: Menghitung jumlah item terdata dari 20 kolom baru Pak!
                Tables\Columns\TextColumn::make('transactions_stock')
                    ->label('Sisa Stok Riil')
                    ->getStateUsing(function ($record) {
                        // Menjumlahkan seluruh physical_stock dari 4.105 data excel berdasarkan kategori
                        $totalStokFisik = \App\Models\Transaction::where('category_id', $record->id)
                            ->sum('physical_stock'); 

                        return number_format($totalStokFisik, 0, ',', '.') . ' Unit';
                    })
                    ->badge()
                    ->color(fn ($record) => match($record->name) {
                        'SPARE PART IMPORT' => 'purple',
                        'SPARE PART LOKAL' => 'success',
                        'BARANG REPAIR BC26' => 'danger',
                        'CONSUMABLE & UTILITY' => 'warning',
                        default => 'info',
                    }),

                Tables\Columns\ColorColumn::make('color')
                    ->label('Warna Visual'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'essential' => 'Essential',
                        'operational' => 'Operational',
                        'variable' => 'Variable',
                        'capex' => 'Capex',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}