<x-filament-panels::page>
    <div class="space-y-6">
        
        {{-- Header Panel Deskripsi Aturan --}}
        <div class="p-6 bg-gray-900 border border-gray-800 rounded-xl">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-white">Threshold Settings</h2>
                @if(($criticalCount + $warningCount) > 0)
                    <span class="px-2 py-0.5 text-xs font-semibold bg-red-500/20 text-red-400 rounded-md border border-red-500/30">
                        {{ $criticalCount + $warningCount }} pelanggaran
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-400 mt-2">
                Atur aturan batas anggaran atau batas stok untuk kategori. Threshold adalah ruang utama untuk membatasi pemasukan/pengeluaran jika diperlukan.
            </p>
        </div>

        {{-- Section 1: Daftar Threshold Rules Yang Terdaftar --}}
        <div class="p-6 bg-gray-900 border border-gray-800 rounded-xl space-y-4">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-list-bullet" class="h-4 w-4" />
                Threshold Rules
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($rules as $rule)
                    <div class="p-4 bg-gray-950 border border-gray-800 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-500/10 rounded-lg border border-purple-500/20">
                                <x-filament::icon icon="heroicon-o-squares-2x2" class="h-5 w-5 text-purple-400" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-white">{{ $rule->category->name }}</h4>
                                <div class="flex items-center gap-3 text-xs text-gray-400 mt-1">
                                    <span class="flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Kritis: <strong>{{ $rule->min_stock_critical }}</strong>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Warning: <strong>{{ $rule->min_stock_warning }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $rule->auto_reorder ? '🤖 Auto-Reorder On' : '' }}
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada aturan threshold yang dikonfigurasi.</p>
                @endforelse
            </div>
        </div>

        {{-- Section 2: Log Pelanggaran Real-time (Pelanggaran Threshold) --}}
        <div class="p-6 bg-gray-900 border border-gray-800 rounded-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-4 w-4 text-red-400" />
                    Pelanggaran Threshold <span class="ml-1 px-1.5 py-0.2 bg-red-500/10 text-red-400 text-xs rounded-full">{{ count($violations) }}</span>
                </h3>
                <div class="flex items-center gap-3 text-xs">
                    <span class="text-gray-400">🔴 {{ $criticalCount }} kritis</span>
                    <span class="text-gray-400">🟡 {{ $warningCount }} warning</span>
                </div>
            </div>

            <div class="space-y-2">
                @forelse($violations as $violation)
                    <div class="p-4 bg-gray-950 border border-gray-800 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                            <div>
                                <h4 class="text-sm font-bold text-white">{{ $violation['item_name'] }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $violation['category'] }} • Stok: <span class="text-red-400 font-bold">{{ $violation['current_stock'] }}</span> / Min: {{ $violation['limit'] }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <span class="px-2.5 py-0.5 text-[10px] font-black bg-red-500/10 text-red-500 rounded border border-red-500/20 uppercase tracking-wider">
                                {{ $violation['type'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-sm text-gray-500">
                        🎉 Hebat! Tidak ada pelanggaran stok saat ini. Semua inventaris berada di atas batas aman.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-filament-panels::page>