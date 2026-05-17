<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Dwi Budgeting') }} - Warehouse System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#16161a',
                            panel: '#22252a',
                            accent: '#7f5af0',
                            headline: '#fffffe',
                            paragraph: '#94a1b2',
                            green: '#2cb67d',
                            red: '#ff5555',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-brand-dark font-sans text-brand-paragraph antialiased selection:bg-brand-accent selection:text-white">

    <nav class="sticky top-0 z-50 bg-brand-dark/80 backdrop-blur-md border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 bg-brand-accent rounded-lg flex items-center justify-center text-white font-black text-lg shadow-lg shadow-brand-accent/30">
                        DW
                    </div>
                    <span class="text-brand-headline font-bold text-lg tracking-tight">Dwi Budgeting <span class="text-brand-accent">Warehouse</span></span>
                </div>
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/admin') }}" class="px-4 py-2 text-sm font-semibold text-brand-headline bg-brand-accent hover:bg-brand-accent/90 rounded-lg transition-all duration-200 shadow-md shadow-brand-accent/20">
                                Masuk Dashboard
                            </a>
                        @else
                            <a href="{{ url('/admin/login') }}" class="text-sm font-medium text-brand-headline hover:text-brand-accent transition-colors duration-200">
                                Sign In
                            </a>
                            <a href="{{ url('/admin/login') }}" class="px-4 py-2 text-sm font-semibold text-brand-headline bg-brand-accent hover:bg-brand-accent/90 rounded-lg transition-all duration-200 shadow-md shadow-brand-accent/20">
                                Gateway Akses
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <header class="relative overflow-hidden py-24 lg:py-32 border-b border-gray-800/50">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(127,90,240,0.12),transparent_45%)]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-accent/10 text-brand-accent border border-brand-accent/20 uppercase tracking-wider">
                        🚀 Next-Gen Warehouse ERP v3
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-brand-headline tracking-tight leading-none">
                        Kontrol Budget & <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-purple-400">Stok Suku Cadang</span> Real-Time.
                    </h1>
                    <p class="text-base sm:text-lg text-brand-paragraph max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Sistem manajemen pergudangan cerdas terintegrasi untuk melacak ribuan mutasi spare part, monitoring batas kritis stok otomatis, dan asisten pintar bertenaga AI.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                        <a href="{{ url('/admin') }}" class="w-full sm:w-auto px-8 py-4 text-center text-sm font-bold text-brand-headline bg-brand-accent hover:bg-brand-accent/90 rounded-xl transition-all duration-200 shadow-lg shadow-brand-accent/20 hover:-translate-y-0.5 transform">
                            Buka Aplikasi (John Cafe)
                        </a>
                        <a href="#fitur" class="w-full sm:w-auto px-8 py-4 text-center text-sm font-bold text-brand-headline bg-brand-panel hover:bg-gray-800 rounded-xl border border-gray-800 transition-all duration-200 hover:-translate-y-0.5 transform">
                            Pelajari Fitur
                        </a>
                    </div>
                </div>
                
                <div class="mt-12 lg:mt-0 lg:col-span-5 relative">
                    <div class="bg-brand-panel border border-gray-800 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
                        <div class="flex items-center justify-between border-b border-gray-800 pb-4 mb-6">
                            <div class="flex gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-brand-red/70"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-500/70"></span>
                                <span class="w-3 h-3 rounded-full bg-brand-green/70"></span>
                            </div>
                            <span class="text-xs font-mono text-gray-500">dwi_budgeting_v3.id</span>
                        </div>
                        <div class="space-y-4">
                            <div class="h-24 bg-brand-dark border border-gray-800 rounded-xl p-4 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Suku Cadang Keluar</p>
                                    <p class="text-2xl font-black text-brand-headline mt-1">1,842 <span class="text-xs text-brand-green font-normal">+= Data Real Excel</span></p>
                                </div>
                                <div class="p-3 bg-brand-accent/10 text-brand-accent rounded-lg border border-brand-accent/20">⚙️</div>
                            </div>
                            <div class="h-20 bg-brand-dark/40 border border-gray-800/60 rounded-xl p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-brand-red animate-pulse"></div>
                                    <div>
                                        <p class="text-sm font-bold text-brand-headline">Circuit Breaker 16A</p>
                                        <p class="text-xs text-gray-500">Stok: <span class="text-brand-red font-semibold">66 Unit</span> / Batas Min: 100</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[9px] font-bold bg-brand-red/10 text-brand-red rounded border border-brand-red/20 uppercase">CRITICAL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="fitur" class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
            <h2 class="text-xs font-black uppercase tracking-widest text-brand-accent">Sistem Manajemen Andal</h2>
            <p class="text-3xl sm:text-4xl font-extrabold text-brand-headline tracking-tight">Dirancang Khusus untuk Efisiensi Gudang</p>
            <p class="text-base text-brand-paragraph">Pangkas waktu manajemen data operasional dengan modul otomasi tingkat tinggi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-8 bg-brand-panel border border-gray-800 rounded-2xl hover:border-brand-accent/50 transition-all duration-300 group">
                <div class="h-12 w-12 bg-brand-accent/10 text-brand-accent rounded-xl flex items-center justify-center text-xl mb-6 group-hover:bg-brand-accent group-hover:text-white transition-all duration-300">
                    📂
                </div>
                <h3 class="text-lg font-bold text-brand-headline mb-2">Smart Data Merger</h3>
                <p class="text-sm text-brand-paragraph leading-relaxed">
                    Unggah ribuan data berformat <code class="text-xs bg-brand-dark px-1.5 py-0.5 rounded text-brand-accent">.xlsx</code> atau CSV dari vendor, dan biarkan sistem memetakan skema master database secara otomatis tanpa duplikasi.
                </p>
            </div>

            <div class="p-8 bg-brand-panel border border-gray-800 rounded-2xl hover:border-brand-accent/50 transition-all duration-300 group">
                <div class="h-12 w-12 bg-brand-green/10 text-brand-green rounded-xl flex items-center justify-center text-xl mb-6 group-hover:bg-brand-green group-hover:text-white transition-all duration-300">
                    🤖
                </div>
                <h3 class="text-lg font-bold text-brand-headline mb-2">AI Autofill Engine</h3>
                <p class="text-sm text-brand-paragraph leading-relaxed">
                    Didukung kecerdasan buatan untuk membaca deskripsi keperluan logistik dan otomatis menentukan kategori pengeluaran serta merekomendasikan harga secara instan.
                </p>
            </div>

            <div class="p-8 bg-brand-panel border border-gray-800 rounded-2xl hover:border-brand-accent/50 transition-all duration-300 group">
                <div class="h-12 w-12 bg-brand-red/10 text-brand-red rounded-xl flex items-center justify-center text-xl mb-6 group-hover:bg-brand-red group-hover:text-white transition-all duration-300">
                    🛡️
                </div>
                <h3 class="text-lg font-bold text-brand-headline mb-2">Threshold & Control</h3>
                <p class="text-sm text-brand-paragraph leading-relaxed">
                    Sistem kendali otomatis yang melacak batasan anggaran minimum. Memberikan peringatan bahaya (*Critical/Warning*) real-time serta otomatisasi perintah *Auto Reorder*.
                </p>
            </div>
        </div>
    </section>

    <footer class="border-t border-gray-800/60 bg-brand-panel/40 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <p>&copy; 2026 Dwi Budgeting Warehouse Application. All rights reserved.</p>
            <p>Engineered with Laravel 12 & Filament v3</p>
        </div>
    </footer>

</body>
</html>