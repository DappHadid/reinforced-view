<?php

namespace App\Http\Controllers;

use App\Support\DummyDataProvider;
use Illuminate\Http\Request;

class RekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->query('name', '');
        $useCascading = $request->query('use_cascading', 'true') === 'true';

        $dosenList = DummyDataProvider::dosenList();
        $rekomendasi = [];
        $graphData = ['nodes' => [], 'edges' => []];

        if ($name !== '') {
            $rekomendasi = DummyDataProvider::rekomendasi($name, $useCascading);
            $rekomNames = array_column($rekomendasi, 'Rekomendasi_Nama');
            $graphData = DummyDataProvider::graph($name, $rekomNames);
        }

        return view('rekomendasi', [
            'dosenList' => $dosenList,
            'rekomendasi' => $rekomendasi,
            'graphData' => $graphData,
            'currentName' => $name,
            'useCascading' => $useCascading,
        ]);
    }

    public function submitPenilaian(Request $request)
    {
        $validated = $request->validate([
            'rekomendasi_sinta_id' => 'required|string',
            'rekomendasi_nama' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
            'name' => 'required|string',
            'use_cascading' => 'nullable|string',
        ]);

        return redirect()
            ->route('rekomendasi', [
                'name' => $validated['name'],
                'use_cascading' => $validated['use_cascading'] ?? 'true',
            ])
            ->with('status', "Penilaian untuk {$validated['rekomendasi_nama']} berhasil disimpan (mode demo).");
    }
}
