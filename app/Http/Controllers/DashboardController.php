<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    protected string $apiBase = 'http://127.0.0.1:8000';

    public function index(Request $request)
    {
        $name = $request->query('name', '');
        $useCascading = $request->query('use_cascading', 'true') === 'true';

        $dosenList = [];
        $rekomendasi = [];
        $graphData = ['nodes' => [], 'edges' => []];
        $apiError = null;

        // Daftar dosen (dipakai untuk statistik & dropdown pencarian)
        try {
            $dosenResponse = Http::timeout(10)->get("{$this->apiBase}/api/dosen");
            $dosenList = $dosenResponse->successful() ? ($dosenResponse->json()['data'] ?? []) : [];

            if (! $dosenResponse->successful()) {
                $apiError = 'Tidak dapat terhubung ke API rekomendasi. Pastikan server FastAPI berjalan di ' . $this->apiBase;
            }
        } catch (\Exception $e) {
            $apiError = 'Tidak dapat terhubung ke API rekomendasi. Pastikan server FastAPI berjalan di ' . $this->apiBase;
        }

        if ($name !== '') {
            try {
                $rekomResponse = Http::timeout(30)->get("{$this->apiBase}/api/rekomendasi", [
                    'name' => $name,
                    'use_cascading' => $useCascading ? 'true' : 'false',
                ]);

                if ($rekomResponse->successful()) {
                    $rekomendasi = $rekomResponse->json()['data'] ?? [];

                    if (! empty($rekomendasi)) {
                        $rekomNames = implode(',', array_column($rekomendasi, 'Rekomendasi_Nama'));

                        $graphResponse = Http::timeout(30)->get("{$this->apiBase}/api/graph", [
                            'target_name' => strtoupper($name),
                            'rekom_names' => $rekomNames,
                        ]);

                        if ($graphResponse->successful()) {
                            $graphData = $graphResponse->json()['data'] ?? $graphData;
                        }
                    }
                } else {
                    $apiError = 'Gagal mengambil data rekomendasi dari API.';
                }
            } catch (\Exception $e) {
                $apiError = 'Tidak dapat terhubung ke API rekomendasi. Pastikan server FastAPI berjalan di ' . $this->apiBase;
            }
        }

        return view('dashboard', [
            'dosenList' => $dosenList,
            'rekomendasi' => $rekomendasi,
            'graphData' => $graphData,
            'currentName' => $name,
            'useCascading' => $useCascading,
            'apiError' => $apiError,
        ]);
    }
}
