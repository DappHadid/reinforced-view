<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'REINFORCED')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/vis-network@9.1.9/standalone/umd/vis-network.min.js"></script>

    <style>
        .vis-network:focus { outline: none; }
    </style>

    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    <div class="flex min-h-screen">
        {{-- ============ SIDEBAR ============ --}}
        <aside class="hidden lg:flex lg:w-72 lg:flex-col lg:fixed lg:inset-y-0 border-r border-slate-200 bg-white">
            <div class="flex items-center gap-3 px-6 h-20 border-b border-slate-200">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white font-extrabold text-lg shadow-lg shadow-blue-600/20">
                    R
                </div>
                <div>
                    <p class="text-base font-extrabold tracking-tight text-slate-900 leading-none">REINFORCED</p>
                    <p class="text-[11px] text-slate-400 mt-1">Research Collaborator Rec.</p>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Menu</p>

                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>'],
                        ['route' => 'rekomendasi', 'label' => 'Cari Rekomendasi', 'icon' => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>'],
                        ['route' => 'dosen.index', 'label' => 'Profil Dosen', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
                        ['route' => 'evaluasi', 'label' => 'Hasil Evaluasi', 'icon' => '<path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>'],
                        ['route' => 'jaringan', 'label' => 'Visualisasi Jaringan', 'icon' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>'],
                        ['route' => 'tentang', 'label' => 'Tentang', 'icon' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @php $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-colors {{ $isActive ? 'bg-blue-50 text-blue-700' : 'font-medium text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach

                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 mt-6 mb-2">Sumber Data</p>
                <div class="px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        Data Dummy &middot; Mode Demo
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                        Neo4j Graph DB (simulasi)
                    </div>
                </div>
            </nav>

            <div class="px-4 py-5 border-t border-slate-200">
                <div class="rounded-xl bg-gradient-to-br from-slate-900 to-slate-700 p-4 text-white">
                    <p class="text-xs font-semibold text-slate-300">Status</p>
                    <p class="text-sm font-bold mt-0.5">Data Dummy Aktif</p>
                </div>
            </div>
        </aside>

        {{-- ============ MAIN ============ --}}
        <div class="flex-1 lg:pl-72">
            <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-slate-200 bg-white/80 backdrop-blur px-4 sm:px-8 h-20">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">@yield('page-title', 'REINFORCED')</h1>
                    <p class="text-sm text-slate-400">@yield('page-subtitle', '')</p>
                </div>
                @hasSection('header-actions')
                    <div>@yield('header-actions')</div>
                @endif
            </header>

            <main class="px-4 sm:px-8 py-8 space-y-8">
                @if(session('status'))
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <p class="text-sm font-semibold text-emerald-700">{{ session('status') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
