<x-filament-panels::page>
    <div class="space-y-6">
        
        {{-- Header Panel --}}
        <div class="p-6 bg-gray-900 border border-gray-800 rounded-xl">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-white">Threshold Settings</h2>
                @if(($criticalCount + $warningCount) > 0)
                    <span class="px-2 py-0.5 text-xs font-semibold bg-red-500/20 text-red-400 rounded-md border border-red-500/30">
                        {{ $criticalCount + $warningCount }} pelanggaran terdeteksi
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-400 mt-2">
                Atur aturan batas anggaran atau batas stok untuk kategori. Threshold adalah ruang utama untuk membatasi pemasukan/pengeluaran jika diperlukan.
            </p>
        </div>

        {{-- Section 1: Daftar Aturan Threshold Master --}}
        <div class="p-6 bg-gray-900 border border-gray-800 rounded-xl space-y-4">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-list-bullet" class="h-4 w-4" />
                Threshold Rules per Kategori Master
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($rules as $rule)
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
                            {{ $rule->auto_reorder ? '🤖 Auto-Reorder On' : '🔒 Manual' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Section 2: Pelanggaran Stok Riil Hasil Sinkronisasi Excel --}}
        <div class="p-6 bg-gray-900 border border-gray-800 rounded-xl space-y-4" x-data="{ activeTab: 'all', searchQuery: '' }">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-gray-800 pb-4">
                <div class="space-y-1">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-4 w-4 text-red-400" />
                        Pelanggaran Batas Stok Aktif
                    </h3>
                    <p class="text-xs text-gray-500">Menampilkan item kritis teratas yang disinkronkan dari data gudang klien.</p>
                </div>
                
                {{-- Navigasi Tab Filter Kontrol --}}
                <div class="flex bg-gray-950 p-1 rounded-lg border border-gray-800 text-xs font-medium self-start lg:self-center">
                    <button @click="activeTab = 'all'" 
                            :class="activeTab === 'all' ? 'bg-purple-600 text-white shadow' : 'text-gray-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-md transition-all duration-150 flex items-center gap-1.5">
                        📦 Semua 
                        <span class="bg-gray-800 px-1.5 py-0.2 rounded-full text-[10px]">{{ count($violations) }}</span>
                    </button>
                    <button @click="activeTab = 'critical'" 
                            :class="activeTab === 'critical' ? 'bg-red-500 text-white shadow' : 'text-gray-400 hover:text-red-400'"
                            class="px-3 py-1.5 rounded-md transition-all duration-150 flex items-center gap-1.5">
                        🔴 Kritis 
                        <span class="bg-red-500/20 text-red-400 px-1.5 py-0.2 rounded-full text-[10px]">{{ $criticalCount }}</span>
                    </button>
                    <button @click="activeTab = 'warning'" 
                            :class="activeTab === 'warning' ? 'bg-yellow-500 text-gray-950 shadow' : 'text-gray-400 hover:text-yellow-400'"
                            class="px-3 py-1.5 rounded-md transition-all duration-150 flex items-center gap-1.5">
                        🟡 Warning 
                        <span class="bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 px-1.5 py-0.2 rounded-full text-[10px]">{{ $warningCount }}</span>
                    </button>
                </div>
            </div>

            {{-- Search Bar Input Live Saringan --}}
            <div class="relative">
                <input x-model="searchQuery" 
                       type="text" 
                       placeholder="🔍 Cari nama barang atau kode rak di sini... (Contoh: Inverter, Shihlin, D2)" 
                       class="w-full pl-4 pr-4 py-2 text-sm bg-gray-950 border border-gray-800 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
            </div>

            {{-- Kotak Log dengan Custom Scrollbar --}}
            <div class="space-y-2 max-h-[350px] overflow-y-auto pr-2 divide-y divide-gray-900/50">
                @foreach($violations as $violation)
                    {{-- Seleksi filter client-side via Alpine.js --}}
                    <div x-show="(activeTab === 'all' || activeTab === '{{ strtolower($violation['type']) }}') && 
                                 ('{{ strtolower($violation['item_name']) }}'.includes(searchQuery.toLowerCase()) || 
                                  '{{ strtolower($violation['item_code']) }}'.includes(searchQuery.toLowerCase()))"
                         class="p-2.5 bg-gray-950/40 border border-gray-800/40 rounded-lg flex items-center justify-between hover:border-gray-700 hover:bg-gray-950 transition-all duration-150">
                        
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $violation['type'] === 'CRITICAL' ? 'bg-red-500 animate-pulse' : 'bg-yellow-500' }}"></div>
                            <div class="truncate">
                                {{-- 🚀 FIX DI SINI: Membaca data item_name dari array baru secara akurat --}}
                                <h4 class="text-xs font-bold text-white tracking-tight truncate max-w-md sm:max-w-xl">{{ $violation['item_name'] }}</h4>
                                <p class="text-[10px] text-gray-500 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    {{-- 🚀 FIX DI SINI: Membaca item_code --}}
                                    <span>Rak: <code class="text-purple-400 font-mono font-bold">{{ $violation['item_code'] }}</code></span>
                                    <span class="text-gray-800">•</span>
                                    <span>Kategori: <span class="text-gray-400">{{ $violation['category'] }}</span></span>
                                    <span class="text-gray-800">•</span>
                                    <span>Stok: <span class="{{ $violation['type'] === 'CRITICAL' ? 'text-red-400' : 'text-yellow-400' }} font-bold">{{ $violation['current_stock'] }} Unit</span></span>
                                    <span class="text-gray-600">(Limit Rule: {{ $violation['limit'] }})</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ml-2">
                            <span class="px-1.5 py-0.2 text-[8px] font-black rounded border uppercase tracking-wider {{ $violation['type'] === 'CRITICAL' ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' }}">
                                {{ $violation['type'] === 'CRITICAL' ? 'CRIT' : 'WARN' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Footer Info --}}
            <div class="text-[11px] text-gray-500 flex justify-between items-center pt-2 border-t border-gray-800/40">
                <span>💡 *Ketik di kolom pencarian untuk menyaring suku cadang secara instan.*</span>
                <span>Total Pelanggaran: <strong class="text-gray-400">{{ count($violations) }} baris data riil</strong></span>
            </div>
        </div>

    </div>
</x-filament-panels::page>