@extends('layouts.app')

@section('title', 'Dashboard | REINFORCED')

@section('content')

    {{-- ============ HERO / WELCOME BANNER CARD ============ --}}
    <section class="relative overflow-hidden rounded-3xl bg-[#FFF3E0] border border-[#FFE0B2] px-6 sm:px-8 md:px-10 py-6 sm:py-8 text-slate-800 shadow-lg shadow-orange-500/5">
        {{-- Decorative background glow circles --}}
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#FFE0B2]/50 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-20 h-52 w-52 rounded-full bg-[#FFCC80]/40 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6 md:gap-4">
            {{-- Left Content --}}
            <div class="max-w-lg lg:max-w-xl xl:max-w-2xl space-y-3 text-center md:text-left py-2 sm:py-4">


                <h2 class="text-2xl sm:text-[28px] lg:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight">
                    REINFORCED
                </h2>

                <p class="text-base text-slate-700 font-normal leading-relaxed">
                    Temukan rekan kolaborasi penelitian yang relevan dan strategis berbasis 
                    <span class="font-semibold text-slate-900">Attributed Network Embedding (ANE)</span>. 
                    Tingkatkan produktivitas publikasi dan luaskan jaringan riset ilmiah Anda.
                </p>

                <div class="pt-2 flex flex-wrap items-center justify-center md:justify-start gap-3">
                    <a href="{{ route('rekomendasi') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4.5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-primary-700 hover:shadow-lg transition-all active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <span>Cari Rekomendasi</span>
                    </a>
                    <a href="{{ route('dosen.index') }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-4.5 py-2.5 text-sm font-semibold text-slate-800 border border-slate-300 hover:bg-slate-50 transition-all shadow-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>Direktori Peneliti</span>
                    </a>
                </div>
            </div>

            {{-- Right Illustration (Storyset Animated SVG) --}}
            <div class="relative shrink-0 flex items-center justify-center w-full md:w-auto md:-my-4 md:-mr-2">
                <div class="w-60 sm:w-72 md:w-80 lg:w-[360px] max-w-full drop-shadow-xl flex items-center justify-center md:justify-end">
                    <img src="{{ asset('images/research-paper-animate.svg') }}" 
                         alt="Research Paper Illustration by Storyset" 
                         class="w-full h-auto object-contain max-h-60 sm:max-h-72 md:max-h-[290px] lg:max-h-[320px] hover:scale-[1.02] transition-transform duration-300">
                </div>
            </div>
        </div>
    </section>

    {{-- ============ STAT CARDS ============ --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        {{-- Card 1: Peneliti Terdaftar --}}
        <div class="group rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all flex items-center justify-between gap-3 overflow-hidden min-h-[140px]">
            <div class="min-w-0 flex-1 z-10">
                <p class="text-[32px] sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-none">{{ count($dosenList) }}</p>
                <p class="text-xs font-normal text-slate-500 mt-2">Peneliti Terdaftar</p>
            </div>
            <div class="shrink-0 w-28 h-28 sm:w-36 sm:h-36 md:w-40 md:h-40 -my-4 -mr-2 flex items-center justify-center">
                <img src="{{ asset('images/Partnership-rafiki.svg') }}" 
                     alt="Ilustrasi Peneliti Terdaftar" 
                     class="w-full h-full object-contain scale-115 sm:scale-120 group-hover:scale-125 transition-transform duration-300">
            </div>
        </div>

        {{-- Card 2: Total Publikasi --}}
        <div class="group rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all flex items-center justify-between gap-3 overflow-hidden min-h-[140px]">
            <div class="min-w-0 flex-1 z-10">
                <p class="text-[32px] sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-none">{{ number_format($totalPublikasi ?? 0) }}</p>
                <p class="text-xs font-normal text-slate-500 mt-2">Total Publikasi</p>
            </div>
            <div class="shrink-0 w-28 h-28 sm:w-36 sm:h-36 md:w-40 md:h-40 -my-4 -mr-2 flex items-center justify-center">
                <img src="{{ asset('images/Online document-rafiki.svg') }}" 
                     alt="Ilustrasi Total Publikasi" 
                     class="w-full h-full object-contain scale-115 sm:scale-120 group-hover:scale-125 transition-transform duration-300">
            </div>
        </div>

        {{-- Card 3: Relasi Kolaborasi --}}
        <div class="group rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all flex items-center justify-between gap-3 overflow-hidden min-h-[140px]">
            <div class="min-w-0 flex-1 z-10">
                <p class="text-[32px] sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-none">{{ number_format($totalRelasi ?? 0) }}</p>
                <p class="text-xs font-normal text-slate-500 mt-2">Relasi Kolaborasi</p>
            </div>
            <div class="shrink-0 w-28 h-28 sm:w-36 sm:h-36 md:w-40 md:h-40 -my-4 -mr-2 flex items-center justify-center">
                <img src="{{ asset('images/Connected world-rafiki.svg') }}" 
                     alt="Ilustrasi Relasi Kolaborasi" 
                     class="w-full h-full object-contain scale-115 sm:scale-120 group-hover:scale-125 transition-transform duration-300">
            </div>
        </div>

        {{-- Card 4: Program Studi --}}
        <div class="group rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all flex items-center justify-between gap-3 overflow-hidden min-h-[140px]">
            <div class="min-w-0 flex-1 z-10">
                <p class="text-[32px] sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-none">{{ count($departemenList) }}</p>
                <p class="text-xs font-normal text-slate-500 mt-2">Program Studi</p>
            </div>
            <div class="shrink-0 w-28 h-28 sm:w-36 sm:h-36 md:w-40 md:h-40 -my-4 -mr-2 flex items-center justify-center">
                <img src="{{ asset('images/college campus-amico.svg') }}"
                     alt="Ilustrasi Program Studi" 
                     class="w-full h-full object-contain scale-115 sm:scale-120 group-hover:scale-125 transition-transform duration-300">
            </div>
        </div>
    </section>

    {{-- ============ TENTANG REINFORCED ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-xs">
        <div class="flex items-start justify-between gap-6 flex-wrap">
            <div class="flex-1 min-w-[300px]">
                <h2 class="text-base font-semibold text-slate-900">Apa itu REINFORCED?</h2>
                <p class="text-base text-slate-500 mt-2 leading-relaxed">
                    REINFORCED adalah sistem rekomendasi kolaborator penelitian yang dibangun di atas
                    <span class="font-semibold text-slate-700">Attributed Network Embedding (ANE)</span> menggunakan
                    <span class="font-semibold text-slate-700">knowledge graph</span> (Neo4j) berbasis ontologi
                    <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">ns0__Person</code> dan
                    <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">ns0__Publication</code>. Sistem ini
                    membantu dosen/peneliti menemukan kandidat kolaborator berdasarkan struktur jaringan kolaborasi
                    dan kemiripan topik publikasi.
                </p>
            </div>
            <a href="{{ route('rekomendasi') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary-600/25 transition-colors whitespace-nowrap">
                Cari Rekomendasi
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-base font-semibold text-primary-700 uppercase tracking-wide">Mode Standar</p>
                <p class="text-base text-slate-500 mt-1">Rekomendasi berbasis H-Index &amp; struktur graf kolaborasi.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-base font-semibold text-primary-700 uppercase tracking-wide">Cascading Hybrid</p>
                <p class="text-base text-slate-500 mt-1">Menambahkan prioritas kemiripan topik via S-BERT (semantic similarity judul publikasi).</p>
            </div>
        </div>
    </section>

    {{-- ============ OVERALL VISUALISASI JARINGAN (Replacing Quick Preview & Directory) ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col">
        {{-- Section Header & Department Filter Form --}}
        <div class="p-6 border-b border-slate-100 bg-white">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-xl bg-primary-50 text-primary-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900 tracking-tight">Overall Visualisasi Jaringan Kolaborasi</h2>
                            <p class="text-xs font-normal text-slate-400 mt-0.5">
                                {{ count($graphData['nodes'] ?? []) }} peneliti terdaftar &bull; {{ count($graphData['edges'] ?? []) }} relasi kolaborasi ilmiah
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Department Filter Form & Controls --}}
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    @php
                        $deptOptions = [['value' => '', 'label' => 'Semua Program Studi']];
                        foreach($departemenList as $dept) {
                            $deptOptions[] = ['value' => $dept, 'label' => $dept];
                        }
                    @endphp
                    <x-dropdown-select 
                        name="departemen" 
                        :options="$deptOptions" 
                        selected="{{ $selectedDepartemen }}" 
                        placeholder="Semua Program Studi" 
                        autoSubmit="true" 
                        extraClasses="min-w-[220px]" 
                    />

                    {{-- Toolbar Zoom Controls --}}
                    <div class="flex items-center gap-1 shrink-0 self-end sm:self-auto">
                        <button type="button" id="graph-zoom-in" title="Perbesar Graf" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <button type="button" id="graph-zoom-out" title="Perkecil Graf" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <button type="button" id="graph-reset" title="Fokus Ulang" class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Overall Graph Canvas Container --}}
        <div class="relative flex-1 min-h-[580px] bg-white overflow-hidden">
            <div id="network-graph" class="absolute inset-0 z-10"></div>
            
            @if(empty($graphData['nodes']))
                <div class="absolute inset-0 flex items-center justify-center text-slate-400 text-xs">
                    Tidak ada data peneliti untuk departemen ini.
                </div>
            @endif

            {{-- Graph Hint & Legend Footer Overlay --}}
            <div class="absolute bottom-4 left-4 right-4 z-20 flex flex-col sm:flex-row items-center justify-between gap-3 p-3 rounded-2xl bg-white/95 backdrop-blur-sm border border-slate-200 text-xs font-normal text-slate-600 shadow-xs">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <span class="text-xs font-normal">Klik simpul peneliti untuk melihat ringkasan profil, lalu lanjutkan ke halaman detail bila diperlukan.</span>
                </div>

                <div class="flex items-center gap-3 text-xs font-normal shrink-0">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Peneliti Terdaftar</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-primary-600"></span>Relasi Terhubung</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Floating Node Info Popup --}}
    <div id="node-popup" class="fixed z-50 hidden pointer-events-none" style="min-width:220px;max-width:280px;">
        <div class="pointer-events-auto relative bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden" style="box-shadow:0 8px 32px rgba(0,0,0,0.13),0 1.5px 6px rgba(0,0,0,0.07);">
            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-white border-b border-slate-100">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div id="np-initials" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-xs font-bold text-emerald-700"></div>
                    <div class="min-w-0">
                        <p id="np-name" class="text-sm font-semibold text-slate-900 truncate leading-tight"></p>
                        <p class="text-xs text-slate-400 font-normal">Peneliti REINFORCED</p>
                    </div>
                </div>
                <button type="button" id="np-close" class="shrink-0 rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            {{-- Stats --}}
            <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
                <div class="flex flex-col items-center justify-center py-3 px-1">
                    <p id="np-hindex" class="text-lg font-bold text-emerald-600 leading-none">-</p>
                    <p class="text-[10px] font-normal text-slate-400 mt-1 text-center">H-Index</p>
                </div>
                <div class="flex flex-col items-center justify-center py-3 px-1">
                    <p id="np-pub" class="text-lg font-bold text-slate-800 leading-none">-</p>
                    <p class="text-[10px] font-normal text-slate-400 mt-1 text-center">Publikasi</p>
                </div>
                <div class="flex flex-col items-center justify-center py-3 px-1">
                    <p id="np-collab" class="text-lg font-bold text-slate-800 leading-none">-</p>
                    <p class="text-[10px] font-normal text-slate-400 mt-1 text-center">Kolaborator</p>
                </div>
            </div>
            {{-- Department --}}
            <div class="px-4 py-2.5">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-0.5">Program Studi</p>
                <p id="np-dept" class="text-xs font-medium text-slate-700 leading-snug">-</p>
            </div>
            {{-- Action --}}
            <div class="px-4 pb-3">
                <a id="np-link" href="#" class="flex items-center justify-center gap-1.5 w-full rounded-xl bg-primary-600 hover:bg-primary-700 px-3 py-2 text-xs font-semibold text-white transition-colors">
                    Lihat Profil
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const graphData = @json($graphData ?? ['nodes' => [], 'edges' => []]);
    const dosenBaseUrl = @json(url('/dosen'));

    const graphContainer = document.getElementById('network-graph');

    // ── Floating Node Popup helpers ──────────────────────────────────────────
    const nodePopup   = document.getElementById('node-popup');
    const npName      = document.getElementById('np-name');
    const npInitials  = document.getElementById('np-initials');
    const npHindex    = document.getElementById('np-hindex');
    const npPub       = document.getElementById('np-pub');
    const npCollab    = document.getElementById('np-collab');
    const npDept      = document.getElementById('np-dept');
    const npLink      = document.getElementById('np-link');
    const npClose     = document.getElementById('np-close');

    function showNodePopup(node, canvasX, canvasY) {
        // Populate data
        const name     = node.label || 'Peneliti REINFORCED';
        const initials = name.split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
        npInitials.textContent = initials;
        npName.textContent     = name;
        npHindex.textContent   = (node.h_index != null && node.h_index !== undefined) ? node.h_index : '-';
        npPub.textContent      = (node.publication_count != null) ? node.publication_count : '-';
        npCollab.textContent   = (node.collaborator_count != null) ? node.collaborator_count : '-';
        npDept.textContent     = node.department || '-';
        npLink.href            = node.sintaId ? `${dosenBaseUrl}/${node.sintaId}` : '#';

        // Show first (hidden but rendered) to get dimensions
        nodePopup.classList.remove('hidden');
        nodePopup.style.visibility = 'hidden';
        nodePopup.style.transition = 'none';

        const rect   = graphContainer.getBoundingClientRect();
        const pw     = nodePopup.offsetWidth  || 260;
        const ph     = nodePopup.offsetHeight || 180;
        const margin = 12;

        // canvasX/Y are relative to the vis-network canvas element (same as container)
        let left = rect.left + canvasX + margin;
        let top  = rect.top  + canvasY - ph / 2;

        // Clamp so popup stays inside viewport
        if (left + pw > window.innerWidth  - margin) left = rect.left + canvasX - pw - margin;
        if (left < margin)                            left = margin;
        if (top  < margin)                            top  = margin;
        if (top  + ph > window.innerHeight - margin)  top  = window.innerHeight - ph - margin;

        nodePopup.style.left       = left + 'px';
        nodePopup.style.top        = top  + 'px';
        nodePopup.style.visibility = 'visible';
        nodePopup.style.opacity    = '0';
        nodePopup.style.transform  = 'scale(0.95)';
        nodePopup.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
        requestAnimationFrame(() => {
            nodePopup.style.opacity   = '1';
            nodePopup.style.transform = 'scale(1)';
        });
    }

    function hideNodePopup() {
        nodePopup.style.opacity   = '0';
        nodePopup.style.transform = 'scale(0.95)';
        setTimeout(() => nodePopup.classList.add('hidden'), 180);
    }

    npClose?.addEventListener('click', hideNodePopup);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') hideNodePopup(); });

    // ── Build graph ──────────────────────────────────────────────────────────
    if (graphContainer && graphData.nodes && graphData.nodes.length > 0) {

        // Hitung jumlah kolaborator tiap node dari data edges (tanpa perlu field API)
        const collabCounts = {};
        graphData.edges.forEach(e => {
            collabCounts[e.from] = (collabCounts[e.from] || 0) + 1;
            collabCounts[e.to]   = (collabCounts[e.to]   || 0) + 1;
        });

        const visNodes = new vis.DataSet(graphData.nodes.map(n => ({
            id:                n.id,
            label:             n.label,
            sintaId:           n.sinta_id ?? n.id,
            department:        n.department || '-',
            h_index:           n.h_index           ?? null,
            publication_count: n.publication_count ?? null,
            collaborator_count: n.collaborator_count ?? collabCounts[n.id] ?? null,
            color: {
                background: '#10b981',
                border:     '#059669',
                highlight:  { background: '#34d399', border: '#059669' },
                hover:      { background: '#34d399', border: '#059669' },
            },
            font: {
                color:       '#0f172a',
                size:        11,
                face:        'Inter, sans-serif',
                strokeWidth: 3,
                strokeColor: '#ffffff',
                vadjust:     -2,
            },
            shape:       'dot',
            size:        12,
            borderWidth: 2.5,
            borderWidthSelected: 4,
            shadow: { enabled: true, color: 'rgba(16,185,129,0.22)', size: 8, x: 0, y: 2 },
        })));

        const visEdges = new vis.DataSet(graphData.edges.map(e => ({
            from:   e.from,
            to:     e.to,
            color:  { color: '#cbd5e1', opacity: 0.7, highlight: '#94a3b8' },
            width:  1.2,
            smooth: { type: 'continuous', roundness: 0.2 },
            hoverWidth: 2,
        })));

        const network = new vis.Network(graphContainer, { nodes: visNodes, edges: visEdges }, {
            autoResize: true,
            height: '100%',
            width:  '100%',
            physics: {
                barnesHut: {
                    gravitationalConstant: -9000,
                    centralGravity:        0.22,
                    springLength:          110,
                    springConstant:        0.03,
                    damping:               0.15,
                },
                stabilization: { iterations: 250, updateInterval: 30 },
            },
            interaction: {
                hover:        true,
                zoomView:     true,
                dragView:     true,
                zoomSpeed:    0.4,
                tooltipDelay: 999999, // disable native tooltip
            },
            nodes: { shadow: false },
            edges: { shadow: false },
        });

        network.once('stabilizationIterationsDone', () => {
            network.fit({ animation: { duration: 700, easingFunction: 'easeInOutQuad' } });
        });

        // Node click → show floating popup
        network.on('click', params => {
            if (params.nodes && params.nodes.length > 0) {
                const node = visNodes.get(params.nodes[0]);
                if (node) {
                    const domPos = params.event.center; // {x, y} relative to canvas
                    showNodePopup(node, domPos.x, domPos.y);
                }
            } else {
                hideNodePopup();
            }
        });

        // Drag / zoom → hide popup
        network.on('dragStart', hideNodePopup);
        network.on('zoom',      hideNodePopup);

        // Controls
        document.getElementById('graph-zoom-in')?.addEventListener('click', () => {
            network.moveTo({ scale: network.getScale() * 1.3, animation: { duration: 400, easingFunction: 'easeInOutQuad' } });
        });
        document.getElementById('graph-zoom-out')?.addEventListener('click', () => {
            network.moveTo({ scale: network.getScale() * 0.75, animation: { duration: 400, easingFunction: 'easeInOutQuad' } });
        });
        document.getElementById('graph-reset')?.addEventListener('click', () => {
            network.fit({ animation: { duration: 500, easingFunction: 'easeInOutQuad' } });
        });
    }
</script>
@endpush
