<x-filament-panels::page>
    <form wire:submit.prevent="processMerge" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" size="lg" color="primary" icon="heroicon-m-cpu-chip">
                Mulai Proses & Gabungkan Data (Merge)
            </x-filament::button>
        </div>
    </form>

    {{-- Kolom Custom Preview Spreadsheet Simulator untuk Prototype --}}
    <div class="mt-8 p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            <x-filament::icon icon="heroicon-o-table-cells" class="h-5 w-5 text-gray-400" />
            Live Preview Spreadsheet Editor (Simulasi Skema Drizzle Mapper)
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data mentah di bawah ini akan di-parsing otomatis ke dalam kolom kategori & vendor.</p>
        
        <div class="mt-4 overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-800">
            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3">Nama Berkas Sumber</th>
                        <th class="px-4 py-3">Deteksi Kolom Mentah</th>
                        <th class="px-4 py-3">Status Mapping</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">vendor_invoice_logistik.csv</td>
                        <td class="px-4 py-3"><code class="text-xs bg-gray-100 dark:bg-gray-800 p-1 rounded">[No, Tgl, Nama_Suplier, Total_Biaya]</code></td>
                        <td class="px-4 py-3"><span class="inline-flex items-center gap-1 text-xs font-medium text-green-600 dark:text-green-400">● Ready to Auto-Match</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>