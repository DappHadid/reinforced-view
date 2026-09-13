<?php

namespace App\Http\Controllers;

use App\Support\ApiDataProvider;
use Illuminate\Http\Request;

class JaringanController extends Controller
{
    public function index(Request $request)
    {
        $departemen = $request->query('departemen', '');

        return view('jaringan', [
            'graphData' => ApiDataProvider::fullGraph($departemen ?: null),
            'departemenList' => ApiDataProvider::departemenList(),
            'selectedDepartemen' => $departemen,
        ]);
    }
}
