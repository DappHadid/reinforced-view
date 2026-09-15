@props([
    'id' => 'search-open-btn',
    'labelId' => 'search-label',
    'currentName' => '',
    'placeholder' => 'Cari nama peneliti...',
    'extraClasses' => ''
])

<button type="button" id="{{ $id }}" class="flex w-full items-center gap-3 rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-left transition hover:border-primary-400 hover:bg-white focus:outline-none focus:ring-4 focus:ring-primary-50 {{ $extraClasses }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <span id="{{ $labelId }}" class="flex-1 truncate font-medium text-slate-{{ $currentName !== '' ? '800' : '400' }}">
        {{ $currentName !== '' ? $currentName : $placeholder }}
    </span>
    <kbd class="hidden rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-semibold text-slate-400 sm:inline">⌘ K</kbd>
</button>
