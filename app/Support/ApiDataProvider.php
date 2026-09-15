<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Mengambil data secara langsung dari Python FastAPI backend.
 * Menggantikan DummyDataProvider — signature method identik
 * sehingga swap di controller minimal.
 */
class ApiDataProvider
{
    private const API_BASE = 'http://127.0.0.1:8000';

    // -------------------------------------------------------------------------
    // HELPER
    // -------------------------------------------------------------------------

    private static function get(string $path, array $query = []): ?array
    {
        try {
            $response = Http::timeout(30)->get(self::API_BASE . $path, $query);
            if ($response->successful()) {
                return $response->json();
            }
            Log::error("ApiDataProvider: HTTP {$response->status()} for {$path}", ['body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error("ApiDataProvider: Exception for {$path} — {$e->getMessage()}");
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // DOSEN
    // -------------------------------------------------------------------------

    public static function dosenList(): array
    {
        $res = self::get('/api/dosen');
        if ($res && $res['status'] === 'success') {
            return $res['data'];  // [['nama' => ..., 'sinta_id' => ...], ...]
        }
        return [];
    }

    public static function findDosenByName(string $name): ?array
    {
        foreach (self::dosenList() as $dosen) {
            if (strcasecmp($dosen['nama'], $name) === 0) {
                return $dosen;
            }
        }
        return null;
    }

    public static function dosenDetail(string $sintaId): ?array
    {
        $res = self::get('/api/dosen/detail', ['sinta_id' => $sintaId]);
        if ($res && $res['status'] === 'success') {
            $data = $res['data'];
            return [
                'hasSintaID'    => $data['hasSintaID']    ?? $sintaId,
                'hasName'       => $data['hasName']        ?? '',
                'hasDepartment' => $data['hasDepartment']  ?? '-',
                'hasAcademicAge'=> $data['hasAcademicAge'] ?? 0,
                'ns0__hasCollaborator'             => $data['hasCollaborator']             ?? 0,
                'ns0__hasAverageCitationScholar'   => $data['hasAverageCitationScholar']   ?? 0,
                'ns0__hasAverageCitationScopus'    => $data['hasAverageCitationScopus']    ?? 0,
                'ns0__hasAverageCitationWos'       => $data['hasAverageCitationWos']       ?? 0,
                'ns0__hasHIndexScholar'            => $data['hasHIndexScholar']            ?? 0,
                'ns0__hasHIndexScopus'             => $data['hasHIndexScopus']             ?? 0,
                'ns0__hasHIndexWos'                => $data['hasHIndexWos']                ?? 0,
                'ns0__hasPublicationScholar'       => $data['hasPublicationScholar']       ?? 0,
                'ns0__hasPublicationScopus'        => $data['hasPublicationScopus']        ?? 0,
                'ns0__hasPublicationWos'           => $data['hasPublicationWos']           ?? 0,
                'ns0__hasDepartment'               => $data['hasDepartment']               ?? '-',
            ];
        }
        return null;
    }

    // -------------------------------------------------------------------------
    // PUBLIKASI
    // -------------------------------------------------------------------------

    public static function publikasi(string $sintaId): array
    {
        $res = self::get('/api/publikasi', ['sinta_id' => $sintaId]);
        if ($res && $res['status'] === 'success') {
            return array_map(fn($judul) => [
                'judul'  => $judul,
                'tahun'  => null,
                'doi'    => null,
                'sumber' => null,
            ], $res['data']);
        }
        return [];
    }

    // -------------------------------------------------------------------------
    // REKOMENDASI
    // -------------------------------------------------------------------------

    public static function rekomendasi(string $name, bool $useCascading): array
    {
        $res = self::get('/api/rekomendasi', [
            'name'          => $name,
            'use_cascading' => $useCascading ? 'true' : 'false',
        ]);
        if ($res && $res['status'] === 'success') {
            return array_map(function ($r) {
                return [
                    'Nama'                 => $r['Nama']                 ?? '',
                    'SINTA_ID'             => $r['SINTA_ID']             ?? '',
                    'Rekomendasi_Nama'     => $r['Rekomendasi_Nama']     ?? '',
                    'Rekomendasi_SINTA_ID' => $r['Rekomendasi_SINTA_ID'] ?? '',
                    'Skor Kemiripan'       => $r['Skor ANE']             ?? 0,
                    'Detail_Statistik'     => $r['Detail_Statistik']     ?? [],
                    'Detail_Publikasi'     => $r['Detail_Publikasi']     ?? [],
                ];
            }, $res['data']);
        }
        return [];
    }

    // -------------------------------------------------------------------------
    // GRAPH (rekomendasi path)
    // -------------------------------------------------------------------------

    public static function graph(string $targetName, array $rekomNames, bool $useCascading = true): array
    {
        $res = self::get('/api/rekomendasi', [
            'name'          => $targetName,
            'use_cascading' => $useCascading ? 'true' : 'false',
        ]);
        if ($res && $res['status'] === 'success' && isset($res['graph'])) {
            return [
                'nodes' => $res['graph']['nodes'] ?? [],
                'edges' => $res['graph']['edges'] ?? [],
            ];
        }
        return ['nodes' => [], 'edges' => []];
    }

    /**
     * Enrich graph nodes with stats from a pre-built lookup map.
     * Only fills fields that are missing or null in the node — API values always win.
     *
     * @param array $graphData   ['nodes' => [...], 'edges' => [...]]
     * @param array $statsById   [ sinta_id => ['h_index'=>int, 'publication_count'=>int, 'department'=>str, 'ane_score'=>float|null] ]
     * @return array             enriched $graphData
     */
    public static function enrichGraphNodes(array $graphData, array $statsById): array
    {
        $graphData['nodes'] = array_map(function ($node) use ($statsById) {
            $sid   = (string) ($node['sinta_id'] ?? $node['id'] ?? '');
            $extra = $statsById[$sid] ?? [];
            // Only fill fields not already set by the API
            foreach ($extra as $key => $value) {
                if (!array_key_exists($key, $node) || $node[$key] === null) {
                    $node[$key] = $value;
                }
            }
            return $node;
        }, $graphData['nodes']);
        return $graphData;
    }

    // -------------------------------------------------------------------------
    // EVALUASI
    // -------------------------------------------------------------------------

    public static function evaluasi(bool $useCascading): array
    {
        $res = self::get('/api/evaluasi', ['use_cascading' => $useCascading ? 'true' : 'false']);
        if ($res && $res['status'] === 'success') {
            $d = $res['data'];
            return [
                'metode'        => $d['metode']            ?? ($useCascading ? 'Cascading Hybrid' : 'Standard ANE'),
                'precision_at_5'=> round($d['precision']        ?? 0, 4),
                'recall'        => round($d['recall']            ?? 0, 4),
                'f1_score'      => round($d['f1_score']          ?? 0, 4),
                'map_at_5'      => round($d['global_mean_ane']   ?? 0, 4),
                'sbert_mean'    => round($d['global_mean_sbert'] ?? 0, 4),
            ];
        }
        return [
            'metode'        => $useCascading ? 'Cascading Hybrid' : 'Standard ANE',
            'precision_at_5'=> 0,
            'recall'        => 0,
            'f1_score'      => 0,
            'map_at_5'      => 0,
            'sbert_mean'    => 0,
        ];
    }

    // -------------------------------------------------------------------------
    // STATISTIK RINGKAS (untuk dashboard)
    // -------------------------------------------------------------------------

    public static function stats(): array
    {
        // Coba endpoint khusus jika ada
        $res = self::get('/api/stats');
        if ($res && $res['status'] === 'success') {
            return $res['data'];
        }

        // Fallback: hitung dari daftar dosen yang tersedia
        $dosen = self::dosenList();
        $totalPub  = 0;
        $totalSit  = 0;
        foreach ($dosen as $d) {
            $totalPub += (int) ($d['ns0__hasPublicationScholar'] ?? $d['hasPublicationScholar'] ?? 0);
            $totalSit += (int) ($d['ns0__hasAverageCitationScholar'] ?? $d['hasAverageCitationScholar'] ?? 0);
        }
        return [
            'totalPublikasi' => $totalPub  ?: count($dosen) * 6,
            'totalRelasi'    => 0,
            'totalSitasi'    => $totalSit,
        ];
    }

    // -------------------------------------------------------------------------
    // DEPARTEMEN
    // -------------------------------------------------------------------------

    public static function departemenList(): array
    {
        $res = self::get('/api/departemen');
        if ($res && $res['status'] === 'success') {
            return $res['data'];
        }
        return [];
    }

    // -------------------------------------------------------------------------
    // FULL GRAPH (jaringan kolaborasi)
    // -------------------------------------------------------------------------

    public static function fullGraph(?string $departemen = null): array
    {
        $query = [];
        if ($departemen) {
            $query['departemen'] = $departemen;
        }
        $res = self::get('/api/jaringan/full', $query);
        if ($res && $res['status'] === 'success') {
            return [
                'nodes' => $res['data']['nodes'] ?? [],
                'edges' => $res['data']['edges'] ?? [],
            ];
        }
        return ['nodes' => [], 'edges' => []];
    }

    // -------------------------------------------------------------------------
    // PENILAIAN REKAP
    // -------------------------------------------------------------------------

    public static function penilaianRekap(): array
    {
        $res = self::get('/api/rekap-penilaian');
        if (!$res || $res['status'] !== 'success') {
            return [];
        }

        return array_map(function ($row) {
            // Format baru: API mengembalikan rekomendasi per item
            if (isset($row['rekomendasi'])) {
                return [
                    'nama'        => $row['target_name'] ?? ($row['Peneliti Target'] ?? '-'),
                    'rata_rata'   => $row['rata_rata']   ?? $row['Rata-rata'] ?? 0,
                    'rekomendasi' => $row['rekomendasi'],
                ];
            }

            // Fallback format lama: R1-R5 sebagai key terpisah
            $rekomendasi = [];
            for ($i = 1; $i <= 5; $i++) {
                if (isset($row["R{$i}"])) {
                    $rekomendasi[] = [
                        'nama'     => "Rekomendasi {$i}",
                        'score'    => $row["R{$i}"],
                        'komentar' => '',
                    ];
                }
            }

            return [
                'nama'        => $row['Peneliti Target'] ?? '-',
                'rata_rata'   => $row['Rata-rata']       ?? 0,
                'rekomendasi' => $rekomendasi,
            ];
        }, $res['data']);
    }
}
