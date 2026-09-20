@extends('layouts.app')

@section('title', 'Hasil Evaluasi | REINFORCED')
@section('page-title', 'Hasil Evaluasi')
@section('page-subtitle', 'Performa model rekomendasi: Standar vs Cascading Hybrid')

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- ================================================================
     SECTION 1 — RIWAYAT PENILAIAN PENGGUNA
================================================================ --}}
<section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

    {{-- ── Hero Header ─────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center gap-6 p-6 md:p-8">

        {{-- Illustration — large, left, ~1/3 width --}}
        <div class="w-full md:w-1/3 flex justify-center shrink-0">
            <img src="{{ asset('images/online-review-animate.svg') }}"
                 alt="Riwayat Penilaian"
                 class="w-48 sm:w-64 md:w-full max-w-[260px] h-auto object-contain
                        select-none hover:scale-[1.02] transition-transform duration-500">
        </div>

        {{-- Text + Controls — right side --}}
        <div class="w-full md:w-2/3 flex flex-col gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">
                    Riwayat Penilaian Pengguna
                </h2>
                <p class="mt-1.5 text-sm text-slate-500 leading-relaxed max-w-lg">
                    Daftar evaluasi kualitas rekomendasi yang diberikan pengguna.
                    Anda dapat melihat detail komentar dan skor perbandingan dari masing-masing metode.
                </p>
            </div>

            {{-- Filter tabs --}}
            <div class="flex rounded-xl border border-slate-200 bg-slate-50 p-1 w-fit">
                <button onclick="filterRiwayat('Cascading Hybrid')" id="btn-filter-Cascading"
                        class="px-5 py-2 rounded-lg text-sm font-semibold text-primary-700
                               bg-white shadow-sm transition-all filter-btn">
                    Cascading Hybrid
                </button>
                <button onclick="filterRiwayat('Standard ANE')" id="btn-filter-Standard"
                        class="px-5 py-2 rounded-lg text-sm font-normal text-slate-500
                               hover:text-slate-700 transition-all filter-btn">
                    Standard ANE
                </button>
            </div>

            {{-- Search & Export --}}
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <input type="text" id="riwayat-search"
                           placeholder="Cari nama Dosen peneliti..."
                           class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-slate-200
                                  bg-slate-50 hover:bg-white focus:bg-white
                                  focus:outline-none focus:ring-2 focus:ring-primary-400/30 focus:border-primary-400
                                  transition-all placeholder:text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <button onclick="exportCSV()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-slate-800 active:scale-95 transition-all shadow-sm focus:outline-none shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- ── Table Column Headers ──────────────────────────────────────── --}}
    <div class="border-t border-slate-100">
        <div class="grid px-6 py-3 bg-slate-50 border-b border-slate-100
                    text-[10px] font-bold uppercase tracking-widest text-slate-400"
             style="grid-template-columns: 2.75rem 1fr 9rem 8.5rem 6.5rem;">
            <span class="text-center">No</span>
            <button onclick="toggleSort('target')" class="inline-flex items-center gap-1.5 hover:text-slate-700 transition-colors focus:outline-none w-full text-left group font-bold uppercase tracking-widest text-[10px]">
                Nama Peneliti (Target)
                <svg id="sort-arrow-target" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-300 group-hover:text-slate-500 shrink-0 transition-transform sort-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
            </button>
            <span class="text-center">Metode</span>
            <button onclick="toggleSort('score')" class="inline-flex items-center justify-center gap-1.5 hover:text-slate-700 transition-colors focus:outline-none w-full text-center group font-bold uppercase tracking-widest text-[10px]">
                Skor Rata-rata
                <svg id="sort-arrow-score" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-primary-500 rotate-180 shrink-0 transition-transform sort-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
            </button>
            <span class="text-center">Aksi</span>
        </div>
    </div>

    {{-- ── Fixed-height Table Body (always 10 rows) ─────────────────── --}}
    <div id="riwayat-body" class="divide-y divide-slate-100">
        {{-- Populated by JavaScript --}}
    </div>

    {{-- ── Pagination Footer ────────────────────────────────────────── --}}
    <div class="border-t border-slate-100 px-6 py-3.5 flex items-center justify-between gap-3
                text-xs text-slate-500 bg-white">
        <span id="riwayat-info" class="font-normal">-</span>
        <div id="riwayat-page-controls" class="flex items-center gap-1"></div>
    </div>

    {{-- ── Raw data blob (PHP → JS) ─────────────────────────────────── --}}
    <script id="riwayat-data-raw" type="application/json">
        @php
            $jsData = [];
            $dosenMap = [];
            try {
                // Enrich data with sinta ID and department if available
                $dl = \App\Support\ApiDataProvider::dosenList();
                foreach($dl as $i => $d) {
                    $transformed = \App\Http\Controllers\DosenController::transform($d, $i);
                    $dosenMap[strtolower($transformed['nama'] ?? '')] = $transformed;
                }
            } catch (\Exception $e) {}

            foreach ($penilaianRekap as $rekap) {
                if (!isset($rekap['rekomendasi']) || !is_array($rekap['rekomendasi'])) continue;
                
                $targetName = $rekap['nama'] ?? '-';
                $dInfo = $dosenMap[strtolower($targetName)] ?? null;
                $sintaId = $dInfo['sinta_id'] ?? '-';
                $dept = $dInfo['prodi'] ?? 'Program Studi Teknik Informatika';
                $dept = stripos($dept, 'Program Studi') === false ? $dept : $dept;

                $avatar = $dInfo['avatar_image'] ?? '';

                $cascading = [];
                $standard = [];

                foreach ($rekap['rekomendasi'] as $rek) {
                    $metode = $rek['metode'] ?? 'Cascading Hybrid';
                    $revName = $rek['nama'] ?? '-';
                    $rInfo = $dosenMap[strtolower($revName)] ?? null;
                    
                    $item = [
                        'nama' => $revName,
                        'score' => (int)($rek['score'] ?? 0),
                        'komentar' => $rek['komentar'] ?? '',
                        'avatar' => $rInfo['avatar_image'] ?? ''
                    ];
                    if ($metode === 'Cascading Hybrid') $cascading[] = $item;
                    else $standard[] = $item;
                }

                if (count($cascading) > 0) {
                    $jsData[] = [
                        'target' => $targetName,
                        'sinta'  => $sintaId !== '-' ? $sintaId : 'N/A',
                        'dept'   => $dept,
                        'avatar' => $avatar,
                        'metode' => 'Cascading Hybrid',
                        'score'  => round(array_sum(array_column($cascading, 'score')) / count($cascading), 1),
                        'reviews' => $cascading
                    ];
                }
                
                if (count($standard) > 0) {
                    $jsData[] = [
                        'target' => $targetName,
                        'sinta'  => $sintaId !== '-' ? $sintaId : 'N/A',
                        'dept'   => $dept,
                        'avatar' => $avatar,
                        'metode' => 'Standard ANE',
                        'score'  => round(array_sum(array_column($standard, 'score')) / count($standard), 1),
                        'reviews' => $standard
                    ];
                }
            }
        @endphp
        {!! json_encode($jsData) !!}
    </script>
</section>


{{-- ================================================================
     DETAIL MODAL (REVIEW LIST)
================================================================ --}}
<div id="detail-modal"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6
            opacity-0 pointer-events-none transition-opacity duration-200"
     role="dialog" aria-modal="true">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="closeModal()"></div>

    {{-- Panel --}}
    <article id="modal-panel"
             class="relative z-10 w-full max-w-2xl bg-white rounded-2xl
                    shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-full
                    scale-95 transition-transform duration-200">

        {{-- ── Modal Header ─────────────────────────────── --}}
        <div class="flex items-start justify-between gap-4 px-6 py-5 shrink-0 bg-white z-10 border-b border-slate-100">
            <div class="flex items-center gap-4 min-w-0">
                <img id="modal-avatar" src="{{ asset('images/avatar.jpg') }}" alt="Avatar" class="w-12 h-12 rounded-full object-cover shrink-0 border-2 border-white shadow-sm ring-1 ring-slate-100">
                <div class="min-w-0">
                    <h3 id="modal-title" class="text-lg font-bold text-slate-900 leading-tight truncate"></h3>
                    <div class="flex items-center gap-3 mt-1.5 text-xs">
                        <span id="modal-metode" class="font-semibold px-2 py-0.5 rounded border"></span>
                        <div class="flex items-center gap-1 font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded border border-amber-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                            <span id="modal-score-num"></span> Average
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button id="btn-export-pdf" onclick="exportModalPDF()" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-lg hover:bg-slate-800 transition-colors focus:outline-none shadow-sm" title="Export ulasan ini menjadi PDF">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export PDF
                </button>
                <button id="btn-close-modal" onclick="closeModal()" aria-label="Tutup modal"
                        class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center
                               text-slate-400 hover:bg-slate-100 hover:text-slate-700
                               transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Modal Body (Scrollable Review List) ──────── --}}
        <div class="overflow-y-auto p-6 bg-slate-50/50 flex-1">
            <h4 class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-4">Daftar Review (Rekomendasi Dinilai)</h4>
            <div id="modal-review-list" class="space-y-3">
                {{-- Diisi oleh JS --}}
            </div>
        </div>

        {{-- ── Modal Footer ─────────────────────────────── --}}
        <div class="px-6 py-4 border-t border-slate-100 bg-white shrink-0">
            <button onclick="closeModal()"
                    class="w-full py-2.5 rounded-xl border border-slate-200 bg-white
                           text-sm font-semibold text-slate-600 hover:text-slate-900
                           hover:bg-slate-50 active:bg-slate-100
                           transition-colors focus:outline-none focus:ring-2 focus:ring-slate-300">
                Tutup
            </button>
        </div>

    </article>
</div>


{{-- ================================================================
     SECTION 3 — S-BERT SCORES (MOVED TO TOP)
================================================================ --}}
<section class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-900">Perbandingan Skor S-BERT</h2>
        <p class="text-sm text-slate-500 mt-0.5">
            Kemiripan semantik rata-rata judul publikasi berdasarkan model Sentence-BERT.
        </p>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
        @foreach([$evaluasiStandar, $evaluasiHybrid] as $e)
            <div class="rounded-2xl border border-slate-100 bg-slate-50/60 px-5 py-5
                        hover:border-slate-200 hover:shadow-sm transition-all
                        flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">{{ $e['metode'] }}</p>
                    <p class="text-4xl font-extrabold text-slate-900 tabular-nums leading-none">
                        {{ number_format($e['sbert_mean'] * 100, 1) }}%
                    </p>
                    <p class="text-[11px] text-slate-400 mt-1.5 font-medium">Kemiripan Semantik</p>
                </div>
            </div>
        @endforeach
    </div>
</section>


{{-- ================================================================
     SECTION 2 — EVALUASI PERFORMA ANE (MOVED TO BOTTOM)
================================================================ --}}
<section class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-900">Evaluasi Performa (ANE &amp; Metrik Dasar)</h2>
        <p class="text-sm text-slate-500 mt-0.5">
            Menilai performa model menggunakan Attributed Network Embedding serta metrik akurasi standar.
        </p>
    </div>
    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-5">
        @foreach([['data' => $evaluasiStandar], ['data' => $evaluasiHybrid]] as $card)
            @php $e = $card['data']; @endphp
            <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5
                        hover:border-slate-200 hover:shadow-sm transition-all">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">{{ $e['metode'] }}</h3>
                    <span class="text-[10px] font-bold uppercase tracking-widest
                                 text-primary-600 bg-primary-50 border border-primary-100
                                 px-2.5 py-1 rounded-full">Model</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['label' => 'Precision@5', 'value' => number_format($e['precision_at_5'] * 100, 1) . '%'],
                        ['label' => 'Recall',      'value' => number_format($e['recall']          * 100, 1) . '%'],
                        ['label' => 'F1 Score',    'value' => number_format($e['f1_score']        * 100, 1) . '%'],
                        ['label' => 'Skor ANE',    'value' => number_format($e['map_at_5']        * 100, 1) . '%'],
                    ] as $m)
                        <div class="rounded-xl bg-white border border-slate-100 px-4 py-3.5">
                            <p class="text-xl font-extrabold text-slate-900">{{ $m['value'] }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5 font-medium">{{ $m['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>


{{-- ================================================================
     JS ENGINE
================================================================ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
(function () {
    'use strict';

    var ROWS_PER_PAGE = 10;
    var rawData       = JSON.parse(document.getElementById('riwayat-data-raw').textContent);
    var filteredRows = [];

    window.exportModalPDF = function() {
        if (!window.currentModalSintaId || window.currentModalSintaId === 'N/A') {
            alert("SINTA ID tidak ditemukan untuk kandidat ini. Gagal mengekspor laporan lengkap.");
            return;
        }
        window.open('/dosen/' + window.currentModalSintaId + '/cetak-laporan', '_blank');
    };

    function getAvatarUrl(name, avatar) {
        if (avatar && avatar !== 'images/avatar.jpg') {
            return avatar.startsWith('http') ? avatar : "{{ asset('') }}" + avatar;
        }
        return "{{ asset('images/avatar.jpg') }}";
    }

    var currentMetode = 'Cascading Hybrid';
    var searchQuery   = '';
    var currentPage   = 1;
    var currentSort   = 'score'; // default column
    var sortAsc       = false;   // default descending

    window.toggleSort = function(col) {
        if (currentSort === col) {
            sortAsc = !sortAsc;
        } else {
            currentSort = col;
            sortAsc = (col === 'target') ? true : false;
        }
        currentPage = 1;
        
        // Update arrow UI
        document.querySelectorAll('.sort-arrow').forEach(function (el) {
            el.classList.remove('text-primary-500', 'rotate-180');
            el.classList.add('text-slate-300');
        });
        var activeEl = document.getElementById('sort-arrow-' + col);
        if (activeEl) {
            activeEl.classList.remove('text-slate-300');
            activeEl.classList.add('text-primary-500');
            if (!sortAsc) activeEl.classList.add('rotate-180');
        }

        render();
    };

    /* ── helpers ───────────────────────────────────────────────── */
    function getIni(name) {
        return (name || '').split(/\s+/).slice(0, 2).map(function (w) { return w[0] || ''; }).join('').toUpperCase();
    }

    function stars(score, lg) {
        var size = lg ? 'w-4 h-4' : 'w-3.5 h-3.5';
        var s = '';
        for (var i = 1; i <= 5; i++) {
            var col = i <= score ? 'text-amber-400' : 'text-slate-200';
            s += '<svg class="' + size + ' ' + col + ' shrink-0" viewBox="0 0 24 24" fill="currentColor">' +
                 '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>' +
                 '</svg>';
        }
        return '<span class="flex items-center gap-0.5">' + s + '</span>';
    }

    /* ── data helpers ──────────────────────────────────────────── */
    function getFiltered() {
        var rows = rawData.filter(function (r) { return r.metode === currentMetode; });
        if (searchQuery) {
            var q = searchQuery.toLowerCase();
            rows = rows.filter(function (r) {
                return r.target.toLowerCase().indexOf(q) > -1 ||
                       r.dept.toLowerCase().indexOf(q) > -1;
            });
        }
        
        // Apply sorting
        if (currentSort === 'score') {
            rows.sort(function(a, b) { return sortAsc ? a.score - b.score : b.score - a.score; });
        } else if (currentSort === 'target') {
            rows.sort(function(a, b) {
                var ta = a.target.toLowerCase();
                var tb = b.target.toLowerCase();
                if (ta < tb) return sortAsc ? -1 : 1;
                if (ta > tb) return sortAsc ? 1 : -1;
                return 0;
            });
        }
        return rows;
    }

    /* ── export helpers ────────────────────────────────────────── */
    window.exportCSV = function() {
        var rows = getFiltered();
        if (rows.length === 0) {
            alert('Tidak ada data untuk diekspor pada filter ini.');
            return;
        }

        var csv = [];
        // Header
        csv.push("No,Nama Peneliti (Target),SINTA ID,Program Studi,Metode,Skor Rata-rata,Nama Reviewer,Bintang Diberikan,Komentar Reviewer");
        
        // Rows
        var lineNum = 1;
        rows.forEach(function(r) {
            var target = '"' + (r.target || '').replace(/"/g, '""') + '"';
            var sinta  = '"' + (r.sinta || '').replace(/"/g, '""') + '"';
            var dept   = '"' + (r.dept || '').replace(/"/g, '""') + '"';
            var metode = '"' + (r.metode || '').replace(/"/g, '""') + '"';
            var score  = r.score;

            if (r.reviews && r.reviews.length > 0) {
                r.reviews.forEach(function(rev, j) {
                    var revName  = '"' + (rev.nama || '').replace(/"/g, '""') + '"';
                    var revScore = rev.score;
                    var revKomen = '"' + (rev.komentar || '').replace(/"/g, '""') + '"';
                    
                    if (j === 0) {
                        // Baris pertama: tampilkan seluruh data target
                        csv.push([lineNum++, target, sinta, dept, metode, score, revName, revScore, revKomen].join(','));
                    } else {
                        // Baris selanjutnya: kosongkan data target agar tidak redudansi saat dibaca di Excel
                        csv.push(['""', '""', '""', '""', '""', '""', revName, revScore, revKomen].join(','));
                    }
                });
            } else {
                csv.push([lineNum++, target, sinta, dept, metode, score, '""', '""', '""'].join(','));
            }
        });

        var csvContent = "\uFEFF" + csv.join("\r\n");
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        
        var link = document.createElement("a");
        link.setAttribute("href", url);
        
        var now = new Date();
        var dateStr = now.toISOString().slice(0, 10);
        var timeStr = now.toTimeString().split(' ')[0].replace(/:/g, '');
        var fileName = "evaluasi_peneliti_" + dateStr + "_" + timeStr + ".csv";
        
        link.setAttribute("download", fileName);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    };

    /* ── skeleton row ──────────────────────────────────────────── */
    function skeletonRow() {
        return '<div class="grid items-center px-6" ' +
               'style="grid-template-columns:2.75rem 1fr 9rem 8.5rem 6.5rem;min-height:76px;">' +
                   '<div class="flex justify-center">' +
                       '<div class="h-3 w-4 bg-slate-100 rounded animate-pulse"></div>' +
                   '</div>' +
                   '<div class="flex items-center gap-3 py-3 pr-3">' +
                       '<div class="w-10 h-10 bg-slate-100 rounded-full animate-pulse shrink-0"></div>' +
                       '<div class="space-y-2 w-full">' +
                           '<div class="h-3 bg-slate-100 rounded animate-pulse w-1/2"></div>' +
                           '<div class="h-2.5 bg-slate-100 rounded animate-pulse w-3/4"></div>' +
                       '</div>' +
                   '</div>' +
                   '<div class="flex justify-center">' +
                       '<div class="h-6 bg-slate-100 rounded animate-pulse w-20"></div>' +
                   '</div>' +
                   '<div class="flex justify-center">' +
                       '<div class="h-5 bg-slate-100 rounded animate-pulse w-24"></div>' +
                   '</div>' +
                   '<div class="flex justify-center">' +
                       '<div class="h-8 w-16 bg-slate-100 rounded-lg animate-pulse"></div>' +
                   '</div>' +
               '</div>';
    }

    /* ── render table ──────────────────────────────────────────── */
    function render() {
        var filtered   = getFiltered();
        var total      = filtered.length;
        var totalPages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1)         currentPage = 1;

        var startIdx = (currentPage - 1) * ROWS_PER_PAGE;
        var pageRows = filtered.slice(startIdx, startIdx + ROWS_PER_PAGE);
        var body     = document.getElementById('riwayat-body');
        var html     = '';

        /* empty state */
        if (total === 0) {
            var ph = ROWS_PER_PAGE * 76;
            body.innerHTML =
                '<div class="flex flex-col items-center justify-center gap-3 text-slate-400" ' +
                'style="min-height:' + ph + 'px;">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-200" ' +
                    'viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">' +
                        '<circle cx="11" cy="11" r="8"/>' +
                        '<line x1="21" y1="21" x2="16.65" y2="16.65"/>' +
                    '</svg>' +
                    '<p class="text-sm font-medium">Tidak ada data target peneliti ditemukan.</p>' +
                '</div>';
            renderFooter(0, 0, 0, 1);
            return;
        }

        pageRows.forEach(function (row, idx) {
            var no        = startIdx + idx + 1;
            var dynAvatar = getAvatarUrl(row.target, row.avatar);

            var metodeBadge = row.metode === 'Cascading Hybrid'
                ? '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Cascading</span>'
                : '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">Standard</span>';

            html +=
                '<div class="grid items-center px-6 hover:bg-slate-50/60 transition-colors" ' +
                'style="grid-template-columns:2.75rem 1fr 9rem 8.5rem 6.5rem;min-height:76px;">' +

                    /* no */
                    '<span class="text-center text-sm font-medium text-slate-400 tabular-nums">' + no + '</span>' +

                    /* Peneliti info */
                    '<div class="flex items-center gap-3 py-3 pr-3 min-w-0">' +
                        '<img src="' + dynAvatar + '" alt="' + row.target + '" class="w-10 h-10 rounded-full object-cover shrink-0 border border-slate-200 bg-slate-100 shadow-sm">' +
                        '<div class="min-w-0">' +
                            '<p class="text-[15px] font-bold text-slate-900 truncate leading-tight">' + row.target + '</p>' +
                            '<p class="text-xs text-slate-500 mt-0.5 truncate">' + row.dept + '</p>' +
                        '</div>' +
                    '</div>' +

                    /* metode */
                    '<div class="flex justify-center">' + metodeBadge + '</div>' +

                    /* avg score */
                    '<div class="flex items-center justify-center gap-1.5">' +
                        stars(Math.round(row.score), true) +
                        '<span class="text-sm font-bold text-slate-800 tabular-nums">' + row.score.toFixed(1) + '</span>' +
                    '</div>' +

                    /* aksi */
                    '<div class="flex justify-center">' +
                        '<button onclick="openModal(' + (startIdx + idx) + ')"' +
                            ' class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg ' +
                                    'border border-slate-200 bg-white text-xs font-semibold text-slate-700 ' +
                                    'hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50 ' +
                                    'active:scale-95 transition-all shadow-sm focus:outline-none ' +
                                    'focus:ring-2 focus:ring-primary-300">' +
                            '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" ' +
                                 'viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">' +
                                '<line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line>' +
                            '</svg>' +
                            'Detail' +
                        '</button>' +
                    '</div>' +

                '</div>';
        });

        /* skeleton filler rows */
        var pad = ROWS_PER_PAGE - pageRows.length;
        for (var i = 0; i < pad; i++) {
            html += skeletonRow();
        }

        body.innerHTML = html;
        renderFooter(total, startIdx + 1, startIdx + pageRows.length, totalPages);
    }

    /* ── footer / pagination ───────────────────────────────────── */
    function renderFooter(total, from, to, totalPages) {
        var info  = document.getElementById('riwayat-info');
        var ctrls = document.getElementById('riwayat-page-controls');
        if (!info || !ctrls) return;

        if (total === 0) {
            info.innerHTML = '<span class="text-slate-400 italic">Tidak ada data.</span>';
            ctrls.innerHTML = '';
            return;
        }

        info.innerHTML = 'Menampilkan&nbsp;<strong class="text-slate-700">' + from + '&ndash;' + to + '</strong>' +
                         '&nbsp;dari&nbsp;<strong class="text-slate-700">' + total + '</strong>&nbsp;peneliti';

        if (totalPages <= 1) { ctrls.innerHTML = ''; return; }

        var btnBase = 'px-2.5 py-1 rounded-lg border text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-slate-300 ';
        var h = '<button onclick="riwayatPage(' + (currentPage - 1) + ')" ' +
                (currentPage === 1 ? 'disabled ' : '') +
                'class="' + btnBase + 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">' +
                '&laquo;&nbsp;Prev</button>';

        for (var i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                h += '<button class="' + btnBase + 'border-slate-900 bg-slate-900 text-white cursor-default">' + i + '</button>';
            } else {
                h += '<button onclick="riwayatPage(' + i + ')" class="' + btnBase + 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50">' + i + '</button>';
            }
        }

        h += '<button onclick="riwayatPage(' + (currentPage + 1) + ')" ' +
             (currentPage === totalPages ? 'disabled ' : '') +
             'class="' + btnBase + 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">' +
             'Next&nbsp;&raquo;</button>';

        ctrls.innerHTML = h;
    }

    /* ── public: pagination ────────────────────────────────────── */
    window.riwayatPage = function (p) {
        var total = Math.max(1, Math.ceil(getFiltered().length / ROWS_PER_PAGE));
        if (p < 1 || p > total) return;
        currentPage = p;
        render();
    };

    /* ── public: filter ────────────────────────────────────────── */
    window.filterRiwayat = function (metode) {
        currentMetode = metode;
        currentPage   = 1;
        document.querySelectorAll('.filter-btn').forEach(function (b) {
            b.classList.remove('bg-white', 'text-primary-700', 'shadow-sm', 'font-semibold');
            b.classList.add('text-slate-500', 'font-normal');
        });
        var activeId = metode === 'Standard ANE' ? 'btn-filter-Standard' : 'btn-filter-Cascading';
        var ab = document.getElementById(activeId);
        if (ab) {
            ab.classList.remove('text-slate-500', 'font-normal');
            ab.classList.add('bg-white', 'text-primary-700', 'shadow-sm', 'font-semibold');
        }
        render();
    };

    /* ── public: open modal ────────────────────────────────────── */
    window.openModal = function (index) {
        var row   = getFiltered()[index];
        if (!row) return;
        var modal = document.getElementById('detail-modal');
        var panel = document.getElementById('modal-panel');

        /* save sinta_id globally for PDF Export */
        window.currentModalSintaId = row.sinta;

        /* populate header */
        document.getElementById('modal-avatar').src = getAvatarUrl(row.target, row.avatar);
        document.getElementById('modal-title').textContent = row.target;
        document.getElementById('modal-score-num').textContent = row.score.toFixed(1);
        
        var mb = document.getElementById('modal-metode');
        if (row.metode === 'Cascading Hybrid') {
            mb.className = 'text-emerald-700 bg-emerald-50 border-emerald-200';
        } else {
            mb.className = 'text-blue-700 bg-blue-50 border-blue-200';
        }
        mb.textContent = row.metode;

        /* populate reviews list */
        var listHtml = '';
        if (row.reviews && row.reviews.length > 0) {
            row.reviews.forEach(function(rev, i) {
                var revAvatarUrl = getAvatarUrl(rev.nama, rev.avatar);
                var kom = rev.komentar ? rev.komentar : '<span class="text-slate-400 italic">Tidak ada komentar.</span>';
                listHtml += 
                    '<div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col gap-3">' +
                        '<div class="flex items-start justify-between gap-3">' +
                            '<div class="flex items-center gap-3 min-w-0">' +
                                '<img src="' + revAvatarUrl + '" alt="' + rev.nama + '" class="w-10 h-10 rounded-lg object-cover bg-slate-100 border border-slate-200 shrink-0 shadow-sm">' +
                                '<div class="min-w-0">' +
                                    '<p class="text-sm font-bold text-slate-900 truncate leading-tight">' + rev.nama + '</p>' +
                                    '<div class="mt-1">' + stars(rev.score, false) + '</div>' +
                                '</div>' +
                            '</div>' +
                            '<span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 shrink-0">Review ' + (i+1) + '</span>' +
                        '</div>' +
                        '<div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-sm text-slate-600 leading-relaxed">' +
                            kom +
                        '</div>' +
                    '</div>';
            });
        } else {
            listHtml = '<p class="text-sm text-slate-500 text-center py-4">Belum ada review untuk target ini.</p>';
        }
        document.getElementById('modal-review-list').innerHTML = listHtml;

        /* animate in */
        modal.classList.remove('opacity-0', 'pointer-events-none');
        requestAnimationFrame(function () {
            panel.classList.remove('scale-95');
            panel.classList.add('scale-100');
        });
        document.body.style.overflow = 'hidden';
    };

    /* ── public: close modal ───────────────────────────────────── */
    window.closeModal = function () {
        var modal = document.getElementById('detail-modal');
        var panel = document.getElementById('modal-panel');
        panel.classList.remove('scale-100');
        panel.classList.add('scale-95');
        modal.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = '';
    };

    /* ── keyboard ESC ──────────────────────────────────────────── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    /* ── init ──────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        render();
        var si = document.getElementById('riwayat-search');
        if (si) {
            si.addEventListener('input', function (e) {
                searchQuery = e.target.value.trim();
                currentPage = 1;
                render();
            });
        }
    });

})();
</script>

@endsection