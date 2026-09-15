<?php

namespace App\Http\Controllers;

use App\Support\ApiDataProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    // Warna gradien avatar (cycling berdasarkan index)
    private static array $gradients = [
        ['from-blue-400',   'to-blue-600'],
        ['from-violet-400', 'to-violet-600'],
        ['from-emerald-400','to-emerald-600'],
        ['from-amber-400',  'to-orange-500'],
        ['from-rose-400',   'to-rose-600'],
        ['from-sky-400',    'to-cyan-500'],
        ['from-fuchsia-400','to-purple-600'],
        ['from-teal-400',   'to-teal-600'],
    ];

    private static array $fakultasMap = [
        'Informatika'                  => 'Fakultas Teknologi Industri',
        'Sistem Informasi'             => 'Fakultas Teknologi Industri',
        'Teknik Elektro'               => 'Fakultas Teknologi Industri',
        'Teknik Mesin'                 => 'Fakultas Teknologi Industri',
        'Teknik Industri'              => 'Fakultas Teknologi Industri',
        'Teknik Kimia'                 => 'Fakultas Teknologi Industri',
        'Teknik Sipil'                 => 'Fakultas Teknik Sipil dan Perencanaan',
        'Teknik Geodesi'               => 'Fakultas Teknik Sipil dan Perencanaan',
        'Perencanaan Wilayah Dan Kota' => 'Fakultas Teknik Sipil dan Perencanaan',
        'Teknik Lingkungan'            => 'Fakultas Teknik Sipil dan Perencanaan',
        'Arsitektur'                   => 'Fakultas Arsitektur dan Desain',
        'Desain Interior'              => 'Fakultas Arsitektur dan Desain',
        'Desain Produk'                => 'Fakultas Arsitektur dan Desain',
        'Desain Komunikasi Visual'     => 'Fakultas Arsitektur dan Desain',
    ];

    /**
     * Transformasi raw dosen dari API menjadi format yang dibutuhkan view.
     */
    public static function transform(array $dosen, int $index): array
    {
        $nama     = $dosen['nama'] ?? '';
        $sintaId  = $dosen['sinta_id'] ?? '';
        $dept     = $dosen['departemen'] ?? ($dosen['hasDepartment'] ?? '');

        // Map departemen ke fakultas
        if (str_contains($dept, ' - ')) {
            [$prodi, $fakultas] = explode(' - ', $dept, 2);
        } else {
            $prodi    = $dept ?: 'Program Studi Umum';
            // Coba lookup berdasarkan nama prodi/departemen yang di-trim dan title case
            $lookupKey = ucwords(strtolower(trim($prodi)));
            $fakultas = self::$fakultasMap[$lookupKey] ?? (self::$fakultasMap[$prodi] ?? 'Fakultas Lainnya');
        }
        // Path foto profil (uppercase, pakai .png/.jpeg — coba keduanya)
        $namaUpper   = strtoupper($nama);
        $avatarImage = null;
        foreach (['png', 'jpeg', 'jpg'] as $ext) {
            $path = "images/profile_picture/{$namaUpper}.{$ext}";
            if (file_exists(public_path($path))) {
                $avatarImage = $path;
                break;
            }
        }
        $avatarImage = $avatarImage ?? 'images/avatar.jpg';

        // Initials
        $words    = preg_split('/\s+/', trim($nama));
        $initials = strtoupper(substr($words[0] ?? 'X', 0, 1) . substr($words[1] ?? '', 0, 1));

        // Gradien
        $grad = self::$gradients[$index % count(self::$gradients)];

        return array_merge($dosen, [
            'nama'         => $nama,
            'nama_display' => Str::title(strtolower($nama)),
            'sinta_id'     => $sintaId,
            'prodi'        => trim($prodi),
            'fakultas'     => trim($fakultas),
            'avatar_image' => $avatarImage,
            'initials'     => $initials,
            'avatar_from'  => $grad[0],
            'avatar_to'    => $grad[1],
        ]);
    }

    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $query    = trim((string) $request->query('q', ''));
        $rawList  = ApiDataProvider::dosenList();

        // Filter pencarian
        if ($query !== '') {
            $rawList = array_values(array_filter($rawList, fn ($d) =>
                stripos($d['nama'] ?? '', $query) !== false
                || stripos($d['sinta_id'] ?? '', $query) !== false
            ));
        }

        usort($rawList, fn ($a, $b) => strcmp($a['nama'] ?? '', $b['nama'] ?? ''));

        // Transform semua dosen
        $dosenList = array_map(
            fn ($d, $i) => self::transform($d, $i),
            $rawList,
            array_keys($rawList)
        );

        // Bangun fakultas & prodi list untuk filter
        $prodiByFakultas = [];
        foreach ($dosenList as $d) {
            $prodiByFakultas[$d['fakultas']][$d['prodi']] = true;
        }
        $prodiByFakultas = array_map(fn ($p) => array_keys($p), $prodiByFakultas);
        $fakultasList    = array_keys($prodiByFakultas);

        // JSON untuk JS search
        $allDosenJson = array_map(fn ($d) => [
            'nama'         => $d['nama'],
            'nama_display' => $d['nama_display'],
            'sinta_id'     => $d['sinta_id'],
            'prodi'        => $d['prodi'],
            'fakultas'     => $d['fakultas'],
            'initials'     => $d['initials'],
            'avatar_image' => $d['avatar_image'],
            'url'          => route('dosen.show', $d['sinta_id']),
        ], $dosenList);

        return view('dosen.index', [
            'dosenList'       => $dosenList,
            'query'           => $query,
            'fakultasList'    => $fakultasList,
            'prodiByFakultas' => $prodiByFakultas,
            'allDosenJson'    => $allDosenJson,
        ]);
    }

    // -------------------------------------------------------------------------

    public function show(Request $request, string $sintaId)
    {
        $detail = ApiDataProvider::dosenDetail($sintaId);
        abort_if($detail === null, 404);

        // Transform detail agar punya field avatar dll
        $detail = self::transform([
            'nama'       => $detail['hasName']       ?? '',
            'sinta_id'   => $detail['hasSintaID']    ?? $sintaId,
            'departemen' => $detail['hasDepartment'] ?? '',
        ], 0);

        // Gabungkan kembali dengan detail lengkap
        $detail = array_merge($detail, ApiDataProvider::dosenDetail($sintaId) ?? []);

        $publikasi   = ApiDataProvider::publikasi($sintaId);
        $useCascading = $request->query('use_cascading', 'true') === 'true';
        $rekomendasi = ApiDataProvider::rekomendasi($detail['hasName'] ?? $detail['nama'], $useCascading);
        $dosenById = [];
        foreach (ApiDataProvider::dosenList() as $index => $dosenItem) {
            $dosenById[(string) ($dosenItem['sinta_id'] ?? '')] = self::transform($dosenItem, $index);
        }
        $rekomendasi = array_map(function ($rek, $index) use ($dosenById) {
            $sintaIdRek = (string) ($rek['Rekomendasi_SINTA_ID'] ?? '');
            $rek['meta'] = $dosenById[$sintaIdRek] ?? self::transform([
                'nama' => $rek['Rekomendasi_Nama'] ?? '',
                'sinta_id' => $sintaIdRek,
            ], $index);
            return $rek;
        }, $rekomendasi, array_keys($rekomendasi));
        $rekomNames  = array_slice(array_column($rekomendasi, 'Rekomendasi_Nama'), 0, 4);
        $graphData   = ApiDataProvider::graph($detail['hasName'] ?? $detail['nama'], $rekomNames, $useCascading);

        // Build stats lookup keyed by sinta_id to enrich graph nodes for popup
        $statsById = [];

        // Target dosen
        $statsById[(string)($detail['hasSintaID'] ?? $sintaId)] = [
            'h_index'           => $detail['ns0__hasHIndexScholar']      ?? null,
            'publication_count' => $detail['ns0__hasPublicationScholar'] ?? null,
            'department'        => $detail['prodi'] ?? ($detail['hasDepartment'] ?? null),
            'ane_score'         => null,
        ];

        // Rekomendasi nodes
        foreach ($rekomendasi as $rek) {
            $sid  = (string)($rek['Rekomendasi_SINTA_ID'] ?? '');
            $stat = $rek['Detail_Statistik'] ?? [];
            $meta = $rek['meta'] ?? [];
            if ($sid !== '') {
                $statsById[$sid] = [
                    'h_index'           => $stat['ns0__hasHIndexScholar']      ?? ($stat['hasHIndexScholar']      ?? null),
                    'publication_count' => $stat['ns0__hasPublicationScholar'] ?? ($stat['hasPublicationScholar'] ?? null),
                    'department'        => $meta['prodi'] ?? null,
                    'ane_score'         => $rek['Skor Kemiripan'] ?? null,
                ];
            }
        }

        // Collaborator nodes from dosenList
        foreach ($dosenById as $sid => $d) {
            if (!isset($statsById[$sid])) {
                $statsById[$sid] = [
                    'h_index'           => $d['ns0__hasHIndexScholar']      ?? ($d['hasHIndexScholar']      ?? null),
                    'publication_count' => $d['ns0__hasPublicationScholar'] ?? ($d['hasPublicationScholar'] ?? null),
                    'department'        => $d['prodi'] ?? null,
                    'ane_score'         => null,
                ];
            }
        }

        $graphData = ApiDataProvider::enrichGraphNodes($graphData, $statsById);

        return view('dosen.show', [
            'dosen'       => $detail,
            'publikasi'   => $publikasi,
            'rekomendasi' => $rekomendasi,
            'graphData'   => $graphData,
            'useCascading' => $useCascading,
        ]);
    }
}
