<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kolaborator - {{ $detail['nama'] }}</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Tailwind via CDN for standalone print page --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: { primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' } }
                }
            }
        }
    </script>

    {{-- Vis Network --}}
    <script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>

    <style>
        body {
            background-color: #f8fafc;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .page-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            min-height: 100vh;
        }
        /* Network canvas should have explicit heights */
        .network-graph {
            width: 100%;
            height: 400px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        /* Hide scrollbars during print */
        ::-webkit-scrollbar { display: none; }
        
        @media print {
            body, .page-container {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
            .break-before { page-break-before: always; }
            .avoid-break { page-break-inside: avoid; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="antialiased">

<div class="no-print fixed top-4 right-4 z-50 flex gap-2">
    <button onclick="window.print()" class="px-4 py-2 bg-slate-900 text-white rounded-lg font-semibold text-sm shadow hover:bg-slate-800">
        Cetak PDF Sekarang
    </button>
    <button onclick="window.close()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold text-sm shadow-sm hover:bg-slate-50">
        Tutup
    </button>
</div>

<div class="page-container" id="printable-area">
    
    {{-- ================= HEADER PROFIL ================= --}}
    <header class="flex items-start gap-6 border-b border-slate-200 pb-6 mb-8 avoid-break">
        <div class="w-24 h-24 rounded-2xl bg-slate-100 border-2 border-slate-200 overflow-hidden shrink-0 shadow-sm relative">
            @if(!empty($detail['avatar_image']))
                <img src="{{ asset($detail['avatar_image']) }}" alt="{{ $detail['nama'] }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('images/avatar.jpg') }}';">
            @else
                <img src="{{ asset('images/avatar.jpg') }}" alt="Default Avatar" class="w-full h-full object-cover">
            @endif
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900">{{ $detail['nama'] }}</h1>
                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    SINTA: {{ $detail['sinta_id'] }}
                </span>
            </div>
            <p class="text-slate-500 font-medium mt-1 text-sm">
                {{ $detail['departemen'] }} &bull; {{ $detail['fakultas'] ?? 'Fakultas Ilmu Komputer' }}
            </p>
            <div class="flex items-center gap-4 mt-3 text-sm text-slate-700 font-medium">
                <div class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded border border-slate-100">
                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    {{ $detail['ns0__hasCollaborator'] ?? 0 }} Kolaborator
                </div>
                <div class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded border border-slate-100">
                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    {{ $detail['ns0__hasPublicationScholar'] ?? 0 }} Publikasi
                </div>
            </div>
        </div>
    </header>

    {{-- ================= METODE 1: CASCADING HYBRID ================= --}}
    <section class="mb-10 avoid-break">
        <div class="mb-4">
            <h2 class="text-xl font-bold text-slate-900 border-l-4 border-emerald-500 pl-3">Metode 1: Cascading Hybrid</h2>
            <p class="text-sm text-slate-500 mt-1 pl-4">Menampilkan top 5 rekomendasi beserta jaringan visual berdasarkan kalkulasi berlapis (Cascading).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Graph 1 --}}
            <div class="flex flex-col">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Graph Jaringan</h3>
                <div id="cy-cascading" class="network-graph"></div>
            </div>
            {{-- List 1 --}}
            <div class="flex flex-col">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Top 5 Kolaborator</h3>
                <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-white">
                    @forelse($rekomendasiCascading as $index => $rek)
                        <div class="p-3 flex items-start gap-3">
                            <span class="w-6 h-6 rounded bg-emerald-50 text-emerald-700 text-xs font-bold flex items-center justify-center shrink-0 border border-emerald-100">#{{ $index+1 }}</span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $rek['Rekomendasi_Nama'] }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $rek['meta']['prodi'] ?? 'Program Studi' }} &bull; {{ $rek['meta']['fakultas'] ?? 'Fakultas' }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                        {{ round(($rek['Skor Kemiripan'] ?? 0)*100, 1) }}% Match
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-slate-400">Tidak ada data.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ================= METODE 2: STANDARD ANE ================= --}}
    <section class="mb-10 avoid-break break-before">
        <div class="mb-4">
            <h2 class="text-xl font-bold text-slate-900 border-l-4 border-blue-500 pl-3">Metode 2: Standard ANE</h2>
            <p class="text-sm text-slate-500 mt-1 pl-4">Menampilkan top 5 rekomendasi beserta jaringan visual berdasarkan kalkulasi standar.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Graph 2 --}}
            <div class="flex flex-col">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Graph Jaringan</h3>
                <div id="cy-standard" class="network-graph"></div>
            </div>
            {{-- List 2 --}}
            <div class="flex flex-col">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Top 5 Kolaborator</h3>
                <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-white">
                    @forelse($rekomendasiStandar as $index => $rek)
                        <div class="p-3 flex items-start gap-3">
                            <span class="w-6 h-6 rounded bg-blue-50 text-blue-700 text-xs font-bold flex items-center justify-center shrink-0 border border-blue-100">#{{ $index+1 }}</span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $rek['Rekomendasi_Nama'] }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $rek['meta']['prodi'] ?? 'Program Studi' }} &bull; {{ $rek['meta']['fakultas'] ?? 'Fakultas' }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800">
                                        {{ round(($rek['Skor Kemiripan'] ?? 0)*100, 1) }}% Match
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-slate-400">Tidak ada data.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ================= EVALUASI PENGGUNA ================= --}}
    @if($evaluasiTarget && !empty($evaluasiTarget['rekomendasi']))
    @php
        $cascadingReviews = collect($evaluasiTarget['rekomendasi'])->filter(fn($r) => ($r['metode'] ?? 'Cascading Hybrid') === 'Cascading Hybrid');
        $standardReviews = collect($evaluasiTarget['rekomendasi'])->filter(fn($r) => ($r['metode'] ?? '') !== 'Cascading Hybrid');
    @endphp
    
    <section class="mb-10 avoid-break break-before">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900 border-l-4 border-amber-500 pl-3">Riwayat Penilaian Pengguna</h2>
            <p class="text-sm text-slate-500 mt-1 pl-4">Daftar ulasan dari pengguna lain terhadap {{ $detail['nama'] }}, dipisahkan berdasarkan algoritma.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Review Cascading Hybrid --}}
            <div class="flex flex-col gap-4">
                <h3 class="text-base font-bold text-slate-800 bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg border border-emerald-100 mb-1">Metode Cascading Hybrid</h3>
                @forelse($cascadingReviews as $rev)
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm avoid-break">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset($rev['avatar'] ?? 'images/avatar.jpg') }}" class="w-10 h-10 rounded-lg object-cover bg-slate-100 border border-slate-200 shrink-0" alt="{{ $rev['nama'] }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 leading-tight">{{ $rev['nama'] }}</p>
                                <div class="mt-1 flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= ($rev['score'] ?? 0) ? 'text-amber-400' : 'text-slate-200' }}" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    @endfor
                                    <span class="ml-1 text-xs font-bold text-amber-600">{{ number_format($rev['score'] ?? 0, 1) }}</span>
                                </div>
                                <div class="mt-3 bg-slate-50 rounded-lg p-3 border border-slate-100 text-sm text-slate-600 italic">
                                    "{{ $rev['komentar'] ?: 'Tidak ada komentar.' }}"
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-400 p-4 text-center border border-dashed rounded-xl">Belum ada review untuk model ini.</div>
                @endforelse
            </div>

            {{-- Review Standard ANE --}}
            <div class="flex flex-col gap-4">
                <h3 class="text-base font-bold text-slate-800 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg border border-blue-100 mb-1">Metode Standard ANE</h3>
                @forelse($standardReviews as $rev)
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm avoid-break">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset($rev['avatar'] ?? 'images/avatar.jpg') }}" class="w-10 h-10 rounded-lg object-cover bg-slate-100 border border-slate-200 shrink-0" alt="{{ $rev['nama'] }}">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 leading-tight">{{ $rev['nama'] }}</p>
                                <div class="mt-1 flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= ($rev['score'] ?? 0) ? 'text-amber-400' : 'text-slate-200' }}" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    @endfor
                                    <span class="ml-1 text-xs font-bold text-amber-600">{{ number_format($rev['score'] ?? 0, 1) }}</span>
                                </div>
                                <div class="mt-3 bg-slate-50 rounded-lg p-3 border border-slate-100 text-sm text-slate-600 italic">
                                    "{{ $rev['komentar'] ?: 'Tidak ada komentar.' }}"
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-slate-400 p-4 text-center border border-dashed rounded-xl">Belum ada review untuk model ini.</div>
                @endforelse
            </div>
        </div>
    </section>
    @endif

</div>

<script>
    const targetName = @json($detail['nama']);
    const graphCas = @json($graphCascading);
    const graphStd = @json($graphStandar);

    function groupColor(group) {
        switch (group) {
            case 'target':
                return { background: '#2563eb', border: '#1d4ed8' };
            case 'recommendation':
                return { background: '#f59e0b', border: '#d97706' };
            default:
                return { background: '#10b981', border: '#059669' };
        }
    }

    function initNetwork(containerId, data) {
        if (!data || !data.nodes || data.nodes.length === 0) return null;

        const visNodes = new vis.DataSet(data.nodes.map(n => ({
            id:          n.id,
            label:       n.label,
            group:       n.group,
            color:       groupColor(n.group),
            font: {
                color:       '#0f172a',
                size:        n.group === 'target' ? 14 : 12,
                face:        'Outfit, sans-serif',
                strokeWidth: 3,
                strokeColor: '#ffffff'
            },
            shape:       n.group === 'target' ? 'star' : 'dot',
            size:        n.group === 'target' ? 24 : (n.group === 'recommendation' ? 18 : 12),
            borderWidth: 2
        })));

        const visEdges = new vis.DataSet(data.edges.map(e => ({
            from:   e.from,
            to:     e.to,
            arrows: { to: { enabled: true, scaleFactor: 0.55 } },
            color: {
                color:     e.label === 'recommended' ? '#f59e0b' : '#cbd5e1',
                opacity:   0.85,
            },
            width:  e.label === 'recommended' ? 2.5 : 1.5,
            dashes: e.label === 'recommended' ? [5, 4] : false,
            smooth: { type: 'continuous', roundness: 0.25 }
        })));

        const container = document.getElementById(containerId);
        const options = {
            physics: {
                enabled: true,
                barnesHut: { gravitationalConstant: -3000, centralGravity: 0.3, springLength: 95 },
                stabilization: { iterations: 100 }
            },
            layout: { randomSeed: 42 },
            interaction: {
                dragNodes: false,
                dragView: false,
                zoomView: false
            }
        };

        const network = new vis.Network(container, { nodes: visNodes, edges: visEdges }, options);
        
        network.on("stabilizationIterationsDone", function () {
            network.setOptions( { physics: false } );
        });

        return network;
    }

    document.addEventListener("DOMContentLoaded", function() {
        initNetwork('cy-cascading', graphCas);
        initNetwork('cy-standard', graphStd);

        // Tunggu stabilisasi rendering vis-network sebelum panggil window.print()
        setTimeout(() => {
            window.print();
        }, 1500);
    });
</script>
</body>
</html>
