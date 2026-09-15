@extends('layouts.app')

@section('title', 'Profil Dosen | REINFORCED')
@section('page-title', 'Profil Dosen')

@section('content')

{{-- ═══════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════════ --}}
<section class="rounded-2xl bg-white border border-slate-100 shadow-sm">
    <div class="flex flex-col lg:flex-row items-center gap-6 lg:gap-0 px-8 py-8 lg:py-0 lg:pl-10 lg:pr-0">

        {{-- Left: copy + search trigger --}}
        <div class="flex-1 min-w-0 lg:py-10">
            <p class="text-[11px] font-bold tracking-[0.18em] uppercase text-primary-500 mb-3">
                Direktori Peneliti
            </p>
            <h1 class="text-[28px] font-extrabold text-slate-900 leading-[1.25] tracking-tight">
                Temukan <span class="text-primary-600">Peneliti</span><br>
                REINFORCED
            </h1>
            <p class="mt-3 text-[15px] text-slate-500 leading-relaxed max-w-sm">
                @php 
                    $totalProdi = 0; 
                    foreach($prodiByFakultas as $fak => $prodis) { $totalProdi += count($prodis); } 
                @endphp
                {{ count($dosenList) }} peneliti dari <strong class="text-slate-700">{{ count($fakultasList) }} fakultas</strong>
                dan <strong class="text-slate-700">{{ $totalProdi }} program studi</strong>.
            </p>

            <div class="mt-6 flex flex-col xl:flex-row flex-wrap items-center gap-3">
                {{-- Search trigger button (command-palette style) --}}
                <x-search-bar 
                    id="search-open-btn" 
                    extraClasses="w-full xl:max-w-[320px] !h-10 !py-0 !rounded-md !border-slate-200" 
                />

                {{-- Fakultas filter --}}
                @php
                    $fakOptions = [['value' => '', 'label' => 'Semua Fakultas']];
                    foreach($fakultasList as $fak) {
                        $fakOptions[] = ['value' => $fak, 'label' => $fak];
                    }
                @endphp
                <x-dropdown-select id="filter-fakultas" :options="$fakOptions" placeholder="Semua Fakultas" extraClasses="w-full sm:w-auto min-w-[200px]" />

                {{-- Prodi filter (cascades from fakultas via window event) --}}
                <div x-data='{
                    fak: "",
                    allProdis: @json($prodiByFakultas),
                    get prodiOpts() {
                        let opts = [{ value: "", label: "Semua Program Studi" }];
                        if (this.fak && this.allProdis[this.fak]) {
                            this.allProdis[this.fak].forEach(p => opts.push({ value: p, label: p }));
                        } else {
                            Object.values(this.allProdis).flat().forEach(p => opts.push({ value: p, label: p }));
                        }
                        return opts;
                    }
                }' @@update-prodi-options.window="fak = $event.detail" class="w-full sm:w-auto min-w-[200px]">
                    <x-dropdown-select id="filter-prodi" optionsJs="prodiOpts" placeholder="Semua Program Studi" extraClasses="w-full sm:w-auto min-w-[200px]" />
                </div>

                {{-- Reset filter button (hidden when no filter active) --}}
                <button id="reset-filters-btn"
                        class="hidden rounded-md border border-slate-200 bg-white px-4 py-2 text-sm
                               font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition shadow-sm h-10">
                    Reset
                </button>
            </div>
        </div>

        {{-- Right: decorative SVG --}}
        <div class="shrink-0 w-56 sm:w-64 lg:w-[300px] flex items-end justify-center lg:justify-end
                    self-end lg:self-auto">
            <img src="{{ asset('images/resume-animate.svg') }}"
                 alt="Ilustrasi Peneliti"
                 class="w-full h-auto object-contain max-h-56 lg:max-h-72
                        hover:scale-[1.02] transition-transform duration-500">
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════
     CARD GRID
══════════════════════════════════════════════ --}}
<section id="dosen-grid"
         class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    @foreach($dosenList as $dosen)
        <div class="dosen-card group flex flex-col rounded-2xl bg-white border border-slate-100
                    shadow-sm hover:shadow-md hover:border-slate-200 transition-all duration-200
                    overflow-hidden"
             data-nama="{{ strtolower($dosen['nama']) }}"
             data-prodi="{{ $dosen['prodi'] }}"
             data-fakultas="{{ $dosen['fakultas'] }}">

            {{-- Photo area: full-width, portrait aspect, top-rounded --}}
            <div class="relative overflow-hidden bg-slate-100" style="aspect-ratio: 3/4;">
                <img src="{{ asset($dosen['avatar_image']) }}"
                     alt="Foto {{ $dosen['nama_display'] }}"
                     class="absolute inset-0 w-full h-full object-cover object-[center_15%]
                            transition-transform duration-500 group-hover:scale-[1.04]"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                {{-- Fallback: gradient initials --}}
                <div style="display:none"
                     class="absolute inset-0 items-center justify-center
                            bg-gradient-to-br {{ $dosen['avatar_from'] }} {{ $dosen['avatar_to'] }}">
                    <span class="text-4xl font-extrabold text-white select-none">
                        {{ $dosen['initials'] }}
                    </span>
                </div>

                {{-- Faculty tag on photo --}}
                <div class="absolute bottom-3 left-3">
                    <span class="inline-block rounded-lg bg-white/90 backdrop-blur-sm
                                 px-2.5 py-1 text-[10px] font-semibold text-slate-600 shadow-sm
                                 border border-white/50">
                        {{ Str::limit($dosen['prodi'], 22) }}
                    </span>
                </div>
            </div>

            {{-- Card body: fixed structure — no flex spacer to avoid uneven gap --}}
            <div class="px-4 pt-4 pb-4">
                {{-- Name (min-height so short names don't collapse the row) --}}
                <h3 class="text-[15px] font-bold text-slate-900 leading-snug line-clamp-2
                           min-h-[2.75rem] group-hover:text-primary-700 transition-colors">
                    {{ $dosen['nama_display'] }}
                </h3>
                <p class="mt-1 text-[11px] font-medium text-slate-400 truncate">
                    {{ $dosen['fakultas'] }}
                </p>

                {{-- Divider + link: always fixed margin from text --}}
                <div class="border-t border-slate-100 mt-4 pt-3">
                    <a href="{{ route('dosen.show', $dosen['sinta_id']) }}"
                       class="inline-flex items-center gap-2 text-[11px] font-bold
                              tracking-widest uppercase text-slate-400
                              hover:text-primary-600 transition-colors group/lnk">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-3.5 w-3.5 group-hover/lnk:translate-x-0.5 transition-transform"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                        Lihat Profil
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</section>

{{-- Empty state (shown by JS) --}}
<div id="empty-state" class="hidden rounded-2xl bg-white border border-slate-100 p-14 text-center shadow-sm">
    <div class="mx-auto mb-4 h-14 w-14 rounded-2xl bg-slate-100 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-slate-300" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
    </div>
    <p class="text-[15px] font-bold text-slate-700">Tidak ada peneliti ditemukan</p>
    <p class="mt-1.5 text-[13px] text-slate-400">
        Coba ubah filter Fakultas atau Program Studi.
    </p>
    <button onclick="resetFilters()"
            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700
                   px-5 py-2.5 text-[13px] font-semibold text-white shadow-lg shadow-primary-600/25 transition-colors">
        Reset Filter
    </button>
</div>

{{-- ═══════════════════════════════════════════
     SEARCH MODAL
══════════════════════════════════════════════ --}}
<div id="search-modal" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-label="Cari peneliti">
    <div id="search-backdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity"></div>
    <div id="search-box" class="flex flex-col relative w-full max-w-2xl max-h-full min-h-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl opacity-0 translate-y-4 transition-all">
        <div class="flex items-center gap-4 border-b border-slate-100 px-6 py-5 shrink-0 bg-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="search-modal-input" type="text" autocomplete="off" placeholder="Cari nama peneliti..." class="min-w-0 flex-1 border-0 p-0 text-lg font-medium text-slate-800 outline-none ring-0 placeholder:text-slate-400 bg-transparent">
            <button type="button" id="search-modal-close" class="rounded-md px-3 py-1.5 text-sm font-semibold text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors shrink-0">Esc</button>
        </div>
        <div id="search-results" class="flex-1 overflow-y-auto overscroll-contain bg-white min-h-0"></div>
    </div>
</div>

{{-- JSON data for JS --}}
<script type="application/json" id="dosen-json">@json($allDosenJson)</script>
<script type="application/json" id="prodi-json">@json($prodiByFakultas)</script>

<script>
(function () {
    'use strict';

    /* ── Data ─────────────────────────────────────── */
    const DOSEN_ALL    = JSON.parse(document.getElementById('dosen-json').textContent);
    const PRODI_BY_FAK = JSON.parse(document.getElementById('prodi-json').textContent);

    /* ── DOM refs ─────────────────────────────────── */
    const modal          = document.getElementById('search-modal');
    const backdrop       = document.getElementById('search-backdrop');
    const searchBox      = document.getElementById('search-box');
    const searchInput    = document.getElementById('search-modal-input');
    const searchResults  = document.getElementById('search-results');
    const grid           = document.getElementById('dosen-grid');
    const gridCards      = Array.from(document.querySelectorAll('.dosen-card'));
    const countEl        = document.getElementById('result-count');
    const emptyState     = document.getElementById('empty-state');
    const filterFakultas = document.getElementById('filter-fakultas');
    const filterProdi    = document.getElementById('filter-prodi');
    const resetBtn       = document.getElementById('reset-filters-btn');

    /* ══════════════════════════════════════════════════
       SEARCH MODAL
    ══════════════════════════════════════════════════ */
    let selectedIdx = -1;

    function openSearch() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            backdrop.classList.add('opacity-100');
            searchBox.classList.remove('opacity-0', 'translate-y-4');
        });
        setTimeout(() => searchInput.focus(), 80);
        renderResults('');
    }

    function closeSearch() {
        backdrop.classList.remove('opacity-100');
        searchBox.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            searchInput.value = '';
            selectedIdx = -1;
        }, 180);
        document.body.style.overflow = '';
    }

    function renderResults(query) {
        const q = query.trim().toLowerCase();
        let list = q ? DOSEN_ALL.filter(d => d.nama.toLowerCase().includes(q)) : [...DOSEN_ALL];

        // Sort alphabetically
        list.sort((a, b) => a.nama.localeCompare(b.nama));

        selectedIdx = -1;

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
        } else {
            html += `<p class="px-6 pt-4 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-400 sticky top-0 bg-white/95 backdrop-blur-sm z-10">Semua Peneliti</p>`;
        }

        for (const [letter, researchers] of Object.entries(groups)) {
            // Group Header
            html += `<div class="px-6 py-2.5 bg-slate-50/90 border-y border-slate-100 sticky top-8 z-10 flex items-center gap-2 backdrop-blur-md">
                        <span class="flex h-6 w-6 items-center justify-center rounded-md bg-white border border-slate-200 text-xs font-semibold text-primary-600 shadow-sm">${letter}</span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">${researchers.length} Peneliti</span>
                     </div>`;
            
            researchers.forEach(d => {
                html += `<a href="/dosen/${escHtml(d.sinta_id)}" class="search-result group flex w-full items-center gap-4 px-6 py-3.5 text-left hover:bg-primary-50/50 transition-colors border-b border-slate-50 last:border-0 outline-none focus:bg-primary-50/50">
                            <img src="/${escHtml(d.avatar_image || 'images/avatar.jpg')}" class="h-12 w-12 rounded-full bg-slate-100 object-cover shadow-sm border border-slate-200" onerror="this.onerror=null;this.src='/images/avatar.jpg'">
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-base font-semibold text-slate-800 group-hover:text-primary-700 transition-colors">${highlight(escHtml(d.nama_display), q)}</span>
                                <span class="block truncate text-xs text-slate-500 mt-0.5">${escHtml(d.prodi)}</span>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300 group-hover:text-primary-400 shrink-0 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                         </a>`;
            });
        }
        
        searchResults.innerHTML = html;
    }

    function highlight(text, query) {
        if (!query) return text;
        const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(re, '<mark class="bg-primary-100 text-primary-700 rounded px-0.5">$1</mark>');
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── Keyboard nav inside modal ─────────────── */
    searchInput.addEventListener('keydown', function (e) {
        const items = Array.from(searchResults.querySelectorAll('a'));
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIdx = Math.min(selectedIdx + 1, items.length - 1);
            items[selectedIdx].focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIdx = Math.max(selectedIdx - 1, 0);
            items[selectedIdx].focus();
        } else if (e.key === 'Enter' && items.length > 0) {
            items[Math.max(selectedIdx, 0)].click();
        }
    });

    /* ── Wire up ──────────────────────────────────── */
    searchInput.addEventListener('input', e => renderResults(e.target.value));
    document.getElementById('search-open-btn')?.addEventListener('click', openSearch);
    document.getElementById('search-modal-close')?.addEventListener('click', closeSearch);
    backdrop.addEventListener('click', closeSearch);

    /* ══════════════════════════════════════════════════
       GRID FILTERING (Fakultas + Prodi)
    ══════════════════════════════════════════════════ */
    function applyFilters() {
        const fak  = filterFakultas.value;
        const prod = filterProdi.value;

        let count = 0;
        gridCards.forEach(card => {
            const show = (!fak || card.dataset.fakultas === fak)
                      && (!prod || card.dataset.prodi === prod);
            card.style.display = show ? '' : 'none';
            if (show) count++;
        });

        countEl.textContent = count;
        emptyState.classList.toggle('hidden', count > 0);
        grid.classList.toggle('hidden', count === 0);
        resetBtn.classList.toggle('hidden', !fak && !prod);
    }

    /* Cascade prodi dropdown using Alpine Event */
    filterFakultas.addEventListener('change', function () {
        const fak = this.value;
        filterProdi.value = '';
        filterProdi.dispatchEvent(new Event('change', { bubbles: true }));
        window.dispatchEvent(new CustomEvent('update-prodi-options', { detail: fak }));
        applyFilters();
    });

    filterProdi.addEventListener('change', applyFilters);

    window.resetFilters = function () {
        filterFakultas.value = '';
        filterProdi.value    = '';
        filterFakultas.dispatchEvent(new Event('change', { bubbles: true }));
        filterProdi.dispatchEvent(new Event('change', { bubbles: true }));
        window.dispatchEvent(new CustomEvent('update-prodi-options', { detail: '' }));
        applyFilters();
    };

    resetBtn.addEventListener('click', resetFilters);

    /* ══════════════════════════════════════════════════
       GLOBAL KEYBOARD SHORTCUTS
    ══════════════════════════════════════════════════ */
    document.addEventListener('keydown', function (e) {
        const inInput = ['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName);
        const modalOpen = !modal.classList.contains('hidden');

        if (e.key === 'Escape' && modalOpen) {
            closeSearch();
            return;
        }

        if ((e.key === '/' && !inInput) ||
            (e.key === 'k' && (e.metaKey || e.ctrlKey))) {
            e.preventDefault();
            if (!modalOpen) openSearch();
        }
    });

})();
</script>

@endsection
