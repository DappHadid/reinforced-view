<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'REINFORCED')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/vis-network@9.1.9/standalone/umd/vis-network.min.js"></script>

    <style>
        .vis-network:focus { outline: none; }

        /* Desktop Sidebar Collapsed State */
        @media (min-width: 1024px) {
            html.sidebar-collapsed #sidebar {
                transform: translateX(-100%) !important;
            }
            html.sidebar-collapsed #main-wrapper {
                padding-left: 0 !important;
            }
            html.sidebar-collapsed #toggle-icon-sidebar {
                transform: rotate(180deg);
            }
        }

        /* Mobile Sidebar Open State */
        @media (max-width: 1023px) {
            html.sidebar-mobile-open {
                overflow: hidden;
            }
            html.sidebar-mobile-open #sidebar {
                transform: translateX(0) !important;
            }
            html.sidebar-mobile-open #sidebar-backdrop {
                opacity: 1 !important;
                pointer-events: auto !important;
            }
        /* Floating toggle button when navbar is hidden */
        .dashboard-floating-toggle {
            display: flex;
        }
        @media (min-width: 1024px) {
            .dashboard-floating-toggle {
                display: none;
            }
            html.sidebar-collapsed .dashboard-floating-toggle {
                display: flex;
            }
        }
    </style>

    {{-- Instant state restoration to prevent layout flicker --}}
    <script>
        (function() {
            try {
                if (localStorage.getItem('reinforced_sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>

    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    {{-- ============ BACKDROP OVERLAY (Mobile) ============ --}}
    <div id="sidebar-backdrop"
         class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden">
    </div>

    <div class="flex min-h-screen">
        {{-- ============ SIDEBAR ============ --}}
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-white border-r border-slate-200/90 shadow-xl lg:shadow-none transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
            
            {{-- Brand / Logo Header (Clean seamless height without awkward border-b) --}}
            <div class="flex items-center justify-between px-5 h-20 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group min-w-0">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-primary-600 text-white font-extrabold text-lg shadow-lg shadow-primary-600/20 group-hover:scale-105 transition-transform">
                        R
                    </div>
                    <div class="min-w-0">
                        <p class="text-base font-extrabold tracking-tight text-slate-900 leading-none group-hover:text-primary-600 transition-colors">REINFORCED</p>
                        <p class="text-xs font-normal text-slate-400 mt-1 truncate">Research Collaborator Rec.</p>
                    </div>
                </a>

                {{-- Close / Collapse Button inside sidebar --}}
                <button id="sidebar-close-btn"
                        type="button"
                        aria-label="Tutup Sidebar"
                        title="Tutup Sidebar"
                        class="p-2 -mr-1 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                        <line x1="9" x2="9" y1="3" y2="21"/>
                        <path d="m14 9-3 3 3 3"/>
                    </svg>
                </button>
            </div>

            {{-- Navigation Menu --}}
            <nav class="flex-1 px-4 py-3 space-y-1 overflow-y-auto">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Menu</p>

                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>'],
                        ['route' => 'rekomendasi', 'label' => 'Cari Rekomendasi', 'icon' => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>'],
                        ['route' => 'dosen.index', 'label' => 'Profil Dosen', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
                        ['route' => 'evaluasi', 'label' => 'Hasil Evaluasi', 'icon' => '<path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @php 
                        $baseRoute = explode('.', $item['route'])[0];
                        $isActive = request()->routeIs($baseRoute) || request()->routeIs($baseRoute . '.*');
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-base font-semibold transition-colors {{ $isActive ? 'bg-primary-50 text-primary-700' : 'font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- ============ MAIN CONTENT WRAPPER ============ --}}
        @php
            $hideHeader = View::hasSection('hide-header') || request()->routeIs(
                'dashboard',
                'rekomendasi',
                'rekomendasi.*',
                'dosen.index',
                'dosen.show',
                'evaluasi',
            );
        @endphp

        <div id="main-wrapper" class="flex-1 flex flex-col min-w-0 transition-[padding] duration-300 ease-in-out lg:pl-72">
            @if(!$hideHeader)
            <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-slate-200/90 bg-white/85 backdrop-blur-md px-4 sm:px-8 h-20 shrink-0">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    {{-- Sidebar Toggle Button --}}
                    <button id="sidebar-toggle-btn"
                            type="button"
                            aria-label="Toggle Sidebar"
                            title="Buka / Tutup Sidebar"
                            class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 shadow-xs transition-all active:scale-95 focus:outline-none shrink-0">
                        <svg id="toggle-icon-sidebar" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <line x1="9" x2="9" y1="3" y2="21"/>
                            <path d="m14 9-3 3 3 3"/>
                        </svg>
                    </button>

                    <div class="min-w-0">
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight truncate">@yield('page-title', 'REINFORCED')</h1>
                        <p class="text-base font-normal text-slate-400 truncate">@yield('page-subtitle', '')</p>
                    </div>
                </div>

                @hasSection('header-actions')
                    <div class="shrink-0">@yield('header-actions')</div>
                @endif
            </header>
            @else
            {{-- Floating Toggle Button when Navbar is Hidden (Dashboard) --}}
            <div class="dashboard-floating-toggle fixed top-5 left-5 z-30">
                <button id="sidebar-toggle-btn"
                        type="button"
                        aria-label="Toggle Sidebar"
                        title="Buka / Tutup Sidebar"
                        class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200/90 bg-white/95 backdrop-blur-md text-slate-700 hover:text-slate-900 hover:bg-white shadow-md transition-all active:scale-95 focus:outline-none">
                    <svg id="toggle-icon-sidebar" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                        <line x1="9" x2="9" y1="3" y2="21"/>
                        <path d="m14 9-3 3 3 3"/>
                    </svg>
                </button>
            </div>
            @endif

            <main class="flex-1 px-4 sm:px-8 {{ $hideHeader ? 'pt-16 sm:pt-8' : 'py-8' }} pb-8 space-y-8">
                @if(session('status'))
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <p class="text-base font-semibold text-emerald-700">{{ session('status') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>

            {{-- Footer & Attribution --}}
            <footer class="mt-auto px-4 sm:px-8 py-4 border-t border-slate-200/80 bg-white/50 text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} REINFORCED &middot; Research Collaborator Recommendation</p>
                <p>
                    <a href="https://storyset.com/business" target="_blank" rel="noopener noreferrer" class="hover:text-primary-600 transition-colors underline underline-offset-2">Business illustrations by Storyset</a>
                </p>
            </footer>
        </div>
    </div>

    {{-- Sidebar Toggle & State Management Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const closeBtn = document.getElementById('sidebar-close-btn');
            const backdrop = document.getElementById('sidebar-backdrop');
            const html = document.documentElement;

            function isDesktop() {
                return window.innerWidth >= 1024;
            }

            function updateToggleTooltip() {
                if (!toggleBtn) return;
                const isCollapsed = html.classList.contains('sidebar-collapsed');
                const isMobileOpen = html.classList.contains('sidebar-mobile-open');
                if (isDesktop()) {
                    toggleBtn.setAttribute('title', isCollapsed ? 'Buka Sidebar' : 'Tutup Sidebar');
                } else {
                    toggleBtn.setAttribute('title', isMobileOpen ? 'Tutup Sidebar' : 'Buka Sidebar');
                }
            }

            function toggleSidebar() {
                if (isDesktop()) {
                    const willCollapse = !html.classList.contains('sidebar-collapsed');
                    html.classList.toggle('sidebar-collapsed', willCollapse);
                    try {
                        localStorage.setItem('reinforced_sidebar_collapsed', willCollapse ? 'true' : 'false');
                    } catch (e) {}
                } else {
                    html.classList.toggle('sidebar-mobile-open');
                }
                updateToggleTooltip();
                setTimeout(() => {
                    window.dispatchEvent(new Event('resize'));
                }, 310);
            }

            function closeSidebar() {
                if (isDesktop()) {
                    html.classList.add('sidebar-collapsed');
                    try {
                        localStorage.setItem('reinforced_sidebar_collapsed', 'true');
                    } catch (e) {}
                } else {
                    html.classList.remove('sidebar-mobile-open');
                }
                updateToggleTooltip();
                setTimeout(() => {
                    window.dispatchEvent(new Event('resize'));
                }, 310);
            }

            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && html.classList.contains('sidebar-mobile-open')) {
                    closeSidebar();
                }
            });

            window.addEventListener('resize', () => {
                if (isDesktop() && html.classList.contains('sidebar-mobile-open')) {
                    html.classList.remove('sidebar-mobile-open');
                }
                updateToggleTooltip();
            });

            updateToggleTooltip();
        });
    </script>

    @stack('modals')
    @stack('scripts')
</body>
</html>
