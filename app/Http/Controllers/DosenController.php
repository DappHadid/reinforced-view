<?php

namespace App\Http\Controllers;

use App\Support\DummyDataProvider;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $dosenList      = DummyDataProvider::dosenListWithMeta();
        $fakultasList   = DummyDataProvider::fakultasList();
        $prodiByFakultas = DummyDataProvider::prodiByFakultas();

        usort($dosenList, fn ($a, $b) => strcmp($a['nama'], $b['nama']));

        // Slim JSON for search modal (only fields needed by JS)
        $allDosenJson = array_map(fn ($d) => [
            'nama'         => $d['nama'],
            'nama_display' => $d['nama_display'],
            'sinta_id'     => $d['sinta_id'],
            'prodi'        => $d['prodi'],
            'fakultas'     => $d['fakultas'],
            'initials'     => $d['initials'],
            'avatar_image' => $d['avatar_image'],
        ], $dosenList);

        return view('dosen.index', [
            'dosenList'       => $dosenList,
            'fakultasList'    => $fakultasList,
            'prodiByFakultas' => $prodiByFakultas,
            'allDosenJson'    => $allDosenJson,
        ]);
    }

    public function show(string $sintaId)
    {
        $detail = DummyDataProvider::dosenDetail($sintaId);

        abort_if($detail === null, 404);

        $publikasi   = DummyDataProvider::publikasi($sintaId);
        $rekomendasi = DummyDataProvider::rekomendasi($detail['hasName'], true);

        // Enrich recommendation candidates with full metadata (avatar, prodi, etc.)
        $dosenList = DummyDataProvider::dosenListWithMeta();
        $dosenMetaMap = [];
        foreach ($dosenList as $d) {
            $dosenMetaMap[$d['sinta_id']] = $d;
        }

        foreach ($rekomendasi as &$rek) {
            $sid = $rek['Rekomendasi_SINTA_ID'];
            if (isset($dosenMetaMap[$sid])) {
                $rek['meta'] = $dosenMetaMap[$sid];
            } else {
                $rek['meta'] = [
                    'prodi'        => 'Teknik Informatika',
                    'fakultas'     => 'Fakultas Ilmu Komputer',
                    'topik_utama'  => 'Knowledge Graph',
                    'avatar_image' => 'images/image_0.jpeg',
                    'initials'     => strtoupper(substr($rek['Rekomendasi_Nama'], 0, 2)),
                ];
            }
        }
        unset($rek);

        $rekomNames = array_slice(array_column($rekomendasi, 'Rekomendasi_Nama'), 0, 4);
        $graphData  = DummyDataProvider::graph($detail['hasName'], $rekomNames);

        return view('dosen.show', [
            'dosen'       => $detail,
            'publikasi'   => $publikasi,
            'rekomendasi' => $rekomendasi,
            'graphData'   => $graphData,
        ]);
    }
}
