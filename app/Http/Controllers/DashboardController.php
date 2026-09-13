<?php

namespace App\Http\Controllers;

use App\Support\DummyDataProvider;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $departemen = $request->query('departemen', '');

        $dosenList       = DummyDataProvider::dosenList();
        $evaluasiHybrid  = DummyDataProvider::evaluasi(true);
        $totalPublikasi  = DummyDataProvider::totalPublikasi();
        $totalRelasi     = DummyDataProvider::totalRelasi();
        $totalSitasi     = DummyDataProvider::totalSitasi();
        $fullGraphData   = DummyDataProvider::fullGraph($departemen ?: null);
        $departemenList  = DummyDataProvider::departemenList();

        return view('dashboard', [
            'dosenList'          => $dosenList,
            'evaluasiTerakhir'   => $evaluasiHybrid,
            'totalPublikasi'     => $totalPublikasi,
            'totalRelasi'        => $totalRelasi,
            'totalSitasi'        => $totalSitasi,
            'graphData'          => $fullGraphData,
            'departemenList'     => $departemenList,
            'selectedDepartemen' => $departemen,
        ]);
    }
}
