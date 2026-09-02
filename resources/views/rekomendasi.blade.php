@extends('layouts.app')

@section('title', 'Cari Rekomendasi | REINFORCED')
@section('page-title', 'Cari Rekomendasi')
@section('page-subtitle', 'Temukan kandidat kolaborator penelitian berdasarkan jaringan &amp; kemiripan topik')

@section('content')

    {{-- ============ SEARCH FORM ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <h2 class="text-base font-bold text-slate-900">Cari Kolaborator</h2>
        </div>

        <form action="{{ route('rekomendasi') }}" method="GET" class="grid grid-cols-1 md:grid-cols-[1fr_auto_auto] gap-4 items-end">
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
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Metode Algoritma</label>
                <div class="flex rounded-xl border border-slate-200 bg-slate-50 p-1 text-sm font-medium">
                    <label class="cursor-pointer">
                        <input type="radio" name="use_cascading" value="false" class="peer sr-only" {{ !$useCascading ? 'checked' : '' }}>
                        <span class="block px-3 py-1.5 rounded-lg text-slate-500 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-sm transition-all whitespace-nowrap">Standar</span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="use_cascading" value="true" class="peer sr-only" {{ $useCascading ? 'checked' : '' }}>
                        <span class="block px-3 py-1.5 rounded-lg text-slate-500 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-sm transition-all whitespace-nowrap">Cascading Hybrid</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Cari Rekomendasi
            </button>
        </form>
    </section>

    @if($currentName !== '')
        {{-- ============ GRAPH ============ --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Visualisasi Jaringan Rekomendasi</h2>
                    <p class="text-xs text-slate-400">Jalur kolaborasi untuk {{ strtoupper($currentName) }}</p>
                </div>
                <div class="hidden sm:flex items-center gap-4 text-[11px] font-medium text-slate-500">
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#1f77b4]"></span>Target</span>
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#ff9800]"></span>Rekomendasi</span>
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#4caf50]"></span>Penghubung</span>
                </div>
            </div>
            <div class="relative min-h-[420px] bg-slate-900">
                <div id="network-graph" class="absolute inset-0"></div>
            </div>
        </section>

        {{-- ============ RECOMMENDATION TABLE ============ --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Top {{ count($rekomendasi) }} Rekomendasi Kolaborator</h2>
                <p class="text-xs text-slate-400">Untuk {{ strtoupper($currentName) }} &middot; Metode {{ $useCascading ? 'Cascading Hybrid' : 'Standar' }}</p>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($rekomendasi as $i => $r)
                    @php
                        $skor = $r['Skor Kemiripan'] ?? 0;
                        $pct = max(0, min(100, round($skor * 100)));
                        $stat = $r['Detail_Statistik'] ?? [];
                        $pubs = $r['Detail_Publikasi'] ?? [];
                        $modalId = 'pub-modal-'.$i;
                    @endphp
                    <div class="px-6 py-5">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            <div class="flex items-center gap-3 lg:w-64 shrink-0">
                                <div class="h-10 w-10 shrink-0 rounded-full bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white text-sm font-bold">
                                    {{ $i + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $r['Rekomendasi_Nama'] }}</p>
                                    <p class="text-xs text-slate-400">SINTA: {{ $r['Rekomendasi_SINTA_ID'] }}</p>
                                </div>
                            </div>

                            <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="rounded-lg bg-slate-50 py-2 text-center">
                                    <p class="text-sm font-bold text-slate-800">{{ $pct }}%</p>
                                    <p class="text-[10px] text-slate-400">Skor Kemiripan</p>
                                </div>
                                <div class="rounded-lg bg-slate-50 py-2 text-center">
                                    <p class="text-sm font-bold text-slate-800">{{ $stat['ns0__hasHIndexScholar'] ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400">H-Index</p>
                                </div>
                                <div class="rounded-lg bg-slate-50 py-2 text-center">
                                    <p class="text-sm font-bold text-slate-800">{{ $stat['ns0__hasCollaborator'] ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400">Kolaborator</p>
                                </div>
                                <div class="rounded-lg bg-slate-50 py-2 text-center">
                                    <p class="text-sm font-bold text-slate-800">{{ count($pubs) }}</p>
                                    <p class="text-[10px] text-slate-400">Publikasi</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="document.getElementById('{{ $modalId }}').showModal()"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    Detail
                                </button>
                                <button type="button" onclick="document.getElementById('rating-{{ $modalId }}').showModal()"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    Nilai
                                </button>
                            </div>
                        </div>

                        <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-blue-600" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    {{-- ===== Modal: Detail Publikasi ===== --}}
                    <dialog id="{{ $modalId }}" class="rounded-2xl border border-slate-200 shadow-xl p-0 w-full max-w-lg backdrop:bg-slate-900/50">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ $r['Rekomendasi_Nama'] }}</h3>
                                <p class="text-xs text-slate-400">Daftar Publikasi &amp; Perbandingan Atribut</p>
                            </div>
                            <button type="button" onclick="document.getElementById('{{ $modalId }}').close()" class="text-slate-400 hover:text-slate-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <div class="px-6 py-4 max-h-96 overflow-y-auto space-y-4">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 mb-2">Perbandingan Atribut</p>
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div class="rounded-lg bg-slate-50 py-2">
                                        <p class="text-xs font-bold text-slate-800">{{ $stat['ns0__hasHIndexScholar'] ?? '-' }}/{{ $stat['ns0__hasHIndexScopus'] ?? '-' }}/{{ $stat['ns0__hasHIndexWos'] ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400">H-Index (Sch/Scp/WoS)</p>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 py-2">
                                        <p class="text-xs font-bold text-slate-800">{{ $stat['ns0__hasPublicationScholar'] ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400">Total Publikasi</p>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 py-2">
                                        <p class="text-xs font-bold text-slate-800">{{ $stat['ns0__hasDepartment'] ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-400">Departemen</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-500 mb-2">Judul Publikasi ({{ count($pubs) }})</p>
                                <ul class="space-y-1.5">
                                    @foreach($pubs as $judul)
                                        <li class="text-xs text-slate-600 flex items-start gap-2">
                                            <span class="text-slate-300 mt-0.5">&bull;</span>
                                            <span>{{ $judul }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="px-6 py-3 border-t border-slate-100 text-right">
                            <button type="button" onclick="document.getElementById('{{ $modalId }}').close()" class="rounded-lg px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-50">Tutup</button>
                        </div>
                    </dialog>

                    {{-- ===== Modal: Form Penilaian ===== --}}
                    <dialog id="rating-{{ $modalId }}" class="rounded-2xl border border-slate-200 shadow-xl p-0 w-full max-w-md backdrop:bg-slate-900/50">
                        <form action="{{ route('rekomendasi.penilaian') }}" method="POST">
                            @csrf
                            <input type="hidden" name="rekomendasi_sinta_id" value="{{ $r['Rekomendasi_SINTA_ID'] }}">
                            <input type="hidden" name="rekomendasi_nama" value="{{ $r['Rekomendasi_Nama'] }}">
                            <input type="hidden" name="name" value="{{ $currentName }}">
                            <input type="hidden" name="use_cascading" value="{{ $useCascading ? 'true' : 'false' }}">

                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Nilai Rekomendasi</h3>
                                    <p class="text-xs text-slate-400">{{ $r['Rekomendasi_Nama'] }}</p>
                                </div>
                                <button type="button" onclick="document.getElementById('rating-{{ $modalId }}').close()" class="text-slate-400 hover:text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>

                            <div class="px-6 py-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-2">Rating Kualitas Rekomendasi</label>
                                    <div class="flex items-center gap-2">
                                        @for($star = 1; $star <= 5; $star++)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $star }}" class="peer sr-only" {{ $star === 5 ? 'checked' : '' }} required>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-200 peer-checked:text-amber-400 hover:text-amber-300 transition-colors" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Komentar (opsional)</label>
                                    <textarea name="komentar" rows="3" placeholder="Tulis catatan mengenai relevansi rekomendasi ini..."
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none transition"></textarea>
                                </div>
                            </div>

                            <div class="px-6 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                                <button type="button" onclick="document.getElementById('rating-{{ $modalId }}').close()" class="rounded-lg px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-50">Batal</button>
                                <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition-colors">Simpan Penilaian</button>
                            </div>
                        </form>
                    </dialog>
                @empty
                    <div class="px-6 py-10 text-center text-sm text-slate-400">
                        Tidak ditemukan rekomendasi untuk peneliti ini.
                    </div>
                @endforelse
            </div>
        </section>
    @else
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-10 text-center">
            <p class="text-sm text-slate-400">Pilih peneliti target di atas untuk melihat rekomendasi kolaborator.</p>
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
