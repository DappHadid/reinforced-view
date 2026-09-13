@extends('layouts.app')

@section('title', $dosen['hasName'].' | REINFORCED')
@section('page-title', 'Profil Peneliti')
@section('page-subtitle', $dosen['hasName'])

@section('content')

    {{-- ============ BREADCRUMB ============ --}}
    <nav class="flex items-center gap-2 text-xs text-slate-400 mb-1">
        <a href="{{ route('dosen.index') }}" class="hover:text-slate-700 transition-colors font-medium">Profil Dosen</a>
        <span>/</span>
        <span class="text-slate-500 font-medium">{{ $dosen['fakultas'] ?? 'Fakultas' }}</span>
        <span>/</span>
        <span class="text-slate-800 font-semibold truncate">{{ $dosen['hasName'] }}</span>
    </nav>

    {{-- ============ PROFILE HEADER CARD ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 p-6 sm:p-7 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            
            {{-- Left: Avatar + Details --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 min-w-0 flex-1">
                {{-- Profile Avatar --}}
                <div class="relative shrink-0">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl border border-slate-200 shadow-xs overflow-hidden bg-slate-100 relative">
                        @if(!empty($dosen['avatar_image']))
                            <img src="{{ asset($dosen['avatar_image']) }}" 
                                 alt="{{ $dosen['hasName'] }}"
                                 class="w-full h-full object-cover object-center"
                                 onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        @endif
                        <div class="{{ !empty($dosen['avatar_image']) ? 'hidden' : '' }} w-full h-full bg-slate-800 flex items-center justify-center text-white text-2xl font-bold tracking-wider">
                            {{ $dosen['initials'] ?? strtoupper(substr($dosen['hasName'], 0, 2)) }}
                        </div>
                    </div>
                </div>

                {{-- Name & Metadata --}}
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                            {{ $dosen['hasName'] }}
                        </h1>
                        <button onclick="copySintaId('{{ $dosen['hasSintaID'] }}')" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-[11px] font-medium text-slate-600 transition-colors cursor-pointer"
                                title="Salin SINTA ID">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                            <span>SINTA ID {{ $dosen['hasSintaID'] }}</span>
                        </button>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                        {{ $dosen['hasDepartment'] }} &bull; {{ $dosen['fakultas'] ?? 'Fakultas Ilmu Komputer' }}
                    </p>

                    {{-- Simple clean tags row --}}
                    <div class="flex flex-wrap items-center gap-3 mt-3 text-xs text-slate-600">
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            Usia Akademik: {{ $dosen['hasAcademicAge'] }} tahun
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            {{ $dosen['ns0__hasCollaborator'] }} Kolaborator
                        </span>
                        @if(!empty($dosen['topik_utama']))
                            <span class="inline-flex items-center gap-1.5 font-medium text-primary-700 bg-primary-50 border border-primary-100 px-2.5 py-1 rounded-md">
                                Fokus: {{ $dosen['topik_utama'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-2.5 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-100">
                <button onclick="copyProfileLink()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition-colors shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    <span>Salin Link</span>
                </button>
                <a href="{{ route('rekomendasi', ['name' => $dosen['hasName']]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-xs font-semibold text-white transition-colors shadow-xs">
                    <span>Cari Rekomendasi</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>

        </div>
    </section>



    {{-- ============ GRAPH & TOP REKOMENDASI FOCUS SECTION ============ --}}
    <section class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">Analisis Jaringan & Top Rekomendasi Kolaborator</h2>
                <p class="text-xs text-slate-500">Visualisasi graf hubungan kolaborasi dan hasil rekomendasi kandidat teratas untuk {{ $dosen['hasName'] }}</p>
            </div>
            <a href="{{ route('rekomendasi', ['name' => $dosen['hasName']]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700">
                <span>Lihat Analisis Rekomendasi Lengkap</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- MINI GRAPH CANVAS (7 Cols) --}}
            <div class="lg:col-span-7 rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden flex flex-col min-h-[540px]">
                {{-- Graph Header --}}
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-xl bg-primary-50 text-primary-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Graph Jaringan Kolaborasi</h3>
                            <p class="text-[11px] text-slate-400">Interaktif &bull; Klik node untuk melihat kandidat</p>
                        </div>
                    </div>

                    {{-- Graph Toolbar --}}
                    <div class="flex items-center gap-1">
                        <button id="graph-zoom-in" title="Perbesar" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <button id="graph-zoom-out" title="Perkecil" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <button id="graph-reset" title="Fokus Ulang" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Canvas Light Container --}}
                <div class="relative flex-1 min-h-[460px] bg-slate-50 overflow-hidden">
                    <div id="network-graph" class="absolute inset-0 z-10"></div>

                    {{-- Legend Footer Overlay --}}
                    <div class="absolute bottom-3 left-3 right-3 z-20 flex flex-wrap items-center justify-center gap-4 px-3 py-2 rounded-xl bg-white/95 backdrop-blur-sm border border-slate-200 text-[10px] font-medium text-slate-600 shadow-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary-600"></span>
                            <span>{{ $dosen['hasName'] }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>Rekomendasi Kolaborator</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span>Kolaborator Eksisting</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOP REKOMENDASI LIST CARDS (5 Cols) --}}
            <div class="lg:col-span-5 rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden flex flex-col min-h-[540px]">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Top Rekomendasi Kolaborator</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Kandidat terbaik hasil analisis algoritma</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ count($rekomendasi ?? []) }} Kandidat
                    </span>
                </div>

                <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[520px]">
                    @forelse($rekomendasi ?? [] as $index => $rek)
                        @php
                            $meta = $rek['meta'] ?? [];
                            $skorPercent = round(($rek['Skor Kemiripan'] ?? 0.8) * 100, 1);
                            $sintaIdRek = $rek['Rekomendasi_SINTA_ID'] ?? '6000000';
                            $stat = $rek['Detail_Statistik'] ?? [];
                        @endphp
                        <div id="rekom-card-{{ $sintaIdRek }}" class="p-4 hover:bg-slate-50 transition-colors group">
                            <div class="flex items-start gap-3">
                                {{-- Rank & Avatar --}}
                                <div class="relative shrink-0">
                                    <div class="w-12 h-12 rounded-xl border border-slate-200 overflow-hidden bg-slate-100 relative">
                                        @if(!empty($meta['avatar_image']))
                                            <img src="{{ asset($meta['avatar_image']) }}" 
                                                 alt="{{ $rek['Rekomendasi_Nama'] }}" 
                                                 class="w-full h-full object-cover object-center"
                                                 onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                        @endif
                                        <div class="{{ !empty($meta['avatar_image']) ? 'hidden' : '' }} w-full h-full bg-slate-800 flex items-center justify-center text-white text-xs font-bold">
                                            {{ $meta['initials'] ?? strtoupper(substr($rek['Rekomendasi_Nama'], 0, 2)) }}
                                        </div>
                                    </div>
                                    <span class="absolute -top-1.5 -left-1.5 w-5 h-5 rounded-full bg-slate-900 text-white text-[10px] font-bold flex items-center justify-center shadow-xs">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <a href="{{ route('dosen.show', ['sintaId' => $sintaIdRek]) }}" class="text-xs font-bold text-slate-800 group-hover:text-primary-600 transition-colors truncate">
                                            {{ $rek['Rekomendasi_Nama'] }}
                                        </a>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                                            {{ $skorPercent }}% Match
                                        </span>
                                    </div>

                                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                        {{ $meta['prodi'] ?? 'Program Studi' }} &bull; {{ $meta['fakultas'] ?? 'Fakultas' }}
                                    </p>

                                    <div class="flex flex-wrap items-center gap-2 mt-2 text-[10px] text-slate-500">
                                        <span class="font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                            H-Index: {{ $stat['ns0__hasHIndexScholar'] ?? 5 }}
                                        </span>
                                        <span class="font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                            {{ $stat['ns0__hasPublicationScholar'] ?? 20 }} Karya
                                        </span>
                                        @if(!empty($meta['topik_utama']))
                                            <span class="font-medium text-primary-700 bg-primary-50 px-2 py-0.5 rounded truncate max-w-[140px]">
                                                {{ $meta['topik_utama'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400">Belum ada rekomendasi tercatat.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </section>

    {{-- ============ DAFTAR PUBLIKASI (FULL WIDTH SECTION) ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-xs overflow-hidden flex flex-col">
        
        {{-- Header & Search Bar --}}
        <div class="px-6 py-4 border-b border-slate-100 bg-white">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Publikasi Peneliti</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        <span id="pub-total-badge" class="font-semibold text-slate-700">{{ count($publikasi) }}</span> publikasi ilmiah tercatat
                    </p>
                </div>

                {{-- Live Search input --}}
                <div class="relative w-full sm:w-72">
                    <input type="text" 
                           id="pub-search-input" 
                           placeholder="Cari judul, DOI, atau topik..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:outline-none focus:border-primary-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
            </div>

            {{-- Clean Filter Buttons --}}
            <div class="flex flex-wrap items-center gap-1.5 mt-3 pt-3 border-t border-slate-100 text-[11px]" id="source-filter-container">
                <button data-source="ALL" class="pub-filter-btn px-3 py-1 rounded-lg font-semibold bg-slate-900 text-white transition-colors">
                    Semua ({{ count($publikasi) }})
                </button>
                @php
                    $sources = ['Scopus', 'Google Scholar', 'Web of Science', 'SINTA'];
                @endphp
                @foreach($sources as $src)
                    @php
                        $cnt = count(array_filter($publikasi, fn($p) => strcasecmp($p['sumber'], $src) === 0));
                    @endphp
                    @if($cnt > 0)
                        <button data-source="{{ $src }}" class="pub-filter-btn px-3 py-1 rounded-lg font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                            {{ $src }} ({{ $cnt }})
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Publications Items Container --}}
        <div id="pub-list-container" class="divide-y divide-slate-100 min-h-[360px] flex-1">
            {{-- Dynamic via JavaScript --}}
        </div>

        {{-- Clean Pagination Footer (10 items per page) --}}
        <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 shrink-0">
            <div>
                Menampilkan <span id="pub-range-text" class="font-bold text-slate-800">1-10</span> dari <span id="pub-filtered-count" class="font-bold text-slate-800">{{ count($publikasi) }}</span> publikasi
            </div>

            {{-- Page Buttons --}}
            <div class="flex items-center gap-1" id="pagination-controls">
                {{-- Dynamic via JavaScript --}}
            </div>
        </div>

    </section>

@endsection

@push('scripts')
<script>
    // ============ GLOBAL DATA & STATE ============
    const allPublikasi = @json($publikasi);
    const graphData    = @json($graphData ?? ['nodes' => [], 'edges' => []]);

    let currentPage    = 1;
    const itemsPerPage = 10;
    let filteredPubs   = [...allPublikasi];
    let selectedSource = 'ALL';
    let searchQuery    = '';

    // ============ COPY HELPERS ============
    function copySintaId(sintaId) {
        navigator.clipboard.writeText(sintaId).then(() => {
            showToast(`SINTA ID ${sintaId} berhasil disalin`);
        });
    }

    function copyProfileLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('Link profil berhasil disalin ke clipboard');
        });
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white px-4 py-2.5 rounded-xl shadow-lg text-xs font-medium flex items-center gap-2 transition-all duration-200 opacity-0 translate-y-2';
        toast.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.remove('opacity-0', 'translate-y-2'), 10);
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 250);
        }, 2500);
    }

    // ============ RENDER PUBLIKASI (PAGINATION PER 10) ============
    function renderPublikasiList() {
        const container = document.getElementById('pub-list-container');
        if (!container) return;

        filteredPubs = allPublikasi.filter(pub => {
            const matchesSource = (selectedSource === 'ALL') || (pub.sumber && pub.sumber.toLowerCase() === selectedSource.toLowerCase());
            const q = searchQuery.toLowerCase().trim();
            const matchesSearch = !q || pub.judul.toLowerCase().includes(q) || (pub.doi && pub.doi.toLowerCase().includes(q)) || (pub.topik && pub.topik.toLowerCase().includes(q));
            return matchesSource && matchesSearch;
        });

        const totalItems = filteredPubs.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / itemsPerPage));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex   = Math.min(startIndex + itemsPerPage, totalItems);
        const pageItems  = filteredPubs.slice(startIndex, endIndex);

        document.getElementById('pub-filtered-count').innerText = totalItems;
        document.getElementById('pub-range-text').innerText = totalItems > 0 ? `${startIndex + 1}-${endIndex}` : '0-0';

        if (pageItems.length === 0) {
            container.innerHTML = `
                <div class="px-6 py-12 text-center text-slate-400 text-xs">
                    Tidak ada publikasi yang sesuai dengan kriteria pencarian.
                </div>
            `;
            renderPaginationControls(totalPages);
            return;
        }

        container.innerHTML = pageItems.map(pub => `
            <div class="px-6 py-4 hover:bg-slate-50/80 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
                        ${pub.judul}
                    </h3>
                    ${pub.sumber ? `<span class="inline-flex items-center shrink-0 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">${pub.sumber}</span>` : ''}
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5 text-xs text-slate-400">
                    ${pub.tahun ? `<span>${pub.tahun}</span>` : ''}
                    ${(pub.tahun && pub.sitasi) ? `<span>&bull;</span>` : ''}
                    ${pub.sitasi ? `
                        <span class="text-slate-600 font-medium">
                            ${pub.sitasi} Sitasi
                        </span>
                    ` : ''}
                </div>
            </div>
        `).join('');

        renderPaginationControls(totalPages);
    }

    function renderPaginationControls(totalPages) {
        const controls = document.getElementById('pagination-controls');
        if (!controls) return;

        let html = '';

        // Previous button
        html += `
            <button onclick="changePage(${currentPage - 1})" 
                    ${currentPage === 1 ? 'disabled' : ''} 
                    class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-600 font-medium hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-xs">
                &laquo; Prev
            </button>
        `;

        // Page buttons
        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                html += `
                    <button class="px-3 py-1 rounded-lg bg-slate-900 text-white font-bold text-xs">
                        ${i}
                    </button>
                `;
            } else {
                html += `
                    <button onclick="changePage(${i})" 
                            class="px-3 py-1 rounded-lg border border-slate-200 bg-white text-slate-600 font-medium hover:bg-slate-50 transition-colors text-xs">
                        ${i}
                    </button>
                `;
            }
        }

        // Next button
        html += `
            <button onclick="changePage(${currentPage + 1})" 
                    ${currentPage === totalPages ? 'disabled' : ''} 
                    class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-600 font-medium hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-xs">
                Next &raquo;
            </button>
        `;

        controls.innerHTML = html;
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredPubs.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderPublikasiList();
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderPublikasiList();

        const searchInput = document.getElementById('pub-search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value;
                currentPage = 1;
                renderPublikasiList();
            });
        }

        const filterBtns = document.querySelectorAll('.pub-filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => {
                    b.classList.remove('bg-slate-900', 'text-white');
                    b.classList.add('text-slate-600', 'hover:bg-slate-100');
                });
                btn.classList.remove('text-slate-600', 'hover:bg-slate-100');
                btn.classList.add('bg-slate-900', 'text-white');

                selectedSource = btn.getAttribute('data-source');
                currentPage = 1;
                renderPublikasiList();
            });
        });
    });

    // ============ VIS NETWORK GRAPH (CLEAN SLATE PALETTE) ============
    function groupColor(group) {
        switch (group) {
            case 'target': 
                return { 
                    background: '#2563eb', 
                    border: '#1d4ed8', 
                    highlight: { background: '#1d4ed8', border: '#1e40af' } 
                };
            case 'recommendation': 
                return { 
                    background: '#f59e0b', 
                    border: '#d97706', 
                    highlight: { background: '#d97706', border: '#b45309' } 
                };
            default: 
                return { 
                    background: '#10b981', 
                    border: '#059669', 
                    highlight: { background: '#059669', border: '#047857' } 
                };
        }
    }

    const graphContainer = document.getElementById('network-graph');

    if (graphContainer && graphData.nodes && graphData.nodes.length > 0) {
        const nodes = new vis.DataSet(graphData.nodes.map(n => ({
            id: n.id,
            label: n.label,
            color: groupColor(n.group),
            font: { 
                color: '#1e293b', 
                size: n.group === 'target' ? 12 : 11, 
                face: 'Inter, sans-serif',
                strokeWidth: 2,
                strokeColor: '#ffffff'
            },
            shape: n.group === 'target' ? 'star' : 'dot',
            size: n.group === 'target' ? 20 : (n.group === 'recommendation' ? 14 : 10),
            borderWidth: 2,
        })));

        const edges = new vis.DataSet(graphData.edges.map(e => ({
            from: e.from,
            to: e.to,
            arrows: {
                to: { enabled: true, scaleFactor: 0.6 }
            },
            color: { 
                color: e.label === 'recommended' ? '#f59e0b' : '#94a3b8', 
                highlight: e.label === 'recommended' ? '#d97706' : '#64748b',
                opacity: 0.85
            },
            width: e.label === 'recommended' ? 2 : 1.2,
            dashes: e.label === 'recommended' ? [4, 4] : false,
            smooth: { type: 'continuous' }
        })));

        const network = new vis.Network(graphContainer, { nodes, edges }, {
            autoResize: true,
            height: '100%',
            width: '100%',
            physics: {
                barnesHut: { 
                    gravitationalConstant: -7000, 
                    centralGravity: 0.3,
                    springLength: 100, 
                    springConstant: 0.04 
                },
                stabilization: { iterations: 150 }
            },
            interaction: { 
                hover: true, 
                tooltipDelay: 100,
                zoomView: true,
                dragView: true
            }
        });

        network.once('stabilizationIterationsDone', () => {
            network.fit({ animation: { duration: 500, easingFunction: 'easeInOutQuad' } });
        });

        // Graph node click interaction -> highlight corresponding Top Rekomendasi card
        network.on('click', (params) => {
            if (params.nodes && params.nodes.length > 0) {
                const clickedNodeId = params.nodes[0];
                const nodeObj = graphData.nodes.find(n => n.id === clickedNodeId);
                if (nodeObj && nodeObj.group === 'recommendation') {
                    const matchedCard = document.querySelector(`[id^="rekom-card-"]`);
                    if (matchedCard) {
                        matchedCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
            }
        });

        document.getElementById('graph-zoom-in')?.addEventListener('click', () => {
            const scale = network.getScale();
            network.moveTo({ scale: scale * 1.25, animation: { duration: 200 } });
        });

        document.getElementById('graph-zoom-out')?.addEventListener('click', () => {
            const scale = network.getScale();
            network.moveTo({ scale: scale * 0.75, animation: { duration: 200 } });
        });

        document.getElementById('graph-reset')?.addEventListener('click', () => {
            network.fit({ animation: { duration: 350 } });
        });
    }
</script>
@endpush
