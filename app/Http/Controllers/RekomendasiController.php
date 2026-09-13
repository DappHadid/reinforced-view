<?php

namespace App\Http\Controllers;

use App\Support\ApiDataProvider;
use Illuminate\Http\Request;

class RekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->query('name', '');
        $useCascading = $request->query('use_cascading', 'true') === 'true';

        $dosenList = ApiDataProvider::dosenList();
        $rekomendasi = [];
        $graphData = ['nodes' => [], 'edges' => []];

        $hasEvaluated = false;

        if ($name !== '') {
            $rekomendasi = ApiDataProvider::rekomendasi($name, $useCascading);
            $rekomNames = array_column($rekomendasi, 'Rekomendasi_Nama');
            $graphData = ApiDataProvider::graph($name, $rekomNames);

            $rekap = ApiDataProvider::penilaianRekap();
            foreach ($rekap as $r) {
                if (strcasecmp($r['nama'], $name) === 0) {
                    $hasEvaluated = true;
                    break;
                }
            }
        }

        return view('rekomendasi', [
            'dosenList' => $dosenList,
            'rekomendasi' => $rekomendasi,
            'graphData' => $graphData,
            'currentName' => $name,
            'useCascading' => $useCascading,
            'hasEvaluated' => $hasEvaluated,
        ]);
    }

    public function submitPenilaian(Request $request)
    {
        $validated = $request->validate([
            'rekomendasi_sinta_id' => 'required|string',
            'rekomendasi_nama'     => 'required|string',
            'rating'               => 'required|integer|min:1|max:5',
            'komentar'             => 'nullable|string|max:1000',
            'name'                 => 'required|string',
            'use_cascading'        => 'nullable|string',
        ]);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->post('http://127.0.0.1:8000/api/penilaian', [
                'target_name' => $validated['name'],
                'evaluations' => [
                    [
                        'nama_rekomendasi' => $validated['rekomendasi_nama'],
                        'nilai'            => (int) $validated['rating'],
                    ],
                ],
                'komentar' => $validated['komentar'] ?? '',
            ]);

            $message = $response->successful()
                ? "Penilaian untuk {$validated['rekomendasi_nama']} berhasil disimpan."
                : "Penilaian gagal disimpan: " . $response->body();
        } catch (\Throwable $e) {
            $message = "Koneksi ke API gagal: " . $e->getMessage();
        }

        return redirect()
            ->route('rekomendasi', [
                'name'          => $validated['name'],
                'use_cascading' => $validated['use_cascading'] ?? 'true',
            ])
            ->with('status', $message);
    }
}
