@extends('layouts.app')

@section('title', 'Tentang | REINFORCED')
@section('page-title', 'Tentang REINFORCED')
@section('page-subtitle', 'Metodologi, ontologi, dan tim riset')

@section('content')

    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-3">Latar Belakang</h2>
        <p class="text-sm text-slate-500 leading-relaxed">
            REINFORCED adalah sistem rekomendasi kolaborator penelitian yang dibangun menggunakan
            <span class="font-semibold text-slate-700">Attributed Network Embedding (ANE)</span> di atas
            <span class="font-semibold text-slate-700">knowledge graph</span> (Neo4j), berbasis ontologi
            <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">ns0__Person</code>,
            <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">ns0__Publication</code>, serta relasi
            <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">collaborateWith</code> /
            <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">hasPublication</code>. Sistem ini
            menggantikan prototipe Streamlit lama dengan antarmuka web berbasis Laravel yang menembak ke
            backend FastAPI permanen.
        </p>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
            <p class="text-xs font-bold text-blue-700 uppercase tracking-wide mb-2">Mode Standar</p>
            <h3 class="text-sm font-bold text-slate-900 mb-2">Berbasis H-Index &amp; Struktur Graf</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                Menentukan kandidat kolaborator berdasarkan kedekatan struktural dalam jaringan kolaborasi
                serta metrik produktivitas ilmiah (H-Index, jumlah publikasi, sitasi) dari Scholar, Scopus,
                dan Web of Science.
            </p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
            <p class="text-xs font-bold text-violet-700 uppercase tracking-wide mb-2">Cascading Hybrid</p>
            <h3 class="text-sm font-bold text-slate-900 mb-2">Menambahkan Kemiripan Topik (S-BERT)</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                Memprioritaskan kandidat dengan topik publikasi yang relevan menggunakan embedding semantik
                S-BERT atas judul publikasi, dikombinasikan secara bertingkat (cascading) dengan hasil mode
                Standar.
            </p>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-4">Sumber Data</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-sm font-bold text-slate-800">SINTA</p>
                <p class="text-xs text-slate-500 mt-1">Science and Technology Index &mdash; identitas &amp; metrik dasar peneliti.</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-sm font-bold text-slate-800">Neo4j Knowledge Graph</p>
                <p class="text-xs text-slate-500 mt-1">Menyimpan entitas peneliti, publikasi, dan relasi kolaborasi.</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-sm font-bold text-slate-800">Ontologi ns0</p>
                <p class="text-xs text-slate-500 mt-1">Skema atribut ns0__Person &amp; ns0__Publication.</p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-4">Tim Riset</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex items-center gap-4 rounded-xl bg-slate-50 p-4">
                <div class="h-12 w-12 shrink-0 rounded-full bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white font-bold">BG</div>
                <div>
                    <p class="text-sm font-bold text-slate-800">Bennart Dem Gunawan</p>
                    <p class="text-xs text-slate-500">Peneliti</p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-xl bg-slate-50 p-4">
                <div class="h-12 w-12 shrink-0 rounded-full bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white font-bold">KR</div>
                <div>
                    <p class="text-sm font-bold text-slate-800">Kurnia Ramadhan Putra</p>
                    <p class="text-xs text-slate-500">Peneliti</p>
                </div>
            </div>
        </div>
    </section>

@endsection
