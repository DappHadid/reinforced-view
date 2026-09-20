@extends('layouts.app')

@section('title', 'Cari Rekomendasi | REINFORCED')
@section('page-title', 'Cari Rekomendasi')
@section('page-subtitle', 'Temukan kandidat kolaborator penelitian berdasarkan jaringan &amp; kemiripan topik')

@section('content')

    {{-- ============ SEARCH FORM ============ --}}
    <section class="rounded-2xl bg-white border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col lg:flex-row gap-8 lg:items-center">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <h2 class="text-2xl font-bold text-slate-900">Cari Rekomendasi</h2>
                </div>

                <form action="{{ route('rekomendasi') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-5 items-end">
                    <div class="sm:col-span-2 lg:col-span-6">
                        <label class="block text-base font-semibold text-slate-700 mb-2">Nama Peneliti Target</label>
                        <input type="hidden" name="name" id="recommendation-name" value="{{ $currentName }}">
                        <x-search-bar 
                            id="recommendation-search-open" 
                            labelId="recommendation-search-label"
                            :currentName="$currentName" 
                        />
                    </div>

                    <div class="lg:col-span-3">
                        <label class="block text-base font-semibold text-slate-700 mb-2">Metode Algoritma</label>
                        <div class="flex rounded-xl border border-slate-300 bg-slate-50 p-1 font-medium h-[50px]">
                            <label class="cursor-pointer flex-1 h-full flex items-center justify-center">
                                <input type="radio" name="use_cascading" value="false" class="peer sr-only" {{ !$useCascading ? 'checked' : '' }}>
                                <span class="flex h-full w-full items-center justify-center px-2 rounded-lg text-xs sm:text-base text-slate-500 peer-checked:bg-white peer-checked:text-primary-700 peer-checked:shadow-sm transition-all whitespace-nowrap">Standar</span>
                            </label>
                            <label class="cursor-pointer flex-1 h-full flex items-center justify-center">
                                <input type="radio" name="use_cascading" value="true" class="peer sr-only" {{ $useCascading ? 'checked' : '' }}>
                                <span class="flex h-full w-full items-center justify-center px-2 rounded-lg text-xs sm:text-base text-slate-500 peer-checked:bg-white peer-checked:text-primary-700 peer-checked:shadow-sm transition-all whitespace-nowrap">Cascading Hybrid</span>
                            </label>
                        </div>
                    </div>

                    <div class="lg:col-span-3">
                        <button type="submit" class="flex w-full h-[50px] items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 active:bg-primary-800 px-4 py-3 text-sm font-bold text-white shadow-md shadow-primary-600/20 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Cari Rekomendasi
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="hidden lg:flex w-48 xl:w-56 shrink-0 items-center justify-center self-stretch">
                <img src="{{ asset('images/Connected-rafiki.svg') }}" alt="Ilustrasi jaringan kolaborasi" class="w-full object-contain" style="mix-blend-mode: multiply;">
            </div>
        </div>
    </section>

    @push('modals')
    <div id="recommendation-search-modal" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-label="Cari peneliti">
        <div id="recommendation-search-backdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity"></div>
        <div id="recommendation-search-box" class="flex flex-col relative w-full max-w-2xl max-h-full min-h-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl opacity-0 translate-y-4 transition-all">
            <div class="flex items-center gap-4 border-b border-slate-100 px-6 py-5 shrink-0 bg-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input id="recommendation-search-input" type="search" placeholder="Cari nama peneliti..." class="min-w-0 flex-1 border-0 p-0 text-lg font-medium text-slate-800 outline-none ring-0 placeholder:text-slate-400 bg-transparent">
                <button type="button" id="recommendation-search-close" class="rounded-md px-3 py-1.5 text-sm font-semibold text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors shrink-0">Esc</button>
            </div>
            <div id="recommendation-search-results" class="flex-1 overflow-y-auto overscroll-contain bg-white min-h-0"></div>
        </div>
    </div>
    @endpush

    @if($currentName !== '')
        {{-- ============ GRAPH ============ --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-primary-50 text-primary-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Visualisasi Jaringan Rekomendasi</h2>
                        <p class="text-sm font-normal text-slate-400">Jalur kolaborasi untuk {{ strtoupper($currentName) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Legend --}}
                    <div class="hidden sm:flex items-center gap-3 text-xs font-normal text-slate-500">
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#2563eb]"></span>Target</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#f59e0b]"></span>Rekomendasi</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#10b981]"></span>Penghubung</span>
                    </div>
                    {{-- Zoom Controls --}}
                    <div class="flex items-center gap-1 shrink-0">
                        <button id="graph-zoom-in" title="Perbesar" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <button id="graph-zoom-out" title="Perkecil" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <button id="graph-reset" title="Fokus Ulang" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="relative min-h-[460px] bg-white overflow-hidden">
                <div id="network-graph" class="absolute inset-0"></div>

                {{-- Floating Node Info Popup --}}
                <div id="node-popup" class="fixed z-50 hidden pointer-events-none" style="min-width:220px;max-width:280px;">
                    <div class="pointer-events-auto bg-white rounded-2xl border border-slate-200 overflow-hidden" style="box-shadow:0 8px 32px rgba(0,0,0,0.13),0 1.5px 6px rgba(0,0,0,0.07);">
                        {{-- Header --}}
                        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-100">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div id="np-initials" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-xs font-bold"></div>
                                <div class="min-w-0">
                                    <p id="np-name" class="text-sm font-semibold text-slate-900 truncate leading-tight"></p>
                                    <p id="np-role" class="text-xs text-slate-400 font-normal">Peneliti</p>
                                </div>
                            </div>
                            <button type="button" id="np-close" class="shrink-0 rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        {{-- Stats --}}
                        <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
                            <div class="flex flex-col items-center justify-center py-3 px-1">
                                <p id="np-hindex" class="text-lg font-bold text-primary-600 leading-none">-</p>
                                <p class="text-[10px] font-normal text-slate-400 mt-1 text-center">H-Index</p>
                            </div>
                            <div class="flex flex-col items-center justify-center py-3 px-1">
                                <p id="np-pub" class="text-lg font-bold text-slate-800 leading-none">-</p>
                                <p class="text-[10px] font-normal text-slate-400 mt-1 text-center">Publikasi</p>
                            </div>
                            <div class="flex flex-col items-center justify-center py-3 px-1">
                                <p id="np-score" class="text-lg font-bold text-amber-600 leading-none">-</p>
                                <p class="text-[10px] font-normal text-slate-400 mt-1 text-center">Skor ANE</p>
                            </div>
                        </div>
                        {{-- Department --}}
                        <div class="px-4 py-2.5">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-0.5">Program Studi</p>
                            <p id="np-dept" class="text-xs font-medium text-slate-700 leading-snug">-</p>
                        </div>
                        {{-- Action --}}
                        <div class="px-4 pb-3">
                            <a id="np-link" href="#" class="flex items-center justify-center gap-1.5 w-full rounded-xl bg-primary-600 hover:bg-primary-700 px-3 py-2 text-xs font-semibold text-white transition-colors">
                                Lihat Profil
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ RECOMMENDATION TABLE ============ --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-xl font-semibold text-slate-900">Top {{ count($rekomendasi) }} Rekomendasi Kolaborator</h2>
                <p class="text-xs font-normal text-slate-400">Untuk {{ strtoupper($currentName) }} &middot; Metode {{ $useCascading ? 'Cascading Hybrid' : 'Standar' }}</p>
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
                    <div class="group relative px-6 py-5 hover:bg-slate-50/60 transition-colors flex flex-col xl:flex-row xl:items-center justify-between gap-6 overflow-hidden">
                        {{-- Background highlight on hover --}}
                        <div class="absolute inset-y-0 left-0 w-1 bg-primary-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        {{-- Left: Profile info & Score --}}
                        <div class="flex items-start sm:items-center gap-4 sm:gap-5 min-w-0">
                            <div class="relative shrink-0 mt-1 sm:mt-0">
                                <div class="h-14 w-14 sm:h-16 sm:w-16 overflow-hidden rounded-full border-2 border-white shadow-sm ring-1 ring-slate-200 group-hover:ring-primary-200 transition-colors bg-slate-100">
                                    <img src="{{ asset($r['meta']['avatar_image'] ?? 'images/avatar.jpg') }}" alt="{{ $r['Rekomendasi_Nama'] }}" class="h-full w-full object-cover object-[center_15%]" onerror="this.onerror=null;this.src='{{ asset('images/avatar.jpg') }}';">
                                    <span class="hidden h-full w-full items-center justify-center bg-primary-100 text-sm font-bold text-primary-700">{{ $r['meta']['initials'] ?? strtoupper(substr($r['Rekomendasi_Nama'], 0, 2)) }}</span>
                                </div>
                                {{-- Small badge for rank --}}
                                <div class="absolute -bottom-1 -right-1 flex h-6 w-6 sm:h-7 sm:w-7 items-center justify-center rounded-full bg-slate-800 border-2 border-white text-[10px] sm:text-xs font-bold text-white shadow-sm">
                                    #{{ $i + 1 }}
                                </div>
                            </div>
                            
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('dosen.show', $r['Rekomendasi_SINTA_ID']) }}" class="block text-base sm:text-lg font-bold text-slate-900 group-hover:text-primary-700 transition-colors truncate">
                                    {{ $r['Rekomendasi_Nama'] }}
                                </a>
                                <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500">
                                    <span class="flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-md text-slate-600 font-medium">SINTA {{ $r['Rekomendasi_SINTA_ID'] }}</span>
                                </div>
                                {{-- Mini progress bar for score --}}
                                <div class="mt-2.5 flex items-center gap-3">
                                    <div class="h-2 w-24 sm:w-32 rounded-full bg-orange-100 overflow-hidden shadow-inner shrink-0">
                                        <div class="h-full rounded-full bg-orange-500 transition-all duration-1000 relative overflow-hidden" style="width: {{ $pct }}%">
                                            <div class="absolute inset-0 bg-white/20 w-full animate-[shimmer_2s_infinite]"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-extrabold text-orange-600 tracking-tight">{{ $pct }}% <span class="font-semibold text-orange-500/80 text-xs">Kemiripan</span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Middle/Right: Stats & Actions --}}
                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-6 sm:gap-10 xl:pl-6 xl:border-l xl:border-slate-100">
                            
                            {{-- Stats --}}
                            <div class="flex items-center divide-x divide-slate-200 shrink-0">
                                <div class="pr-5 text-center">
                                    <p class="text-xl font-bold text-slate-800 tracking-tight">{{ $stat['ns0__hasHIndexScholar'] ?? '-' }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">H-Index</p>
                                </div>
                                <div class="px-5 text-center">
                                    <p class="text-xl font-bold text-slate-800 tracking-tight">{{ $stat['ns0__hasCollaborator'] ?? '-' }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">Rekan</p>
                                </div>
                                <div class="pl-5 text-center">
                                    <p class="text-xl font-bold text-slate-800 tracking-tight">{{ count($pubs) }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">Publikasi</p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2.5 shrink-0 ml-auto sm:ml-0">
                                <button type="button" onclick="document.getElementById('{{ $modalId }}').showModal()"
                                    class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-full bg-white border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Detail
                                </button>
                                
                                @php
                                    $rekNameLower = strtolower(trim($r['Rekomendasi_Nama']));
                                    $hasEvaluatedThis = in_array($rekNameLower, $evaluatedRekomendasi);
                                @endphp
                                
                                @if($hasEvaluatedThis)
                                    <button type="button" disabled
                                        class="inline-flex items-center gap-1.5 h-9 px-4 rounded-full bg-emerald-50 border border-emerald-100 text-xs font-bold text-emerald-600 cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                        Dinilai
                                    </button>
                                @else
                                    <button type="button" onclick="document.getElementById('rating-{{ $modalId }}').showModal()"
                                        class="inline-flex items-center gap-1.5 h-9 px-5 rounded-full bg-primary-600 text-xs font-bold text-white shadow-sm hover:bg-primary-700 hover:shadow transition-all group-hover:scale-105">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        Beri Nilai
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ===== Modal: Detail Publikasi ===== --}}
                    <dialog id="{{ $modalId }}" class="m-auto w-[calc(100%-2rem)] max-w-2xl rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-900/50">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2"><img src="{{ asset($r['meta']['avatar_image'] ?? 'images/avatar.jpg') }}" alt="" class="h-8 w-8 rounded-full object-cover" onerror="this.onerror=null;this.src='{{ asset('images/avatar.jpg') }}';"><h3 class="text-base font-semibold text-slate-900">{{ $r['Rekomendasi_Nama'] }}</h3></div>
                                <p class="text-xs font-normal text-slate-400">Daftar Publikasi &amp; Perbandingan Atribut</p>
                            </div>
                            <button type="button" onclick="document.getElementById('{{ $modalId }}').close()" class="text-slate-400 hover:text-slate-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <div class="px-6 py-4 max-h-96 overflow-y-auto space-y-4">
                            <div>
                                <p class="text-xs font-normal text-slate-500 mb-2">Perbandingan Atribut</p>
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div class="rounded-lg bg-slate-50 py-2">
                                        <p class="text-base font-semibold text-slate-800">{{ $stat['ns0__hasHIndexScholar'] ?? '-' }}/{{ $stat['ns0__hasHIndexScopus'] ?? '-' }}/{{ $stat['ns0__hasHIndexWos'] ?? '-' }}</p>
                                        <p class="text-xs font-normal text-slate-400">H-Index (Sch/Scp/WoS)</p>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 py-2">
                                        <p class="text-base font-semibold text-slate-800">{{ $stat['ns0__hasPublicationScholar'] ?? '-' }}</p>
                                        <p class="text-xs font-normal text-slate-400">Total Publikasi</p>
                                    </div>
                                    <div class="rounded-lg bg-slate-50 py-2">
                                        <p class="text-base font-semibold text-slate-800">{{ $stat['ns0__hasDepartment'] ?? '-' }}</p>
                                        <p class="text-xs font-normal text-slate-400">Departemen</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-500 mb-2">Judul Publikasi ({{ count($pubs) }})</p>
                                <ul class="space-y-1.5">
                                    @foreach($pubs as $judul)
                                        <li class="text-base text-slate-600 flex items-start gap-2">
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
                    <dialog id="rating-{{ $modalId }}" class="m-auto rounded-2xl border border-slate-200 shadow-xl p-0 w-full max-w-lg backdrop:bg-slate-900/50">
                        <form action="{{ route('rekomendasi.penilaian') }}" method="POST">
                            @csrf
                            <input type="hidden" name="rekomendasi_sinta_id" value="{{ $r['Rekomendasi_SINTA_ID'] }}">
                            <input type="hidden" name="rekomendasi_nama" value="{{ $r['Rekomendasi_Nama'] }}">
                            <input type="hidden" name="name" value="{{ $currentName }}">
                            <input type="hidden" name="use_cascading" value="{{ $useCascading ? 'true' : 'false' }}">

                            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-slate-900">Nilai Rekomendasi</h3>
                                    <p class="text-xs text-slate-400">{{ $r['Rekomendasi_Nama'] }}</p>
                                </div>
                                <button type="button" onclick="document.getElementById('rating-{{ $modalId }}').close()" class="text-slate-400 hover:text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>

                            <div class="px-6 py-4 space-y-4">
                                <div>
                                    <label class="block text-base font-semibold text-slate-500 mb-2">Rating Kualitas Rekomendasi</label>
                                    <div class="flex items-center flex-row-reverse justify-end gap-2 [&>label:hover]:text-amber-300 [&>label:hover~label]:text-amber-300">
                                        @for($star = 5; $star >= 1; $star--)
                                            <input type="radio" id="star-{{ $modalId }}-{{ $star }}" name="rating" value="{{ $star }}" class="peer sr-only" {{ $star === 5 ? 'checked' : '' }} required>
                                            <label for="star-{{ $modalId }}-{{ $star }}" class="cursor-pointer text-slate-200 peer-checked:text-amber-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-base font-semibold text-slate-500 mb-1.5">Komentar (opsional)</label>
                                    <textarea name="komentar" rows="3" placeholder="Tulis catatan mengenai relevansi rekomendasi ini..."
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-base text-slate-800 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 focus:outline-none transition"></textarea>
                                </div>
                            </div>

                            <div class="px-6 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                                <button type="button" onclick="document.getElementById('rating-{{ $modalId }}').close()" class="rounded-lg px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-50">Batal</button>
                                <button type="submit" class="rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2 text-xs font-semibold text-white transition-colors">Simpan Penilaian</button>
                            </div>
                        </form>
                    </dialog>
                @empty
                    <div class="px-6 py-10 text-center text-base text-slate-400">
                        Tidak ditemukan rekomendasi untuk peneliti ini.
                    </div>
                @endforelse
            </div>
        </section>
    @else
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-10 text-center">
            <p class="text-base text-slate-400">Pilih peneliti target di atas untuk melihat rekomendasi kolaborator.</p>
        </section>
    @endif

@endsection

@push('scripts')
<script>
    const graphData = @json($graphData ?? ['nodes' => [], 'edges' => []]);
    const dosenBaseUrl = @json(url('/dosen'));

    // ── Color helpers ─────────────────────────────────────────────────────────
    function groupColor(group) {
        switch (group) {
            case 'target':
                return { background: '#2563eb', border: '#1d4ed8', highlight: { background: '#3b82f6', border: '#1d4ed8' }, hover: { background: '#3b82f6', border: '#1d4ed8' } };
            case 'recommendation':
                return { background: '#f59e0b', border: '#d97706', highlight: { background: '#fbbf24', border: '#d97706' }, hover: { background: '#fbbf24', border: '#d97706' } };
            default:
                return { background: '#10b981', border: '#059669', highlight: { background: '#34d399', border: '#059669' }, hover: { background: '#34d399', border: '#059669' } };
        }
    }
    function groupRole(group) {
        switch (group) {
            case 'target':         return 'Peneliti Target';
            case 'recommendation': return 'Kandidat Rekomendasi';
            default:               return 'Penghubung Kolaborasi';
        }
    }
    function initialsColor(group) {
        switch (group) {
            case 'target':         return 'bg-primary-100 text-primary-700';
            case 'recommendation': return 'bg-amber-100 text-amber-700';
            default:               return 'bg-emerald-100 text-emerald-700';
        }
    }

    const graphContainerEl = document.getElementById('network-graph');

    // ── Floating Node Popup ───────────────────────────────────────────────────
    const nodePopup  = document.getElementById('node-popup');
    const npName     = document.getElementById('np-name');
    const npRole     = document.getElementById('np-role');
    const npInitials = document.getElementById('np-initials');
    const npHindex   = document.getElementById('np-hindex');
    const npPub      = document.getElementById('np-pub');
    const npScore    = document.getElementById('np-score');
    const npDept     = document.getElementById('np-dept');
    const npLink     = document.getElementById('np-link');
    const npClose    = document.getElementById('np-close');

    function showNodePopup(node, canvasX, canvasY) {
        const name     = node.label || 'Peneliti';
        const initials = name.split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
        const cls      = initialsColor(node.group);

        npInitials.className   = `flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-xs font-bold ${cls}`;
        npInitials.textContent = initials;
        npName.textContent     = name;
        npRole.textContent     = groupRole(node.group);
        npHindex.textContent   = node.h_index != null ? node.h_index : '-';
        npPub.textContent      = node.publication_count != null ? node.publication_count : '-';
        npScore.textContent    = node.ane_score != null ? Number(node.ane_score).toFixed(2) : '-';
        npDept.textContent     = node.department || '-';
        npLink.href            = node.sintaId ? `${dosenBaseUrl}/${node.sintaId}` : '#';

        nodePopup.classList.remove('hidden');
        nodePopup.style.visibility = 'hidden';
        nodePopup.style.transition = 'none';

        const rect   = graphContainerEl.getBoundingClientRect();
        const pw     = nodePopup.offsetWidth  || 260;
        const ph     = nodePopup.offsetHeight || 200;
        const margin = 12;

        let left = rect.left + canvasX + margin;
        let top  = rect.top  + canvasY - ph / 2;

        if (left + pw > window.innerWidth  - margin) left = rect.left + canvasX - pw - margin;
        if (left < margin)                            left = margin;
        if (top  < margin)                            top  = margin;
        if (top  + ph > window.innerHeight - margin)  top  = window.innerHeight - ph - margin;

        nodePopup.style.left       = left + 'px';
        nodePopup.style.top        = top  + 'px';
        nodePopup.style.visibility = 'visible';
        nodePopup.style.opacity    = '0';
        nodePopup.style.transform  = 'scale(0.95)';
        nodePopup.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
        requestAnimationFrame(() => {
            nodePopup.style.opacity   = '1';
            nodePopup.style.transform = 'scale(1)';
        });
    }

    function hideNodePopup() {
        if (!nodePopup) return;
        nodePopup.style.opacity   = '0';
        nodePopup.style.transform = 'scale(0.95)';
        setTimeout(() => nodePopup.classList.add('hidden'), 180);
    }

    npClose?.addEventListener('click', hideNodePopup);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') hideNodePopup(); });

    // ── Build vis-network ─────────────────────────────────────────────────────
    if (graphContainerEl && graphData.nodes && graphData.nodes.length > 0) {

        const visNodes = new vis.DataSet(graphData.nodes.map(n => ({
            id:                n.id,
            label:             n.label,
            group:             n.group,
            sintaId:           n.sinta_id ?? n.id,
            department:        n.department || '-',
            h_index:           n.h_index   ?? null,
            publication_count: n.publication_count ?? null,
            ane_score:         n.ane_score ?? null,
            color:             groupColor(n.group),
            font: {
                color:       '#0f172a',
                size:        n.group === 'target' ? 12 : 11,
                face:        'Inter, sans-serif',
                strokeWidth: 3,
                strokeColor: '#ffffff',
                vadjust:     -2,
            },
            shape:       n.group === 'target' ? 'star' : 'dot',
            size:        n.group === 'target' ? 22 : (n.group === 'recommendation' ? 15 : 11),
            borderWidth: 2.5,
            borderWidthSelected: 4,
        })));

        const visEdges = new vis.DataSet(graphData.edges.map(e => ({
            from:   e.from,
            to:     e.to,
            label:  e.label,
            arrows: { to: { enabled: true, scaleFactor: 0.55 } },
            color: {
                color:     e.label === 'recommended' ? '#f59e0b' : '#cbd5e1',
                highlight: e.label === 'recommended' ? '#d97706' : '#94a3b8',
                opacity:   0.85,
            },
            font:   { color: '#94a3b8', size: 9, strokeWidth: 0, align: 'middle' },
            width:  e.label === 'recommended' ? 2 : 1.2,
            dashes: e.label === 'recommended' ? [5, 4] : false,
            smooth: { type: 'continuous', roundness: 0.25 },
            hoverWidth: 1.8,
        })));

        const network = new vis.Network(graphContainerEl, { nodes: visNodes, edges: visEdges }, {
            autoResize: true,
            height: '100%',
            width:  '100%',
            physics: {
                barnesHut: {
                    gravitationalConstant: -12000,
                    centralGravity:        0.3,
                    springLength:          140,
                    springConstant:        0.04,
                    damping:               0.15,
                },
                stabilization: { iterations: 180, updateInterval: 25 },
            },
            interaction: {
                hover:        true,
                zoomView:     true,
                dragView:     true,
                zoomSpeed:    0.4,
                tooltipDelay: 999999,
            },
            nodes: { shadow: false },
            edges: { shadow: false },
        });

        network.once('stabilizationIterationsDone', () => {
            network.fit({ animation: { duration: 600, easingFunction: 'easeInOutQuad' } });
        });

        network.on('click', params => {
            if (params.nodes && params.nodes.length > 0) {
                const node   = visNodes.get(params.nodes[0]);
                const domPos = params.event.center;
                if (node) showNodePopup(node, domPos.x, domPos.y);
            } else {
                hideNodePopup();
            }
        });

        network.on('dragStart', hideNodePopup);
        network.on('zoom',      hideNodePopup);

        document.getElementById('graph-zoom-in')?.addEventListener('click', () => {
            network.moveTo({ scale: network.getScale() * 1.3, animation: { duration: 400, easingFunction: 'easeInOutQuad' } });
        });
        document.getElementById('graph-zoom-out')?.addEventListener('click', () => {
            network.moveTo({ scale: network.getScale() * 0.75, animation: { duration: 400, easingFunction: 'easeInOutQuad' } });
        });
        document.getElementById('graph-reset')?.addEventListener('click', () => {
            network.fit({ animation: { duration: 500, easingFunction: 'easeInOutQuad' } });
        });
    }

    const recommendationDosen = @json($dosenSearch);
    const searchModal = document.getElementById('recommendation-search-modal');
    const searchBackdrop = document.getElementById('recommendation-search-backdrop');
    const searchBox = document.getElementById('recommendation-search-box');
    const searchInput = document.getElementById('recommendation-search-input');
    const searchResults = document.getElementById('recommendation-search-results');
    const openSearch = () => {
        searchModal.classList.remove('hidden'); searchModal.classList.add('flex'); document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => { searchBackdrop.classList.add('opacity-100'); searchBox.classList.remove('opacity-0', 'translate-y-4'); });
        searchInput.value = ''; renderSearch(''); setTimeout(() => searchInput.focus(), 80);
    };
    const closeSearch = () => {
        searchBackdrop.classList.remove('opacity-100'); searchBox.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => { searchModal.classList.add('hidden'); searchModal.classList.remove('flex'); }, 180); document.body.style.overflow = '';
    };
    const escHtml = value => String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    const renderSearch = query => {
        const q = query.trim().toLowerCase();
        // Load ALL matching, or ALL if empty query
        let list = q ? recommendationDosen.filter(d => d.nama.toLowerCase().includes(q)) : [...recommendationDosen];
        
        // Sort alphabetically
        list.sort((a, b) => a.nama.localeCompare(b.nama));

        if (list.length === 0) {
            searchResults.innerHTML = '<p class="px-6 py-12 text-center text-base text-slate-400">Peneliti tidak ditemukan.</p>';
            return;
        }

        // Group by first letter
        const groups = {};
        list.forEach(d => {
            let letter = d.nama.charAt(0).toUpperCase();
            if (!/[A-Z]/.test(letter)) letter = '#';
            if (!groups[letter]) groups[letter] = [];
            groups[letter].push(d);
        });

        let html = '';
        if (q) {
            html += `<p class="px-6 pt-4 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-400 sticky top-0 bg-white/95 backdrop-blur-sm z-10">${list.length} hasil ditemukan</p>`;
        }

        for (const [letter, researchers] of Object.entries(groups)) {
            // Group Header
            html += `<div class="px-6 py-2.5 bg-slate-50/90 border-y border-slate-100 sticky ${q ? 'top-8' : 'top-0'} z-10 flex items-center gap-2 backdrop-blur-md">
                        <span class="flex h-6 w-6 items-center justify-center rounded-md bg-white border border-slate-200 text-xs font-semibold text-primary-600 shadow-sm">${letter}</span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">${researchers.length} Peneliti</span>
                     </div>`;
            
            researchers.forEach(d => {
                html += `<button type="button" data-name="${escHtml(d.nama)}" class="recommendation-result group flex w-full items-center gap-4 px-6 py-3.5 text-left hover:bg-primary-50/50 transition-colors border-b border-slate-50 last:border-0">
                            <img src="/${escHtml(d.avatar_image || 'images/avatar.jpg')}" class="h-12 w-12 rounded-full bg-slate-100 object-cover shadow-sm border border-slate-200" onerror="this.onerror=null;this.src='/images/avatar.jpg'">
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-base font-semibold text-slate-800 group-hover:text-primary-700 transition-colors">${escHtml(d.nama_display)}</span>
                                <span class="block truncate text-xs text-slate-500 mt-0.5">${escHtml(d.prodi)} &middot; SINTA ${escHtml(d.sinta_id)}</span>
                            </span>
                         </button>`;
            });
        }
        
        searchResults.innerHTML = html;
        
        searchResults.querySelectorAll('.recommendation-result').forEach(button => {
            button.addEventListener('click', () => { 
                document.getElementById('recommendation-name').value = button.dataset.name; 
                document.getElementById('recommendation-search-label').textContent = button.dataset.name; 
                document.getElementById('recommendation-search-label').classList.remove('text-slate-400');
                document.getElementById('recommendation-search-label').classList.add('text-slate-800', 'font-semibold');
                closeSearch(); 
            });
        });
    };
    document.getElementById('recommendation-search-open')?.addEventListener('click', openSearch);
    document.getElementById('recommendation-search-close')?.addEventListener('click', closeSearch);
    searchBackdrop?.addEventListener('click', closeSearch);
    searchInput?.addEventListener('input', event => renderSearch(event.target.value));
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && !searchModal.classList.contains('hidden')) closeSearch(); if ((event.key === 'k' && (event.metaKey || event.ctrlKey)) && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) { event.preventDefault(); openSearch(); } });
</script>
@endpush
