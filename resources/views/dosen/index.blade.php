@extends('layouts.app')

@section('title', 'Profil Dosen | REINFORCED')
@section('page-title', 'Profil Dosen')
@section('page-subtitle', 'Direktori peneliti terdaftar dalam sistem REINFORCED')

@section('content')

    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <form action="{{ route('dosen.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 sm:items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Cari Nama / SINTA ID</label>
                <input type="text" name="q" value="{{ $query }}" placeholder="Ketik nama peneliti atau SINTA ID..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none transition">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Cari
            </button>
            @if($query !== '')
                <a href="{{ route('dosen.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Daftar Peneliti</h2>
            <span class="text-xs font-semibold text-slate-400">{{ count($dosenList) }} ditemukan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Nama Peneliti</th>
                        <th class="px-6 py-3">SINTA ID</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dosenList as $i => $dosen)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3.5 text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-6 py-3.5 font-medium text-slate-800">{{ $dosen['nama'] }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $dosen['sinta_id'] }}</td>
                            <td class="px-6 py-3.5 text-right">
                                <a href="{{ route('dosen.show', $dosen['sinta_id']) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800">
                                    Lihat Profil
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400">Tidak ada peneliti yang cocok dengan pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

@endsection
