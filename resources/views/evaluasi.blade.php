@extends('layouts.app')

@section('title', 'Hasil Evaluasi | REINFORCED')
@section('page-title', 'Hasil Evaluasi')
@section('page-subtitle', 'Performa model rekomendasi: Standar vs Cascading Hybrid')

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
@endpush

@section('content')

    {{-- ============ METRIC COMPARISON ============ --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach([['data' => $evaluasiStandar, 'badge' => 'text-blue-700 bg-blue-50'], ['data' => $evaluasiHybrid, 'badge' => 'text-violet-700 bg-violet-50']] as $card)
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
                        <p class="text-xs text-slate-400 mt-1">MAP@5</p>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ============ CHART ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-4">Perbandingan Metrik Model</h2>
        <div class="h-72">
            <canvas id="metric-chart"></canvas>
        </div>
    </section>

    {{-- ============ PENILAIAN REKAP ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Rekap Penilaian Pengguna</h2>
            <p class="text-xs text-slate-400">Rating 1-5 dari pengguna terhadap hasil rekomendasi per peneliti</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-3">Nama Peneliti</th>
                        <th class="px-6 py-3 text-center">R1</th>
                        <th class="px-6 py-3 text-center">R2</th>
                        <th class="px-6 py-3 text-center">R3</th>
                        <th class="px-6 py-3 text-center">R4</th>
                        <th class="px-6 py-3 text-center">R5</th>
                        <th class="px-6 py-3 text-center">Rata-rata</th>
                        <th class="px-6 py-3">Komentar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($penilaianRekap as $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3.5 font-medium text-slate-800">{{ $row['nama'] }}</td>
                            @foreach($row['ratings'] as $rating)
                                <td class="px-6 py-3.5 text-center text-slate-500">{{ $rating }}</td>
                            @endforeach
                            <td class="px-6 py-3.5 text-center">
                                <span class="inline-flex items-center rounded-full bg-amber-50 text-amber-700 px-2.5 py-1 text-xs font-semibold">{{ $row['rata_rata'] }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500 max-w-xs truncate">{{ $row['komentar'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    const standar = @json($evaluasiStandar);
    const hybrid = @json($evaluasiHybrid);

    const ctx = document.getElementById('metric-chart');
    if (ctx && window.Chart) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Precision@5', 'Recall', 'F1 Score', 'MAP@5'],
                datasets: [
                    {
                        label: standar.metode,
                        data: [standar.precision_at_5, standar.recall, standar.f1_score, standar.map_at_5].map(v => v * 100),
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                    },
                    {
                        label: hybrid.metode,
                        data: [hybrid.precision_at_5, hybrid.recall, hybrid.f1_score, hybrid.map_at_5].map(v => v * 100),
                        backgroundColor: '#8b5cf6',
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: (v) => v + '%' } },
                },
                plugins: {
                    legend: { position: 'bottom' },
                },
            },
        });
    }
</script>
@endpush
