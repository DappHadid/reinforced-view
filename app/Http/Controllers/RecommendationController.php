<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecommendationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil parameter dari URL
        $name = $request->query('name', 'MIRA MUSRINI BARMAWI');
        $useCascading = $request->query('use_cascading', 'true') === 'true';
        
        // 2. Request ke Python API
        $response = Http::timeout(30)->get('http://127.0.0.1:8000/api/rekomendasi', [
            'name' => $name,
            'use_cascading' => $useCascading ? 'true' : 'false'
        ]);
        
        // 3. Ambil daftar dosen
        $dosenResponse = Http::timeout(30)->get('http://127.0.0.1:8000/api/dosen');
        
        // 4. Return View
        return view('rekomendasi', [
            'rekomendasi' => $response->successful() ? ($response->json()['data'] ?? []) : [],
            'dosenList' => $dosenResponse->successful() ? ($dosenResponse->json()['data'] ?? []) : [],
            'currentName' => $name,
            'useCascading' => $useCascading
        ]);
    }
}
