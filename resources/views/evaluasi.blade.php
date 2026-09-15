@extends('layouts.app')

@section('title', 'Hasil Evaluasi | REINFORCED')
@section('page-title', 'Hasil Evaluasi')
@section('page-subtitle', 'Performa model rekomendasi: Standar vs Cascading Hybrid')

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- ============ METRIC COMPARISON (ANE SCORES) ============ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-8 overflow-hidden">
        <div class="p-6 md:px-8 md:py-6 border-b border-slate-200 bg-white">
            <h2 class="text-xl font-semibold text-slate-900">Evaluasi Performa (ANE & Metrik Dasar)</h2>
            <p class="text-base font-normal text-slate-500 mt-1">Menilai performa model menggunakan Attributed Network Embedding (ANE) serta metrik akurasi standar.</p>
        </div>
        
        <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-2 gap-6 bg-slate-50/50">
            @foreach([['data' => $evaluasiStandar, 'badge' => 'text-primary-700 bg-primary-50'], ['data' => $evaluasiHybrid, 'badge' => 'text-primary-700 bg-primary-50']] as $card)
                @php $e = $card['data']; @endphp
                <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-semibold text-slate-900">{{ $e['metode'] }}</h2>
                    <span class="text-xs font-semibold {{ $card['badge'] }} px-2.5 py-1 rounded-full">Model</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-2xl font-extrabold text-slate-900">{{ number_format($e['precision_at_5'] * 100, 1) }}%</p>
                        <p class="text-xs font-normal text-slate-400 mt-1">Precision@5</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-2xl font-extrabold text-slate-900">{{ number_format($e['recall'] * 100, 1) }}%</p>
                        <p class="text-xs font-normal text-slate-400 mt-1">Recall</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-2xl font-extrabold text-slate-900">{{ number_format($e['f1_score'] * 100, 1) }}%</p>
                        <p class="text-xs font-normal text-slate-400 mt-1">F1 Score</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-2xl font-extrabold text-slate-900">{{ number_format($e['map_at_5'] * 100, 1) }}%</p>
                        <p class="text-xs font-normal text-slate-400 mt-1">Skor ANE (Mean)</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ============ S-BERT SCORES ============ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-12 overflow-hidden">
        <div class="p-6 md:px-8 md:py-6 border-b border-slate-200 bg-white">
            <h2 class="text-xl font-semibold text-slate-900">Perbandingan Skor S-BERT</h2>
            <p class="text-base font-normal text-slate-500 mt-1">Kemiripan semantik rata-rata judul publikasi berdasarkan model Sentence-BERT.</p>
        </div>

        <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50">
            @foreach([$evaluasiStandar, $evaluasiHybrid] as $e)
                <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
                <div>
                    <p class="text-base font-semibold text-slate-500">{{ $e['metode'] }}</p>
                    <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($e['sbert_mean'] * 100, 1) }}%</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    {{-- ============ RIWAYAT PENILAIAN ============ --}}
    <div class="mt-12 mb-12 bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        
        {{-- Header / Hero Section --}}
        <div class="p-8 border-b border-slate-200 flex flex-col md:flex-row items-center gap-8">
            <div class="w-full md:w-1/3 flex justify-center">
                <img src="{{ asset('images/online-review-animate.svg') }}" alt="Riwayat Penilaian" class="w-48 sm:w-64 h-auto object-contain hover:scale-[1.02] transition-transform duration-500">
            </div>
            <div class="w-full md:w-2/3">
                <h2 class="text-2xl font-bold text-slate-900">Riwayat Penilaian Pengguna</h2>
                <p class="mt-2 text-base font-normal text-slate-500">Daftar evaluasi kualitas rekomendasi yang diberikan pengguna. Anda dapat melihat detail komentar dan skor perbandingan dari masing-masing metode.</p>
                
                <!-- Filter Metode -->
                <div class="mt-6 flex p-1 bg-slate-50 rounded-xl w-fit border border-slate-200 shadow-sm">
                    <button onclick="filterRiwayat('Cascading Hybrid')" id="btn-filter-Cascading" class="px-5 py-2.5 text-xs sm:text-base font-semibold rounded-lg bg-white text-primary-700 shadow-sm transition-all filter-btn">Cascading Hybrid</button>
                    <button onclick="filterRiwayat('Standard ANE')" id="btn-filter-Standard" class="px-5 py-2.5 text-xs sm:text-base font-normal rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100/50 transition-all filter-btn">Standard ANE</button>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-base whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Target Peneliti</th>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4">Rekomendasi Dinilai</th>
                        <th class="px-6 py-4 text-center">Skor</th>
                        <th class="px-6 py-4">Komentar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 table-body">
                    @php $hasData = false; @endphp
                    @foreach($penilaianRekap as $rekap)
                        @if(isset($rekap['rekomendasi']) && is_array($rekap['rekomendasi']))
                            @foreach($rekap['rekomendasi'] as $rek)
                                @php 
                                    $hasData = true; 
                                    $metode = $rek['metode'] ?? 'Cascading Hybrid';
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors rekap-row" data-metode="{{ $metode }}">
                                    <td class="px-6 py-4 text-slate-500 text-center font-medium row-number"></td>
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $rekap['nama'] }}</td>
                                    <td class="px-6 py-4">
                                        @if($metode == 'Cascading Hybrid')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800">Cascading Hybrid</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-100 text-blue-800">Standard ANE</span>
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
                        @endif
                    @endforeach
                    
                    @if(!$hasData)
                        <tr id="empty-row">
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                Belum ada data penilaian.
                            </td>
                        </tr>
                    @endif
                    
                    <tr id="no-match-row" style="display: none;">
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                            Tidak ada penilaian dengan metode ini.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Controls (Injected via JS) -->
    <div id="pagination-container" class="border-t border-slate-100 bg-slate-50 rounded-b-2xl"></div>

    <script>
        let currentPage = 1;
        const rowsPerPage = 5;
        let currentMetode = 'Cascading Hybrid';

        function filterRiwayat(metode) {
            currentMetode = metode;
            currentPage = 1; // Reset to page 1 on filter change
            
            // Update buttons styling
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'text-primary-700', 'shadow-sm', 'font-semibold');
                btn.classList.add('text-slate-500', 'font-normal');
            });
            
            let activeBtnId = metode === 'Standard ANE' ? 'btn-filter-Standard' : 'btn-filter-Cascading';
            let activeBtn = document.getElementById(activeBtnId);
            if(activeBtn) {
                activeBtn.classList.remove('text-slate-500', 'font-normal');
                activeBtn.classList.add('bg-white', 'text-primary-700', 'shadow-sm', 'font-semibold');
            }
            
            renderTable();
        }

        function renderTable() {
            const allRows = Array.from(document.querySelectorAll('.rekap-row'));
            const matchedRows = allRows.filter(row => row.getAttribute('data-metode') === currentMetode);
            
            const totalRows = matchedRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));
            
            // Ensure currentPage is within bounds
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            
            // Hide all rows first
            allRows.forEach(row => { row.style.display = 'none'; });
            
            // Show only the matched rows for the current page
            matchedRows.forEach((row, index) => {
                if (index >= startIndex && index < endIndex) {
                    row.style.display = '';
                    row.querySelector('.row-number').innerText = index + 1;
                }
            });
            
            // Handle Empty state
            const noMatchRow = document.getElementById('no-match-row');
            if (noMatchRow) {
                noMatchRow.style.display = totalRows === 0 ? '' : 'none';
            }
            
            // Handle Empty rows padding to maintain "5 field paten" height
            const currentVisibleCount = matchedRows.slice(startIndex, endIndex).length;
            let padCount = 0;
            if (totalRows > 0 && currentVisibleCount < rowsPerPage) {
                padCount = rowsPerPage - currentVisibleCount;
            } else if (totalRows === 0) {
                // If completely empty, pad 4 rows (1 is taken by noMatchRow)
                padCount = rowsPerPage - 1;
            }
            
            renderPaddingRows(padCount);
            renderPagination(totalPages);
        }

        function renderPaddingRows(count) {
            document.querySelectorAll('.pad-row').forEach(row => row.remove());
            if (count <= 0) return;
            
            const tbody = document.querySelector('.table-body');
            for (let i = 0; i < count; i++) {
                const tr = document.createElement('tr');
                tr.className = 'pad-row border-t border-slate-50 opacity-50';
                tr.innerHTML = `
                    <td class="px-6 py-4"><div class="h-4 bg-slate-100 rounded animate-pulse w-6 mx-auto"></div></td>
                    <td class="px-6 py-4"><div class="h-4 bg-slate-100 rounded animate-pulse w-3/4"></div></td>
                    <td class="px-6 py-4"><div class="h-6 bg-slate-100 rounded animate-pulse w-24"></div></td>
                    <td class="px-6 py-4"><div class="h-4 bg-slate-100 rounded animate-pulse w-1/2"></div></td>
                    <td class="px-6 py-4"><div class="h-4 bg-slate-100 rounded animate-pulse w-12 mx-auto"></div></td>
                    <td class="px-6 py-4"><div class="h-4 bg-slate-100 rounded animate-pulse w-5/6"></div></td>
                `;
                tbody.appendChild(tr);
            }
        }

        function renderPagination(totalPages) {
            const pagContainer = document.getElementById('pagination-container');
            
            if (totalPages <= 1) {
                pagContainer.style.display = 'none';
                return;
            }
            
            pagContainer.style.display = 'flex';
            
            let html = `
                <div class="w-full flex items-center justify-between px-6 py-4">
                    <span class="text-sm font-medium text-slate-500">Halaman ${currentPage} dari ${totalPages}</span>
                    <div class="flex gap-2">
                        <button onclick="changePage(-1)" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage === 1 ? 'disabled' : ''}>Sebelumnya</button>
                        <button onclick="changePage(1)" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage === totalPages ? 'disabled' : ''}>Selanjutnya</button>
                    </div>
                </div>
            `;
            pagContainer.innerHTML = html;
        }

        window.changePage = function(delta) {
            currentPage += delta;
            renderTable();
        };

        // Initialize state on load
        document.addEventListener('DOMContentLoaded', () => {
            filterRiwayat('Cascading Hybrid');
        });
    </script>
@endsection
