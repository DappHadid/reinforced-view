<?php

namespace App\Http\Controllers;

use App\Support\ApiDataProvider;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $name               = $request->query('name', '');
        $useCascading       = $request->query('use_cascading', 'true') === 'true';
        $selectedDepartemen = $request->query('departemen', '');

        $dosenList      = ApiDataProvider::dosenList();
        $departemenList = ApiDataProvider::departemenList();
        $rekomendasi    = [];
        $graphData      = ['nodes' => [], 'edges' => []];

        if ($name !== '') {
            $rekomendasi = ApiDataProvider::rekomendasi($name, $useCascading);
            // $rekomNames  = array_column($rekomendasi, 'Rekomendasi_Nama');
            // $graphData   = ApiDataProvider::graph($name, $rekomNames);
        }

        // Fetch overall graph for the dashboard
        $graphData = ApiDataProvider::fullGraph($selectedDepartemen ?: null);

        $evaluasiHybrid = ApiDataProvider::evaluasi(true);

        // Statistik ringkas dari data dosen
        $stats        = ApiDataProvider::stats();
        $totalPublikasi = $stats['totalPublikasi'] ?? count($dosenList) * 6;
        $totalRelasi    = $stats['totalRelasi']    ?? 0;
        $totalSitasi    = $stats['totalSitasi']    ?? 0;

        return view('dashboard', [
            'dosenList'              => $dosenList,
            'departemenList'         => $departemenList,
            'selectedDepartemen'     => $selectedDepartemen,
            'rekomendasi'            => $rekomendasi,
            'graphData'              => $graphData,
            'currentName'            => $name,
            'useCascading'           => $useCascading,
            'evaluasiTerakhir'       => $evaluasiHybrid,
            'totalPublikasi'         => $totalPublikasi,
            'totalRelasi'            => $totalRelasi,
            'totalSitasi'            => $totalSitasi,
            'totalRekomendasiDicari' => 128,
        ]);
    }
}
