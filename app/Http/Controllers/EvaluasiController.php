<?php

namespace App\Http\Controllers;

use App\Support\ApiDataProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EvaluasiController extends Controller
{
    public function index()
    {
        return view('evaluasi', [
            'evaluasiStandar' => ApiDataProvider::evaluasi(false),
            'evaluasiHybrid' => ApiDataProvider::evaluasi(true),
            'penilaianRekap' => ApiDataProvider::penilaianRekap(),
        ]);
    }

    public function getTargetDetail(Request $request)
    {
        $name = trim($request->query('name', ''));
        $metode = $request->query('metode', 'Cascading Hybrid');
        $useCascading = ($metode === 'Cascading Hybrid');

        if (empty($name)) {
            return response()->json(['status' => 'error', 'message' => 'Nama tidak boleh kosong'], 400);
        }

        $cacheKey = 'target_detail_' . md5(strtolower($name) . '_' . $metode);
        $data = Cache::remember($cacheKey, 300, function () use ($name, $useCascading, $metode) {
            $modelRekomendasi = ApiDataProvider::rekomendasi($name, $useCascading);
            $allRekap = ApiDataProvider::penilaianRekap();

            $targetReviews = [];
            foreach ($allRekap as $r) {
                if (strcasecmp(trim($r['nama'] ?? ''), trim($name)) === 0) {
                    foreach ($r['rekomendasi'] ?? [] as $rek) {
                        if (($rek['metode'] ?? 'Cascading Hybrid') === $metode) {
                            $targetReviews[strtolower(trim($rek['nama']))] = $rek;
                        }
                    }
                    break;
                }
            }

            $dl = ApiDataProvider::dosenList();
            $dosenMap = [];
            foreach ($dl as $i => $d) {
                $t = DosenController::transform($d, $i);
                $dosenMap[strtolower(trim($t['nama'] ?? ''))] = $t;
            }

            $result = [];
            $seen = [];
            foreach ($modelRekomendasi as $idx => $m) {
                $rekName = trim($m['Rekomendasi_Nama'] ?? '');
                if (!$rekName) continue;
                $lower = strtolower($rekName);
                $seen[$lower] = true;

                $rev = $targetReviews[$lower] ?? null;
                $dInfo = $dosenMap[$lower] ?? null;

                $result[] = [
                    'nama'        => $rekName,
                    'sinta_id'    => $m['Rekomendasi_SINTA_ID'] ?? ($dInfo['sinta_id'] ?? '-'),
                    'prodi'       => $dInfo['prodi'] ?? 'Dosen / Peneliti',
                    'avatar'      => $dInfo['avatar_image'] ?? 'images/avatar.jpg',
                    'model_rank'  => $idx + 1,
                    'model_score' => (float)($m['Skor Kemiripan'] ?? 0),
                    'has_review'  => $rev !== null,
                    'eval_score'  => $rev ? (int)($rev['score'] ?? 0) : 0,
                    'komentar'    => $rev['komentar'] ?? '',
                ];
            }

            // Tambahkan juga rekomendasi yang memiliki ulasan tapi mungkin di luar top 10 model
            foreach ($targetReviews as $lower => $rev) {
                if (!isset($seen[$lower])) {
                    $dInfo = $dosenMap[$lower] ?? null;
                    $result[] = [
                        'nama'        => $rev['nama'] ?? '',
                        'sinta_id'    => $dInfo['sinta_id'] ?? '-',
                        'prodi'       => $dInfo['prodi'] ?? 'Dosen / Peneliti',
                        'avatar'      => $dInfo['avatar_image'] ?? 'images/avatar.jpg',
                        'model_rank'  => count($result) + 1,
                        'model_score' => 0.5,
                        'has_review'  => true,
                        'eval_score'  => (int)($rev['score'] ?? 0),
                        'komentar'    => $rev['komentar'] ?? '',
                    ];
                }
            }

            // Skeleton fallback jika hasil rekomendasi kosong
            if (empty($result)) {
                $sampleDosen = array_slice($dl, 0, 5);
                foreach ($sampleDosen as $idx => $sd) {
                    $t = DosenController::transform($sd, $idx);
                    $result[] = [
                        'nama'        => $t['nama'],
                        'sinta_id'    => $t['sinta_id'],
                        'prodi'       => $t['prodi'],
                        'avatar'      => $t['avatar_image'],
                        'model_rank'  => $idx + 1,
                        'model_score' => round(0.95 - ($idx * 0.05), 3),
                        'has_review'  => false,
                        'eval_score'  => 0,
                        'komentar'    => '',
                    ];
                }
            }

            return $result;
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }
}

