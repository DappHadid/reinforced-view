@extends('layouts.app')

@section('title', 'Hasil Evaluasi | REINFORCED')
@section('page-title', 'Hasil Evaluasi')
@section('page-subtitle', 'Performa model rekomendasi: Standar vs Cascading Hybrid')

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- ============ METRIC COMPARISON (ANE SCORES) ============ --}}
    <div class="mb-4">
        <h2 class="text-lg font-bold text-slate-900">Evaluasi Performa (ANE & Metrik Dasar)</h2>
        <p class="text-sm text-slate-500">Menilai performa model menggunakan Attributed Network Embedding (ANE) serta metrik akurasi standar.</p>
    </div>
    
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        @foreach([['data' => $evaluasiStandar, 'badge' => 'text-primary-700 bg-primary-50'], ['data' => $evaluasiHybrid, 'badge' => 'text-primary-700 bg-primary-50']] as $card)
            @php $e = $card['data']; @endphp
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-bold text-slate-900">{{ $e['metode'] }}</h2>
                    <span class="text-[11px] font-semibold {{ $card['badge'] }} px-2.5 py-1 rounded-full">Model</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-2xl font-extrabold text-slate-900">{{ number_format($e['precision_at_5'] * 100, 1) }}%</p>
                        <p class="text-xs text-slate-400 mt-1">Precision@5</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-2xl font-extrabold text-slate-900">{{ number_format($e['recall'] * 100, 1) }}%</p>
                        <p class="text-xs text-slate-400 mt-1">Recall</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-2xl font-extrabold text-slate-900">{{ number_format($e['f1_score'] * 100, 1) }}%</p>
                        <p class="text-xs text-slate-400 mt-1">F1 Score</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-2xl font-extrabold text-slate-900">{{ number_format($e['map_at_5'] * 100, 1) }}%</p>
                        <p class="text-xs text-slate-400 mt-1">Skor ANE (Mean)</p>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ============ S-BERT SCORES ============ --}}
    <div class="mb-4 pt-4 border-t border-slate-200">
        <h2 class="text-lg font-bold text-slate-900">Perbandingan Skor S-BERT</h2>
        <p class="text-sm text-slate-500">Kemiripan semantik rata-rata judul publikasi berdasarkan model Sentence-BERT.</p>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach([$evaluasiStandar, $evaluasiHybrid] as $e)
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500">{{ $e['metode'] }}</p>
                    <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($e['sbert_mean'] * 100, 1) }}%</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-emerald-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        @endforeach
    </section>
    {{-- ============ RIWAYAT PENILAIAN ============ --}}
    <div class="mt-12 mb-4 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Riwayat Penilaian Pengguna</h2>
            <p class="text-sm text-slate-500">Daftar evaluasi kualitas rekomendasi yang diberikan pengguna.</p>
        </div>
        
        <!-- Filter Metode -->
        <div class="flex p-1 bg-slate-100 rounded-lg w-fit">
            <button onclick="filterRiwayat('Semua')" id="btn-filter-Semua" class="px-4 py-2 text-sm font-medium rounded-md bg-white text-slate-800 shadow-sm transition-all filter-btn">Semua</button>
            <button onclick="filterRiwayat('Cascading Hybrid')" id="btn-filter-Cascading" class="px-4 py-2 text-sm font-medium rounded-md text-slate-500 hover:text-slate-700 transition-all filter-btn">Cascading Hybrid</button>
            <button onclick="filterRiwayat('Standard ANE')" id="btn-filter-Standard" class="px-4 py-2 text-sm font-medium rounded-md text-slate-500 hover:text-slate-700 transition-all filter-btn">Standard ANE</button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-12">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4 w-1/12">No</th>
                        <th class="px-6 py-4 w-1/3">Target Peneliti</th>
                        <th class="px-6 py-4 text-center">Total Evaluasi</th>
                        <th class="px-6 py-4 text-center">Skor Rata-Rata</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penilaianRekap as $index => $rekap)
                        @php
                            $rekaps = $rekap['rekomendasi'] ?? [];
                            $cascading = array_filter($rekaps, fn($r) => ($r['metode'] ?? '') == 'Cascading Hybrid');
                            $standard = array_filter($rekaps, fn($r) => ($r['metode'] ?? '') == 'Standard ANE');
                            
                            $countAll = count($rekaps);
                            $avgAll = $countAll > 0 ? array_sum(array_column($rekaps, 'score')) / $countAll : 0;

                            $countC = count($cascading);
                            $avgC = $countC > 0 ? array_sum(array_column($cascading, 'score')) / $countC : 0;
                            
                            $countS = count($standard);
                            $avgS = $countS > 0 ? array_sum(array_column($standard, 'score')) / $countS : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors rekap-row"
                            data-all-count="{{ $countAll }}" data-all-avg="{{ number_format($avgAll, 1) }}"
                            data-cascading-count="{{ $countC }}" data-cascading-avg="{{ number_format($avgC, 1) }}"
                            data-standard-count="{{ $countS }}" data-standard-avg="{{ number_format($avgS, 1) }}">
                            
                            <td class="px-6 py-4 text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $rekap['nama'] }}</td>
                            
                            <td class="px-6 py-4 text-center text-slate-600 js-count">
                                {{ $countAll }} Penilaian
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center text-amber-400">
                                    <span class="js-avg">{{ number_format($avgAll, 1) }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <button type="button" onclick="openDetailModal('modal-detail-{{ $index }}')" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Belum ada data penilaian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modals for Details --}}
    @foreach($penilaianRekap as $index => $rekap)
        <div id="modal-detail-{{ $index }}" class="fixed inset-0 z-[100] hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeDetailModal('modal-detail-{{ $index }}')"></div>
            
            <!-- Modal Box -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-[90vh]">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Detail Penilaian: {{ $rekap['nama'] }}</h3>
                        <p class="text-sm text-slate-500">Daftar evaluasi yang telah diberikan pada peneliti ini.</p>
                    </div>
                    <button onclick="closeDetailModal('modal-detail-{{ $index }}')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                
                <!-- Body -->
                <div class="p-0 overflow-y-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider text-xs sticky top-0 shadow-sm">
                            <tr>
                                <th class="px-6 py-4">Metode</th>
                                <th class="px-6 py-4">Rekomendasi Dinilai</th>
                                <th class="px-6 py-4 text-center">Skor</th>
                                <th class="px-6 py-4">Komentar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @if(isset($rekap['rekomendasi']) && is_array($rekap['rekomendasi']))
                                @foreach($rekap['rekomendasi'] as $rek)
                                    @php $metode = $rek['metode'] ?? 'Cascading Hybrid'; @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors modal-rek-row" data-metode="{{ $metode }}">
                                        <td class="px-6 py-4">
                                            @if($metode == 'Cascading Hybrid')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Cascading Hybrid</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Standard ANE</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-slate-900 font-medium">{{ $rek['nama'] }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center text-amber-400">
                                                {{ $rek['score'] }}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 whitespace-normal min-w-[200px]">
                                            {{ !empty($rek['komentar']) ? $rek['komentar'] : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">Data detail tidak tersedia.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function filterRiwayat(metode) {
            // Update buttons styling
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'text-slate-800', 'shadow-sm');
                btn.classList.add('text-slate-500');
            });
            
            let activeBtnId = 'btn-filter-Semua';
            if(metode === 'Cascading Hybrid') activeBtnId = 'btn-filter-Cascading';
            if(metode === 'Standard ANE') activeBtnId = 'btn-filter-Standard';
            
            let activeBtn = document.getElementById(activeBtnId);
            activeBtn.classList.remove('text-slate-500');
            activeBtn.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
            
            // Update main table rows
            document.querySelectorAll('.rekap-row').forEach(row => {
                let count = 0;
                let avg = '0.0';
                
                if(metode === 'Semua') {
                    count = row.getAttribute('data-all-count');
                    avg = row.getAttribute('data-all-avg');
                } else if(metode === 'Cascading Hybrid') {
                    count = row.getAttribute('data-cascading-count');
                    avg = row.getAttribute('data-cascading-avg');
                } else if(metode === 'Standard ANE') {
                    count = row.getAttribute('data-standard-count');
                    avg = row.getAttribute('data-standard-avg');
                }
                
                row.querySelector('.js-count').innerText = count + ' Penilaian';
                row.querySelector('.js-avg').innerText = parseInt(count) > 0 ? avg : '-';
                
                // Hide row completely if count is 0 for specific filter
                if(parseInt(count) === 0 && metode !== 'Semua') {
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                }
            });

            // Update modal details
            document.querySelectorAll('.modal-rek-row').forEach(row => {
                if(metode === 'Semua' || row.getAttribute('data-metode') === metode) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openDetailModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDetailModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.style.overflow = '';
        }
    </script>
@endsection
