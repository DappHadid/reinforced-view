@extends('layouts.app')

@section('title', $dosen['hasName'].' | REINFORCED')
@section('page-title', 'Profil Peneliti')
@section('page-subtitle', $dosen['hasName'])

@section('header-actions')
    <a href="{{ route('rekomendasi', ['name' => $dosen['hasName']]) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition-colors">
        Cari Rekomendasi untuk Dosen Ini
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
@endsection

@section('content')

    <div class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('dosen.index') }}" class="hover:text-slate-700 font-medium">Profil Dosen</a>
        <span>/</span>
        <span class="text-slate-600">{{ $dosen['hasName'] }}</span>
    </div>

    {{-- ============ PROFILE HEADER ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="h-16 w-16 shrink-0 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white text-xl font-extrabold">
                {{ strtoupper(substr($dosen['hasName'], 0, 1)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-extrabold text-slate-900">{{ $dosen['hasName'] }}</h2>
                <p class="text-sm text-slate-400 mt-0.5">SINTA ID {{ $dosen['hasSintaID'] }} &middot; {{ $dosen['hasDepartment'] }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="text-[11px] font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">Usia Akademik: {{ $dosen['hasAcademicAge'] }} tahun</span>
                    <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">{{ $dosen['ns0__hasCollaborator'] }} Kolaborator</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ STAT GRID ============ --}}
    <section class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">H-Index</p>
            <p class="mt-2 text-sm text-slate-700">Scholar <span class="font-bold text-slate-900">{{ $dosen['ns0__hasHIndexScholar'] }}</span></p>
            <p class="text-sm text-slate-700">Scopus <span class="font-bold text-slate-900">{{ $dosen['ns0__hasHIndexScopus'] }}</span></p>
            <p class="text-sm text-slate-700">WoS <span class="font-bold text-slate-900">{{ $dosen['ns0__hasHIndexWos'] }}</span></p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Publikasi</p>
            <p class="mt-2 text-sm text-slate-700">Scholar <span class="font-bold text-slate-900">{{ $dosen['ns0__hasPublicationScholar'] }}</span></p>
            <p class="text-sm text-slate-700">Scopus <span class="font-bold text-slate-900">{{ $dosen['ns0__hasPublicationScopus'] }}</span></p>
            <p class="text-sm text-slate-700">WoS <span class="font-bold text-slate-900">{{ $dosen['ns0__hasPublicationWos'] }}</span></p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Rata-rata Sitasi</p>
            <p class="mt-2 text-sm text-slate-700">Scholar <span class="font-bold text-slate-900">{{ $dosen['ns0__hasAverageCitationScholar'] }}</span></p>
            <p class="text-sm text-slate-700">Scopus <span class="font-bold text-slate-900">{{ $dosen['ns0__hasAverageCitationScopus'] }}</span></p>
            <p class="text-sm text-slate-700">WoS <span class="font-bold text-slate-900">{{ $dosen['ns0__hasAverageCitationWos'] }}</span></p>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        {{-- ============ MINI GRAPH ============ --}}
        <section class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Mini-Graph Kolaborasi</h2>
                <p class="text-xs text-slate-400">Jaringan sekitar {{ $dosen['hasName'] }}</p>
            </div>
            <div class="relative flex-1 min-h-[360px] bg-slate-900">
                <div id="network-graph" class="absolute inset-0"></div>
            </div>
        </section>

        {{-- ============ PUBLICATIONS ============ --}}
        <section class="xl:col-span-3 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Daftar Publikasi</h2>
                <p class="text-xs text-slate-400">{{ count($publikasi) }} publikasi tercatat</p>
            </div>
            <div class="divide-y divide-slate-100 max-h-[420px] overflow-y-auto">
                @forelse($publikasi as $pub)
                    <div class="px-6 py-3.5">
                        <p class="text-sm font-medium text-slate-800">{{ $pub['judul'] }}</p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-slate-400">
                            <span>{{ $pub['tahun'] }}</span>
                            <span>&middot;</span>
                            <span>DOI: {{ $pub['doi'] }}</span>
                            <span>&middot;</span>
                            <span class="font-semibold text-slate-500">{{ $pub['sumber'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-sm text-slate-400">Belum ada publikasi tercatat.</div>
                @endforelse
            </div>
        </section>
    </div>

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
            font: { color: '#e2e8f0', size: 12, face: 'Inter' },
            shape: n.group === 'target' ? 'star' : 'dot',
            size: n.group === 'target' ? 20 : (n.group === 'recommendation' ? 14 : 10),
            borderWidth: 2,
        })));

        const edges = new vis.DataSet(graphData.edges.map(e => ({
            from: e.from,
            to: e.to,
            arrows: 'to',
            color: { color: e.label === 'recommended' ? '#ff9800' : '#475569', opacity: 0.8 },
            width: e.label === 'recommended' ? 2 : 1,
            smooth: { type: 'continuous' },
        })));

        const network = new vis.Network(container, { nodes, edges }, {
            autoResize: true,
            height: '100%',
            width: '100%',
            physics: {
                barnesHut: { gravitationalConstant: -10000, springLength: 120, springConstant: 0.04 },
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
