@extends('layouts.app')

@section('title', 'Visualisasi Jaringan | REINFORCED')
@section('page-title', 'Visualisasi Jaringan Kolaborasi')
@section('page-subtitle', 'Graf kolaborasi seluruh peneliti dalam sistem')

@section('content')

    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <form action="{{ route('jaringan') }}" method="GET" class="flex flex-col sm:flex-row gap-4 sm:items-end">
            <div class="flex-1 max-w-xs">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Filter Departemen</label>
                <select name="departemen" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none transition">
                    <option value="">Semua Departemen</option>
                    @foreach($departemenList as $dept)
                        <option value="{{ $dept }}" {{ $selectedDepartemen === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-xs text-slate-400">Klik salah satu node untuk membuka profil peneliti tersebut.</p>
        </form>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Graf Kolaborasi</h2>
                <p class="text-xs text-slate-400">{{ count($graphData['nodes']) }} peneliti &middot; {{ count($graphData['edges']) }} relasi kolaborasi</p>
            </div>
        </div>
        <div class="relative min-h-[600px] bg-slate-900">
            <div id="network-graph" class="absolute inset-0"></div>
            @if(empty($graphData['nodes']))
                <div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">
                    Tidak ada data untuk filter ini.
                </div>
            @endif
        </div>
    </section>

@endsection

@push('scripts')
<script>
    const graphData = @json($graphData ?? ['nodes' => [], 'edges' => []]);
    const dosenBaseUrl = @json(url('/dosen'));

    const container = document.getElementById('network-graph');

    if (container && graphData.nodes && graphData.nodes.length > 0) {
        const nodes = new vis.DataSet(graphData.nodes.map(n => ({
            id: n.id,
            label: n.label,
            sintaId: n.sinta_id,
            title: n.department,
            color: { background: '#4caf50', border: '#357a38', highlight: { background: '#6fc873', border: '#357a38' } },
            font: { color: '#e2e8f0', size: 12, face: 'Inter' },
            shape: 'dot',
            size: 12,
            borderWidth: 2,
        })));

        const edges = new vis.DataSet(graphData.edges.map(e => ({
            from: e.from,
            to: e.to,
            color: { color: '#475569', opacity: 0.6 },
            width: 1,
            smooth: { type: 'continuous' },
        })));

        const network = new vis.Network(container, { nodes, edges }, {
            autoResize: true,
            height: '100%',
            width: '100%',
            physics: {
                barnesHut: { gravitationalConstant: -8000, springLength: 100, springConstant: 0.03 },
                stabilization: { iterations: 200 },
            },
            interaction: { hover: true, tooltipDelay: 100 },
            nodes: { shadow: true },
            edges: { shadow: false },
        });

        network.once('stabilizationIterationsDone', () => network.fit({ animation: { duration: 500, easingFunction: 'easeInOutQuad' } }));

        network.on('click', (params) => {
            if (params.nodes.length > 0) {
                const node = nodes.get(params.nodes[0]);
                if (node && node.sintaId) {
                    window.location.href = `${dosenBaseUrl}/${node.sintaId}`;
                }
            }
        });
    }
</script>
@endpush
