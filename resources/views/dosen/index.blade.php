@extends('layouts.app')

@section('title', 'Profil Dosen | REINFORCED')
@section('page-title', 'Profil Dosen')

@section('content')

{{-- ═══════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════════ --}}
<section class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
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
                {{ count($dosenList) }} peneliti aktif dari <strong class="text-slate-700">{{ count($fakultasList) }} fakultas</strong>
                dan <strong class="text-slate-700">{{ $totalProdi }} program studi</strong>.
            </p>

            {{-- Search trigger button (command-palette style) --}}
            <button id="search-open-btn"
                    class="mt-6 w-full max-w-[400px] flex items-center gap-3 rounded-xl
                           border-2 border-slate-200 bg-slate-50 hover:border-primary-300
                           hover:bg-white px-4 py-3 text-left transition-all duration-200
                           group focus:outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-50">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 text-slate-400 group-hover:text-primary-500 transition-colors shrink-0"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span class="flex-1 text-[14px] text-slate-400 group-hover:text-slate-500 transition-colors">
                    Cari nama peneliti...
                </span>
                <kbd class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-slate-200
                            bg-white px-2 py-1 text-[10px] font-semibold text-slate-400 shadow-sm">
                    ⌘ K
                </kbd>
            </button>
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
     FILTER BAR
══════════════════════════════════════════════ --}}
<section class="flex flex-wrap items-center gap-3">
    {{-- Fakultas filter --}}
    <div class="relative">
        <select id="filter-fakultas"
                class="appearance-none rounded-xl border border-slate-200 bg-white pl-4 pr-9 py-2.5
                       text-[13px] font-medium text-slate-700 shadow-sm
                       hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-primary-50
                       focus:border-primary-400 transition cursor-pointer">
            <option value="">Semua Fakultas</option>
            @foreach($fakultasList as $fak)
                <option value="{{ $fak }}">{{ $fak }}</option>
            @endforeach
        </select>
        <svg xmlns="http://www.w3.org/2000/svg"
             class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>

    {{-- Prodi filter (cascades from fakultas) --}}
    <div class="relative">
        <select id="filter-prodi"
                class="appearance-none rounded-xl border border-slate-200 bg-white pl-4 pr-9 py-2.5
                       text-[13px] font-medium text-slate-700 shadow-sm
                       hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-primary-50
                       focus:border-primary-400 transition cursor-pointer
                       disabled:opacity-50 disabled:cursor-not-allowed">
            <option value="">Semua Program Studi</option>
            @foreach($prodiByFakultas as $fak => $prodis)
                <optgroup label="{{ $fak }}">
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi }}" data-fakultas="{{ $fak }}">{{ $prodi }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <svg xmlns="http://www.w3.org/2000/svg"
             class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>

    {{-- Reset filter button (hidden when no filter active) --}}
    <button id="reset-filters-btn"
            class="hidden rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[13px]
                   font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition shadow-sm">
        Reset Filter
    </button>

    {{-- Result count --}}
    <div class="ml-auto text-[13px] text-slate-500">
        <span id="result-count" class="font-bold text-slate-900">{{ count($dosenList) }}</span>
        peneliti ditemukan
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
     SEARCH MODAL (Command Palette)
══════════════════════════════════════════════ --}}
<div id="search-modal"
     class="fixed inset-0 z-50 hidden"
     role="dialog" aria-modal="true" aria-label="Cari Peneliti">

    {{-- Backdrop --}}
    <div id="search-backdrop"
         class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200">
    </div>

    {{-- Modal box --}}
    <div class="relative z-10 flex justify-center pt-[6vh] px-4">
        <div id="search-box"
             class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden
                    translate-y-4 opacity-0 transition-all duration-200">

            {{-- Input row --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 shrink-0"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input id="search-modal-input"
                       type="text"
                       autocomplete="off"
                       placeholder="Cari nama peneliti..."
                       class="flex-1 text-[16px] text-slate-800 placeholder-slate-400
                              bg-transparent outline-none">
                <kbd class="hidden sm:inline-flex items-center rounded-lg border border-slate-200
                            bg-slate-50 px-2.5 py-1.5 text-[10px] font-semibold text-slate-400">
                    Esc
                </kbd>
            </div>

            {{-- Results list --}}
            <div id="search-results" class="overflow-y-auto max-h-[520px]">
                {{-- Populated by JS --}}

            {{-- Footer hint --}}
            <div class="px-5 py-2.5 border-t border-slate-100 flex items-center gap-4
                        text-[10px] font-semibold text-slate-400">
                <span class="flex items-center gap-1">
                    <kbd class="rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5">↑↓</kbd>
                    Navigasi
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5">Enter</kbd>
                    Buka
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5">Esc</kbd>
                    Tutup
                </span>
            </div>
        </div>
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
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            backdrop.style.opacity = '1';
            searchBox.style.opacity = '1';
            searchBox.style.transform = 'translateY(0)';
        });
        setTimeout(() => searchInput.focus(), 80);
        renderResults('');
    }

    function closeSearch() {
        backdrop.style.opacity = '0';
        searchBox.style.opacity = '0';
        searchBox.style.transform = 'translateY(1rem)';
        setTimeout(() => {
            modal.classList.add('hidden');
            searchInput.value = '';
            selectedIdx = -1;
        }, 200);
        document.body.style.overflow = '';
    }

    function renderResults(query) {
        const q = query.trim().toLowerCase();
        const list = q === ''
            ? DOSEN_ALL.slice(0, 10)
            : DOSEN_ALL.filter(d => d.nama.toLowerCase().includes(q));

        selectedIdx = -1;

        if (list.length === 0) {
            searchResults.innerHTML = `
                <div class="py-10 text-center">
                    <p class="text-[13px] font-semibold text-slate-500">Tidak ada hasil untuk</p>
                    <p class="text-[13px] text-slate-400 mt-0.5">"${escHtml(query)}"</p>
                </div>`;
            return;
        }

        if (q === '') {
            searchResults.innerHTML = `<p class="px-5 pt-4 pb-2 text-[10px] font-bold tracking-widest uppercase text-slate-400">Semua Peneliti</p>`;
        } else {
            searchResults.innerHTML = `<p class="px-5 pt-4 pb-2 text-[10px] font-bold tracking-widest uppercase text-slate-400">${list.length} hasil ditemukan</p>`;
        }

        const ul = document.createElement('ul');
        list.forEach((d, i) => {
            const li = document.createElement('li');
            li.className = 'search-result-item';
            li.innerHTML = `
                <a href="/dosen/${escHtml(d.sinta_id)}"
                   class="flex items-center gap-3.5 px-5 py-3
                          hover:bg-primary-50 transition-colors group/item">
                    <div class="h-10 w-10 shrink-0 rounded-full overflow-hidden bg-slate-100
                                ring-2 ring-white shadow-sm">
                        <img src="/${escHtml(d.avatar_image)}"
                             alt="${escHtml(d.nama_display)}"
                             class="h-full w-full object-cover object-[center_15%]"
                             onerror="this.style.display='none';
                                      this.nextElementSibling.style.display='flex';">
                        <div style="display:none"
                             class="h-full w-full items-center justify-center bg-primary-100">
                            <span class="text-sm font-extrabold text-primary-700">${escHtml(d.initials)}</span>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[14px] font-semibold text-slate-800 truncate
                                  group-hover/item:text-primary-700 transition-colors">
                            ${highlight(escHtml(d.nama_display), q)}
                        </p>
                        <p class="text-[11px] text-slate-400 truncate">${escHtml(d.prodi)}</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-3.5 w-3.5 text-slate-300 group-hover/item:text-primary-400 shrink-0 transition-colors"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>`;
            ul.appendChild(li);
        });
        searchResults.appendChild(ul);
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

    /* ── Wire up ──────────────────────────────── */
    searchInput.addEventListener('input', e => renderResults(e.target.value));
    document.getElementById('search-open-btn').addEventListener('click', openSearch);
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

    /* Cascade prodi dropdown */
    filterFakultas.addEventListener('change', function () {
        const fak    = this.value;
        const prodis = fak ? (PRODI_BY_FAK[fak] || []) : null;

        // Re-build prodi select options
        filterProdi.innerHTML = '<option value="">Semua Program Studi</option>';
        if (prodis) {
            const grp = document.createElement('optgroup');
            grp.label = fak;
            prodis.forEach(p => {
                const opt    = document.createElement('option');
                opt.value    = p;
                opt.textContent = p;
                grp.appendChild(opt);
            });
            filterProdi.appendChild(grp);
        } else {
            // Show all prodi from all faculties
            Object.entries(PRODI_BY_FAK).forEach(([f, ps]) => {
                const grp = document.createElement('optgroup');
                grp.label = f;
                ps.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p;
                    opt.textContent = p;
                    grp.appendChild(opt);
                });
                filterProdi.appendChild(grp);
            });
        }

        filterProdi.value    = '';
        filterProdi.disabled = false;
        applyFilters();
    });

    filterProdi.addEventListener('change', applyFilters);

    window.resetFilters = function () {
        filterFakultas.value = '';
        filterProdi.value    = '';
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
