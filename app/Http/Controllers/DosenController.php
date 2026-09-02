<?php

namespace App\Http\Controllers;

use App\Support\DummyDataProvider;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $dosenList = DummyDataProvider::dosenList();

        if ($query !== '') {
            $dosenList = array_values(array_filter($dosenList, function ($dosen) use ($query) {
                return stripos($dosen['nama'], $query) !== false || stripos($dosen['sinta_id'], $query) !== false;
            }));
        }

        usort($dosenList, fn ($a, $b) => strcmp($a['nama'], $b['nama']));

        return view('dosen.index', [
            'dosenList' => $dosenList,
            'query' => $query,
        ]);
    }

    public function show(string $sintaId)
    {
        $detail = DummyDataProvider::dosenDetail($sintaId);

        abort_if($detail === null, 404);

        $publikasi = DummyDataProvider::publikasi($sintaId);
        $rekomendasi = DummyDataProvider::rekomendasi($detail['hasName'], true);
        $rekomNames = array_slice(array_column($rekomendasi, 'Rekomendasi_Nama'), 0, 4);
        $graphData = DummyDataProvider::graph($detail['hasName'], $rekomNames);

        return view('dosen.show', [
            'dosen' => $detail,
            'publikasi' => $publikasi,
            'graphData' => $graphData,
        ]);
    }
}
