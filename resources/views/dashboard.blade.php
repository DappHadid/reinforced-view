@extends('layouts.app')

@section('title', 'Dashboard | REINFORCED')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan jaringan kolaborasi & rekomendasi peneliti')

@section('content')

    {{-- ============ STAT CARDS ============ --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="h-11 w-11 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5.5 w-5.5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Aktif</span>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ count($dosenList) }}</p>
            <p class="text-sm text-slate-400 mt-1">Peneliti Terdaftar</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="h-11 w-11 rounded-xl bg-violet-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5.5 w-5.5 text-violet-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $totalRekomendasiDicari }}</p>
            <p class="text-sm text-slate-400 mt-1">Rekomendasi Pernah Dicari</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="h-11 w-11 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5.5 w-5.5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ number_format($evaluasiTerakhir['precision_at_5'] * 100, 1) }}%</p>
            <p class="text-sm text-slate-400 mt-1">Precision@5 ({{ $evaluasiTerakhir['metode'] }})</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="h-11 w-11 rounded-xl bg-rose-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5.5 w-5.5 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="3" x2="6" y2="15"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ number_format($evaluasiTerakhir['map_at_5'] * 100, 1) }}%</p>
            <p class="text-sm text-slate-400 mt-1">MAP@5 ({{ $evaluasiTerakhir['metode'] }})</p>
        </div>
    </section>

    {{-- ============ TENTANG REINFORCED (statis) ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="max-w-2xl">
                <h2 class="text-base font-bold text-slate-900">Apa itu REINFORCED?</h2>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    REINFORCED adalah sistem rekomendasi kolaborator penelitian yang dibangun di atas
                    <span class="font-semibold text-slate-700">Attributed Network Embedding (ANE)</span> menggunakan
                    <span class="font-semibold text-slate-700">knowledge graph</span> (Neo4j) berbasis ontologi
                    <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">ns0__Person</code> dan
                    <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">ns0__Publication</code>. Sistem ini
                    membantu dosen/peneliti menemukan kandidat kolaborator berdasarkan struktur jaringan kolaborasi
                    dan kemiripan topik publikasi.
                </p>
            </div>
            <a href="{{ route('rekomendasi') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition-colors whitespace-nowrap">
                Cari Rekomendasi
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold text-blue-700 uppercase tracking-wide">Mode Standar</p>
                <p class="text-sm text-slate-500 mt-1">Rekomendasi berbasis H-Index &amp; struktur graf kolaborasi.</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold text-violet-700 uppercase tracking-wide">Cascading Hybrid</p>
                <p class="text-sm text-slate-500 mt-1">Menambahkan prioritas kemiripan topik via S-BERT (semantic similarity judul publikasi).</p>
            </div>
        </div>
    </section>

    {{-- ============ SEARCH / FORM ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <h2 class="text-base font-bold text-slate-900">Preview Cepat Rekomendasi</h2>
        </div>

        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-[1fr_auto_auto] gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Nama Peneliti Target</label>
                <select name="name" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none transition">
                    <option value="">— Pilih Peneliti —</option>
                    @foreach($dosenList as $dosen)
                        <option value="{{ $dosen['nama'] }}" {{ strcasecmp($currentName, $dosen['nama']) === 0 ? 'selected' : '' }}>
                            {{ $dosen['nama'] }} &middot; SINTA {{ $dosen['sinta_id'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Metode</label>
                <div class="flex rounded-xl border border-slate-200 bg-slate-50 p-1 text-sm font-medium">
                    <label class="cursor-pointer">
                        <input type="radio" name="use_cascading" value="false" class="peer sr-only" {{ !$useCascading ? 'checked' : '' }}>
                        <span class="block px-3 py-1.5 rounded-lg text-slate-500 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-sm transition-all whitespace-nowrap">Standard</span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="use_cascading" value="true" class="peer sr-only" {{ $useCascading ? 'checked' : '' }}>
                        <span class="block px-3 py-1.5 rounded-lg text-slate-500 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-sm transition-all whitespace-nowrap">Hybrid</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Temukan
            </button>
        </form>
    </section>

    @if($currentName !== '')
        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
            {{-- ============ GRAPH ============ --}}
            <section class="xl:col-span-3 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Visualisasi Jaringan</h2>
                        <p class="text-xs text-slate-400">Jalur kolaborasi menuju rekomendasi untuk {{ strtoupper($currentName) }}</p>
                    </div>
                    <div class="hidden sm:flex items-center gap-4 text-[11px] font-medium text-slate-500">
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#1f77b4]"></span>Target</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#ff9800]"></span>Rekomendasi</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#4caf50]"></span>Penghubung</span>
                    </div>
                </div>
                <div class="relative flex-1 min-h-[420px] bg-slate-900">
                    <div id="network-graph" class="absolute inset-0"></div>
                    @if(empty($graphData['nodes']))
                        <div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">
                            Tidak ada data graf untuk ditampilkan.
                        </div>
                    @endif
                </div>
            </section>

            {{-- ============ RECOMMENDATION LIST ============ --}}
            <section class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Top Rekomendasi</h2>
                        <p class="text-xs text-slate-400">Untuk {{ strtoupper($currentName) }}</p>
                    </div>
                    <a href="{{ route('rekomendasi', ['name' => $currentName, 'use_cascading' => $useCascading ? 'true' : 'false']) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                        Lihat detail &rarr;
                    </a>
                </div>

                <div class="flex-1 overflow-y-auto max-h-[420px] divide-y divide-slate-100">
                    @forelse($rekomendasi as $i => $r)
                        @php
                            $skor = $r['Skor Kemiripan'] ?? 0;
                            $pct = max(0, min(100, round($skor * 100)));
                            $stat = $r['Detail_Statistik'] ?? [];
                            $pubs = $r['Detail_Publikasi'] ?? [];
                        @endphp
                        <div class="px-6 py-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="h-9 w-9 shrink-0 rounded-full bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ $i + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $r['Rekomendasi_Nama'] }}</p>
                                        <span class="shrink-0 text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">{{ $pct }}%</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">SINTA: {{ $r['Rekomendasi_SINTA_ID'] }} &middot; {{ count($pubs) }} publikasi</p>

                                    <div class="mt-2 h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-blue-600" style="width: {{ $pct }}%"></div>
                                    </div>

                                    @if(!empty($stat))
                                        <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                                            <div class="rounded-lg bg-slate-50 py-1.5">
                                                <p class="text-xs font-bold text-slate-800">{{ $stat['ns0__hasHIndexScholar'] ?? '-' }}</p>
                                                <p class="text-[10px] text-slate-400">H-Index</p>
                                            </div>
                                            <div class="rounded-lg bg-slate-50 py-1.5">
                                                <p class="text-xs font-bold text-slate-800">{{ $stat['ns0__hasCollaborator'] ?? '-' }}</p>
                                                <p class="text-[10px] text-slate-400">Kolaborator</p>
                                            </div>
                                            <div class="rounded-lg bg-slate-50 py-1.5">
                                                <p class="text-xs font-bold text-slate-800">{{ $stat['ns0__hasPublicationScholar'] ?? '-' }}</p>
                                                <p class="text-[10px] text-slate-400">Publikasi</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-slate-400">
                            Tidak ditemukan rekomendasi untuk peneliti ini.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    @else
        {{-- ============ EMPTY / DIRECTORY STATE ============ --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Direktori Peneliti</h2>
                    <p class="text-xs text-slate-400">Pilih salah satu peneliti di atas untuk melihat rekomendasi</p>
                </div>
                <span class="text-xs font-semibold text-slate-400">{{ count($dosenList) }} total</span>
            </div>
            <div class="overflow-x-auto max-h-[520px] overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0">
                        <tr class="bg-slate-50 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Nama Peneliti</th>
                            <th class="px-6 py-3">SINTA ID</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dosenList as $i => $dosen)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3 text-slate-400">{{ $i + 1 }}</td>
                                <td class="px-6 py-3 font-medium text-slate-800">{{ $dosen['nama'] }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ $dosen['sinta_id'] }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('dashboard', ['name' => $dosen['nama'], 'use_cascading' => $useCascading ? 'true' : 'false']) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800">
                                        Lihat Rekomendasi
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada data peneliti.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

@endsection

@push('scripts')
<script>
    const graphData = @json($graphData ?? ['nodes' => [], 'edges' => []]);

    function groupColor(group) {
        switch (group) {
            case 'target': return { background: '#1f77b4', border: '#155a8a', highlight: { background: '#3b93d6', border: '#155a8a' } };
            case 'recommendation': return { background: '#ff9800', border: '#c26f00', highlight: { background: '#ffb04d', border: '#c26f00' } };
            default: return { background: '#4caf50', border: '#357a38', highlight: { background: '#6fc873', border: '#357a38' } };
        }
    }

    const container = document.getElementById('network-graph');

    if (container && graphData.nodes && graphData.nodes.length > 0) {
        const nodes = new vis.DataSet(graphData.nodes.map(n => ({
            id: n.id,
            label: n.label,
            color: groupColor(n.group),
            font: { color: '#e2e8f0', size: 13, face: 'Inter' },
            shape: n.group === 'target' ? 'star' : 'dot',
            size: n.group === 'target' ? 22 : (n.group === 'recommendation' ? 16 : 12),
            borderWidth: 2,
        })));

        const edges = new vis.DataSet(graphData.edges.map(e => ({
            from: e.from,
            to: e.to,
            label: e.label,
            arrows: 'to',
            color: { color: e.label === 'recommended' ? '#ff9800' : '#475569', opacity: 0.8 },
            font: { color: '#94a3b8', size: 9, strokeWidth: 0, align: 'middle' },
            width: e.label === 'recommended' ? 2.5 : 1,
            smooth: { type: 'continuous' },
        })));

        const network = new vis.Network(container, { nodes, edges }, {
            autoResize: true,
            height: '100%',
            width: '100%',
            physics: {
                barnesHut: { gravitationalConstant: -12000, springLength: 140, springConstant: 0.04 },
                stabilization: { iterations: 150 },
            },
            interaction: { hover: true, tooltipDelay: 100 },
            nodes: { shadow: true },
            edges: { shadow: false },
        });

        network.once('stabilizationIterationsDone', () => network.fit({ animation: { duration: 500, easingFunction: 'easeInOutQuad' } }));
    }
</script>
@endpush
