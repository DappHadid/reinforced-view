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
    <div class="mt-12 mb-4">
        <h2 class="text-lg font-bold text-slate-900">Riwayat Penilaian Pengguna</h2>
        <p class="text-sm text-slate-500">Daftar evaluasi kualitas rekomendasi yang diberikan pengguna.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm mb-12">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Metode</th>
                        <th class="px-6 py-4">Target Peneliti</th>
                        <th class="px-6 py-4">Rekomendasi Dinilai</th>
                        <th class="px-6 py-4 text-center">Skor</th>
                        <th class="px-6 py-4">Komentar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penilaianRekap as $rekap)
                        @if(isset($rekap['rekomendasi']) && is_array($rekap['rekomendasi']))
                            @foreach($rekap['rekomendasi'] as $rek)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        @if(($rek['metode'] ?? '') == 'Cascading Hybrid')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Cascading Hybrid</span>
                                        @elseif(($rek['metode'] ?? '') == 'Standard ANE')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Standard ANE</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Cascading Hybrid</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ $rekap['nama'] }}</td>
                                    <td class="px-6 py-4 text-slate-900">{{ $rek['nama'] }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center text-amber-400">
                                            {{ $rek['score'] }}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 max-w-xs truncate" title="{{ $rek['komentar'] ?? '' }}">
                                        {{ !empty($rek['komentar']) ? $rek['komentar'] : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
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
@endsection
