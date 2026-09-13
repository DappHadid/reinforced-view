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
                        <p class="text-xs text-slate-400 mt-1">MAP@5</p>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

@endsection
