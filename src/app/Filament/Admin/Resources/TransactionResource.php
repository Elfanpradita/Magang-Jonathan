<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Detail Transaksi')
                            ->schema([
                                Forms\Components\TextInput::make('transaction_number')
                                    ->label('No. Transaksi')
                                    ->default('TXN-' . strtoupper(uniqid()))
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                                Forms\Components\Select::make('category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('vendor')
                                    ->label('Vendor / Pihak Kedua')
                                    ->maxLength(255)
                                    ->placeholder('e.g., PT. Jaya Abadi, Toko Atk'),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Jumlah (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                                Forms\Components\DatePicker::make('date')
                                    ->label('Tanggal Transaksi')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\Select::make('type')
                                    ->label('Jenis Aliran')
                                    ->options([
                                        'expense' => 'Pengeluaran (Expense)',
                                        'income' => 'Pemasukan (Income)',
                                        'transfer' => 'Transfer Internal',
                                    ])
                                    ->required(),

                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi / Keperluan')
                                    ->columnSpanFull()
                                    ->required(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status & Dokumen Pendukung')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status Transaksi')
                                    ->options([
                                        'pending' => 'Pending (Menunggu)',
                                        'approved' => 'Approved (Disetujui)',
                                        'rejected' => 'Rejected (Ditolak)',
                                    ])
                                    ->default('pending')
                                    ->required(),

                                // Input ID Pembuat Tersembunyi Otomatis
                                Forms\Components\Hidden::make('created_by_id')
                                    ->default(fn () => Auth::id()),
                            ]),

                        Forms\Components\Section::make('Bukti Fisik / Nota')
                            ->schema([
                                Forms\Components\FileUpload::make('attachments')
                                    ->label('Upload File Nota/Invoice')
                                    ->multiple()
                                    ->directory('transaction-docs')
                                    ->preserveFilenames()
                                    ->maxSize(5120), // Max 5MB
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_number')
                    ->label('No. Transaksi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),

                Tables\Columns\TextColumn::make('vendor')
                    ->label('Vendor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'expense' => 'Expense',
                        'income' => 'Income',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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