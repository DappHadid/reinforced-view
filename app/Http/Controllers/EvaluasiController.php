<?php

namespace App\Http\Controllers;

use App\Support\ApiDataProvider;

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
}
