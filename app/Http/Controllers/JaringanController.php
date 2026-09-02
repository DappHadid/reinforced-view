<?php

namespace App\Http\Controllers;

use App\Support\DummyDataProvider;
use Illuminate\Http\Request;

class JaringanController extends Controller
{
    public function index(Request $request)
    {
        $departemen = $request->query('departemen', '');

        return view('jaringan', [
            'graphData' => DummyDataProvider::fullGraph($departemen ?: null),
            'departemenList' => DummyDataProvider::departemenList(),
            'selectedDepartemen' => $departemen,
        ]);
    }
}
