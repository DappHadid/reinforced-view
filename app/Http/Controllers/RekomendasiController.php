<?php

namespace App\Http\Controllers;

use App\Support\ApiDataProvider;
use App\Http\Controllers\DosenController;
use Illuminate\Http\Request;

class RekomendasiController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->query('name', 'KURNIA RAMADHAN PUTRA');
        $useCascading = $request->query('use_cascading', 'true') === 'true';

        $dosenList = ApiDataProvider::dosenList();
        $rekomendasi = [];
        $graphData = ['nodes' => [], 'edges' => []];

        $evaluatedRekomendasi = [];

        $currentMetode = $useCascading ? 'Cascading Hybrid' : 'Standard ANE';

        if ($name !== '') {
            $rekomendasi = ApiDataProvider::rekomendasi($name, $useCascading);
            $dosenById = [];
            foreach ($dosenList as $index => $dosen) {
                $dosenById[(string) ($dosen['sinta_id'] ?? '')] = DosenController::transform($dosen, $index);
            }
            $rekomendasi = array_map(function ($rek, $index) use ($dosenById) {
                $sintaId = (string) ($rek['Rekomendasi_SINTA_ID'] ?? '');
                $rek['meta'] = $dosenById[$sintaId] ?? DosenController::transform([
                    'nama' => $rek['Rekomendasi_Nama'] ?? '',
                    'sinta_id' => $sintaId,
                ], $index);
                return $rek;
            }, $rekomendasi, array_keys($rekomendasi));
            $rekomNames = array_column($rekomendasi, 'Rekomendasi_Nama');
            $graphData = ApiDataProvider::graph($name, $rekomNames, $useCascading);

            // Build stats lookup keyed by sinta_id for graph node popup
            $statsById = [];

            // Target dosen (node 'target') — look up from dosenList by name
            foreach ($dosenList as $d) {
                if (strcasecmp(trim($d['nama'] ?? ''), trim($name)) === 0) {
                    $statsById[(string)($d['sinta_id'] ?? '')] = [
                        'h_index'           => $d['ns0__hasHIndexScholar']      ?? ($d['hasHIndexScholar']      ?? null),
                        'publication_count' => $d['ns0__hasPublicationScholar'] ?? ($d['hasPublicationScholar'] ?? null),
                        'department'        => $dosenById[(string)($d['sinta_id'] ?? '')]['prodi'] ?? null,
                        'ane_score'         => null,
                    ];
                    break;
                }
            }

            // Rekomendasi nodes
            foreach ($rekomendasi as $rek) {
                $sid  = (string)($rek['Rekomendasi_SINTA_ID'] ?? '');
                $stat = $rek['Detail_Statistik'] ?? [];
                $meta = $rek['meta'] ?? [];
                if ($sid !== '') {
                    $statsById[$sid] = [
                        'h_index'           => $stat['ns0__hasHIndexScholar']      ?? ($stat['hasHIndexScholar']      ?? null),
                        'publication_count' => $stat['ns0__hasPublicationScholar'] ?? ($stat['hasPublicationScholar'] ?? null),
                        'department'        => $meta['prodi'] ?? null,
                        'ane_score'         => $rek['Skor Kemiripan'] ?? null,
                    ];
                }
            }

            // Connector nodes from dosenList fallback
            foreach ($dosenById as $sid => $d) {
                if (!isset($statsById[$sid])) {
                    $statsById[$sid] = [
                        'h_index'           => $d['ns0__hasHIndexScholar']      ?? ($d['hasHIndexScholar']      ?? null),
                        'publication_count' => $d['ns0__hasPublicationScholar'] ?? ($d['hasPublicationScholar'] ?? null),
                        'department'        => $d['prodi'] ?? null,
                        'ane_score'         => null,
                    ];
                }
            }

            $graphData = ApiDataProvider::enrichGraphNodes($graphData, $statsById);

            $rekap = ApiDataProvider::penilaianRekap();
            foreach ($rekap as $r) {
                if (strcasecmp(trim($r['nama']), trim($name)) === 0) {
                    if (isset($r['rekomendasi']) && is_array($r['rekomendasi'])) {
                        foreach ($r['rekomendasi'] as $rek) {
                            if (isset($rek['metode']) && $rek['metode'] === $currentMetode) {
                                $evaluatedRekomendasi[] = strtolower(trim($rek['nama']));
                            }
                        }
                    }
                    break;
                }
            }
        }

        return view('rekomendasi', [
            'dosenList' => $dosenList,
            'dosenSearch' => array_map(fn ($dosen, $index) => DosenController::transform($dosen, $index), $dosenList, array_keys($dosenList)),
            'rekomendasi' => $rekomendasi,
            'graphData' => $graphData,
            'currentName' => $name,
            'useCascading' => $useCascading,
            'evaluatedRekomendasi' => $evaluatedRekomendasi,
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
            $metode = (isset($validated['use_cascading']) && $validated['use_cascading'] === 'true') 
                ? 'Cascading Hybrid' 
                : 'Standard ANE';

            $response = \Illuminate\Support\Facades\Http::timeout(10)->post('http://127.0.0.1:8000/api/penilaian', [
                'target_name' => $validated['name'],
                'evaluations' => [
                    [
                        'nama_rekomendasi' => $validated['rekomendasi_nama'],
                        'nilai'            => (int) $validated['rating'],
                    ],
                ],
                'komentar' => $validated['komentar'] ?? '',
                'metode'   => $metode,
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
