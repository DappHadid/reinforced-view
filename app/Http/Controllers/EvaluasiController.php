<?php

namespace App\Http\Controllers;

use App\Support\DummyDataProvider;

class EvaluasiController extends Controller
{
    public function index()
    {
        return view('evaluasi', [
            'evaluasiStandar' => DummyDataProvider::evaluasi(false),
            'evaluasiHybrid' => DummyDataProvider::evaluasi(true),
            'penilaianRekap' => DummyDataProvider::penilaianRekap(),
        ]);
    }
}
