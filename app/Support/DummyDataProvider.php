<?php

namespace App\Support;

/**
 * Sumber data dummy untuk seluruh halaman REINFORCED, dibentuk mengikuti
 * persis field response API FastAPI (lihat PRD §5 & §8). Ganti isi tiap
 * method dengan panggilan Http::get(...)->json() saat endpoint API asli
 * sudah siap — signature method sengaja dibuat identik dengan bentuk
 * response API supaya swap-nya minim perubahan di controller.
 */
class DummyDataProvider
{
    private const DEPARTEMEN = [
        // Fakultas Ilmu Komputer (5 prodi)
        'Teknik Informatika',
        'Sistem Informasi',
        'Ilmu Komputer',
        'Teknologi Informasi',
        'Keamanan Siber',
        // Fakultas Teknik (4 prodi)
        'Teknik Elektro',
        'Teknik Industri',
        'Teknik Mesin',
        'Teknik Sipil',
        // Fakultas Sains dan Matematika (5 prodi)
        'Matematika',
        'Fisika',
        'Statistika',
        'Kimia',
        'Biologi',
    ];

    private const FAKULTAS_MAP = [
        'Teknik Informatika'  => 'Fakultas Ilmu Komputer',
        'Sistem Informasi'    => 'Fakultas Ilmu Komputer',
        'Ilmu Komputer'       => 'Fakultas Ilmu Komputer',
        'Teknologi Informasi' => 'Fakultas Ilmu Komputer',
        'Keamanan Siber'      => 'Fakultas Ilmu Komputer',
        'Teknik Elektro'      => 'Fakultas Teknik',
        'Teknik Industri'     => 'Fakultas Teknik',
        'Teknik Mesin'        => 'Fakultas Teknik',
        'Teknik Sipil'        => 'Fakultas Teknik',
        'Matematika'          => 'Fakultas Sains dan Matematika',
        'Fisika'              => 'Fakultas Sains dan Matematika',
        'Statistika'          => 'Fakultas Sains dan Matematika',
        'Kimia'               => 'Fakultas Sains dan Matematika',
        'Biologi'             => 'Fakultas Sains dan Matematika',
    ];

    private const TOPIK = [
        'Machine Learning', 'Knowledge Graph', 'Natural Language Processing',
        'Computer Vision', 'Jaringan Sensor', 'Rekayasa Perangkat Lunak',
        'Sistem Terdistribusi', 'Keamanan Siber', 'Data Mining', 'Internet of Things',
    ];

    private const DOSEN = [
        ['nama' => 'MIRA MUSRINI BARMAWI', 'sinta_id' => '6003212'],
        ['nama' => 'BAMBANG ARI WAHYUDI', 'sinta_id' => '6001187'],
        ['nama' => 'SITI NURUL AZIZAH', 'sinta_id' => '6004531'],
        ['nama' => 'ANDI SETIAWAN PUTRA', 'sinta_id' => '6002298'],
        ['nama' => 'DEWI KARTIKA SARI', 'sinta_id' => '6005672'],
        ['nama' => 'FAJAR RAMADHAN NUGROHO', 'sinta_id' => '6006841'],
        ['nama' => 'RATNA WIJAYANTI', 'sinta_id' => '6003958'],
        ['nama' => 'HENDRA KURNIAWAN', 'sinta_id' => '6007123'],
        ['nama' => 'LESTARI PRATIWI', 'sinta_id' => '6002045'],
        ['nama' => 'AGUS SUSANTO', 'sinta_id' => '6008376'],
        ['nama' => 'NOVI ANGGRAINI', 'sinta_id' => '6004890'],
        ['nama' => 'RIZKY FAUZI ABDILLAH', 'sinta_id' => '6009214'],
        ['nama' => 'YULIANA SAFITRI', 'sinta_id' => '6001765'],
        ['nama' => 'DIMAS ARYA WICAKSONO', 'sinta_id' => '6005329'],
        ['nama' => 'INDAH PERMATASARI', 'sinta_id' => '6003687'],
        ['nama' => 'TAUFIK HIDAYAT', 'sinta_id' => '6007954'],
        ['nama' => 'MELATI PUSPA NINGRUM', 'sinta_id' => '6002510'],
        ['nama' => 'GALIH PRASETYO', 'sinta_id' => '6006098'],
    ];

    public static function dosenList(): array
    {
        return self::DOSEN;
    }

    public static function dosenListWithMeta(): array
    {
        // Avatar gradient pairs (from-color to-color) cycled by index
        $gradients = [
            ['from-sky-400', 'to-blue-600'],
            ['from-violet-400', 'to-purple-600'],
            ['from-emerald-400', 'to-teal-600'],
            ['from-rose-400', 'to-pink-600'],
            ['from-amber-400', 'to-orange-600'],
            ['from-cyan-400', 'to-sky-600'],
            ['from-fuchsia-400', 'to-violet-600'],
            ['from-lime-400', 'to-green-600'],
        ];

        // 16 portrait images: image_0.jpeg … image_15.jpeg
        $totalImages = 16;

        $result = [];
        foreach (self::DOSEN as $i => $dosen) {
            $sid   = $dosen['sinta_id'];
            $stat  = self::statistikFor($sid);
            $seed  = self::seedFromString($sid);
            $grad  = $gradients[$i % count($gradients)];
            $topik = self::TOPIK[$seed % count(self::TOPIK)];

            $prodi    = self::pick(self::DEPARTEMEN, $sid);
            $fakultas = self::FAKULTAS_MAP[$prodi] ?? 'Lainnya';

            $result[] = [
                'nama'            => $dosen['nama'],
                'nama_display'    => ucwords(strtolower($dosen['nama'])),
                'sinta_id'        => $sid,
                'prodi'           => $prodi,
                'fakultas'        => $fakultas,
                'topik_utama'     => $topik,
                'h_index_scholar' => $stat['ns0__hasHIndexScholar'],
                'total_publikasi' => $stat['ns0__hasPublicationScholar'],
                'kolaborator'     => $stat['ns0__hasCollaborator'],
                'avatar_from'     => $grad[0],
                'avatar_to'       => $grad[1],
                'initials'        => self::initials($dosen['nama']),
                'avatar_image'    => 'images/image_'.($i % $totalImages).'.jpeg',
            ];
        }

        return $result;
    }

    private static function initials(string $nama): string
    {
        $words = preg_split('/\s+/', trim($nama));
        $out   = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $out .= strtoupper(mb_substr($w, 0, 1));
        }

        return $out ?: '?';
    }

    public static function fakultasList(): array
    {
        return ['Fakultas Ilmu Komputer', 'Fakultas Teknik', 'Fakultas Sains dan Matematika'];
    }

    public static function prodiByFakultas(): array
    {
        $result = [];
        foreach (self::FAKULTAS_MAP as $prodi => $fakultas) {
            $result[$fakultas][] = $prodi;
        }

        return $result;
    }

    public static function totalPublikasi(): int
    {
        $total = 0;
        foreach (self::DOSEN as $dosen) {
            $total += count(self::publikasi($dosen['sinta_id']));
        }

        return $total;
    }

    public static function totalRelasi(): int
    {
        return count(self::fullGraph()['edges']);
    }

    public static function totalSitasi(): int
    {
        $total = 0;
        foreach (self::DOSEN as $dosen) {
            $stat = self::statistikFor($dosen['sinta_id']);
            $total += (int) (
                ($stat['ns0__hasPublicationScholar'] * $stat['ns0__hasAverageCitationScholar']) +
                ($stat['ns0__hasPublicationScopus'] * $stat['ns0__hasAverageCitationScopus'])
            );
        }

        return $total > 0 ? $total : 1420;
    }


    public static function findDosenByName(string $name): ?array
    {
        foreach (self::DOSEN as $dosen) {
            if (strcasecmp($dosen['nama'], $name) === 0) {
                return $dosen;
            }
        }

        return null;
    }

    public static function findDosenBySintaId(string $sintaId): ?array
    {
        foreach (self::DOSEN as $dosen) {
            if ($dosen['sinta_id'] === $sintaId) {
                return $dosen;
            }
        }

        return null;
    }

    public static function rekomendasi(string $name, bool $useCascading): array
    {
        $target = self::findDosenByName($name) ?? ['nama' => strtoupper($name), 'sinta_id' => '0000000'];
        $pool = array_values(array_filter(self::DOSEN, fn ($d) => strcasecmp($d['nama'], $target['nama']) !== 0));

        $seed = self::seedFromString($target['nama'].($useCascading ? 'hybrid' : 'standard'));
        $rng = self::seededRandom($seed);

        usort($pool, fn ($a, $b) => ($rng() <=> $rng()));

        $candidates = array_slice($pool, 0, 5);
        $result = [];

        foreach ($candidates as $i => $candidate) {
            $baseScore = 0.94 - ($i * 0.09) - ($rng() * 0.04);
            $bonus = $useCascading ? 0.03 : 0;
            $skor = round(max(0.35, min(0.98, $baseScore + $bonus)), 4);

            $result[] = [
                'Nama' => $target['nama'],
                'SINTA_ID' => $target['sinta_id'],
                'Rekomendasi_Nama' => $candidate['nama'],
                'Rekomendasi_SINTA_ID' => $candidate['sinta_id'],
                'Skor Kemiripan' => $skor,
                'Detail_Statistik' => self::statistikFor($candidate['sinta_id']),
                'Detail_Publikasi' => array_column(self::publikasi($candidate['sinta_id']), 'judul'),
            ];
        }

        return $result;
    }

    public static function departemenList(): array
    {
        return self::DEPARTEMEN;
    }

    public static function fullGraph(?string $departemen = null): array
    {
        $dosen = self::DOSEN;

        if ($departemen !== null && $departemen !== '') {
            $dosen = array_values(array_filter($dosen, fn ($d) => self::pick(self::DEPARTEMEN, $d['sinta_id']) === $departemen));
        }

        $nodes = [];
        $edges = [];
        $nodeIds = [];

        foreach ($dosen as $d) {
            $id = 'd_'.self::slug($d['sinta_id']);
            $nodes[] = [
                'id' => $id,
                'label' => $d['nama'],
                'group' => 'connector',
                'sinta_id' => $d['sinta_id'],
                'department' => self::pick(self::DEPARTEMEN, $d['sinta_id']),
            ];
            $nodeIds[$d['sinta_id']] = $id;
        }

        foreach ($dosen as $d) {
            $seed = self::seedFromString($d['sinta_id'].'collab');
            $collabCount = 1 + ($seed % 3);

            for ($i = 0; $i < $collabCount; $i++) {
                $targetIndex = ($seed + $i * 7) % count($dosen);
                $target = $dosen[$targetIndex];

                if ($target['sinta_id'] === $d['sinta_id']) {
                    continue;
                }

                $edges[] = [
                    'from' => $nodeIds[$d['sinta_id']],
                    'to' => $nodeIds[$target['sinta_id']],
                    'label' => 'collaborateWith',
                ];
            }
        }

        return ['nodes' => $nodes, 'edges' => $edges];
    }

    public static function graph(string $targetName, array $rekomNames): array
    {
        $target = self::findDosenByName($targetName) ?? ['nama' => strtoupper($targetName), 'sinta_id' => 'target'];
        $targetId = 't_'.self::slug($target['nama']);

        $nodes = [
            ['id' => $targetId, 'label' => $target['nama'], 'group' => 'target'],
        ];
        $edges = [];

        $connectorPool = array_values(array_filter(
            self::DOSEN,
            fn ($d) => strcasecmp($d['nama'], $target['nama']) !== 0 && ! in_array($d['nama'], $rekomNames, true)
        ));

        foreach ($rekomNames as $rekomName) {
            $rekomId = 'r_'.self::slug($rekomName);
            $nodes[] = ['id' => $rekomId, 'label' => $rekomName, 'group' => 'recommendation'];
            $edges[] = ['from' => $targetId, 'to' => $rekomId, 'label' => 'recommended'];

            if (! empty($connectorPool)) {
                $connector = $connectorPool[array_rand($connectorPool)];
                $connectorId = 'c_'.self::slug($connector['nama']);

                if (! in_array($connectorId, array_column($nodes, 'id'), true)) {
                    $nodes[] = ['id' => $connectorId, 'label' => $connector['nama'], 'group' => 'connector'];
                }

                $edges[] = ['from' => $targetId, 'to' => $connectorId, 'label' => 'collaborateWith'];
                $edges[] = ['from' => $connectorId, 'to' => $rekomId, 'label' => 'collaborateWith'];
            }
        }

        return ['nodes' => $nodes, 'edges' => $edges];
    }

    public static function dosenDetail(string $sintaId): ?array
    {
        $dosenList = self::dosenListWithMeta();
        $found = null;
        foreach ($dosenList as $d) {
            if ($d['sinta_id'] === $sintaId) {
                $found = $d;
                break;
            }
        }

        if (! $found) {
            $base = self::findDosenBySintaId($sintaId);
            if (! $base) {
                return null;
            }
            $stat = self::statistikFor($sintaId);
            return array_merge([
                'hasSintaID' => $base['sinta_id'],
                'hasName' => $base['nama'],
                'hasDepartment' => self::pick(self::DEPARTEMEN, $sintaId),
                'fakultas' => 'Fakultas Ilmu Komputer',
                'prodi' => self::pick(self::DEPARTEMEN, $sintaId),
                'topik_utama' => self::TOPIK[self::seedFromString($sintaId) % count(self::TOPIK)],
                'avatar_image' => 'images/image_0.jpeg',
                'initials' => self::initials($base['nama']),
                'avatar_from' => 'from-sky-400',
                'avatar_to' => 'to-blue-600',
                'hasAcademicAge' => 5 + (self::seedFromString($sintaId) % 25),
            ], $stat);
        }

        $stat = self::statistikFor($sintaId);

        return array_merge([
            'hasSintaID'     => $found['sinta_id'],
            'hasName'        => $found['nama'],
            'hasDepartment'  => $found['prodi'],
            'fakultas'       => $found['fakultas'],
            'prodi'          => $found['prodi'],
            'topik_utama'    => $found['topik_utama'],
            'avatar_image'   => $found['avatar_image'],
            'initials'       => $found['initials'],
            'avatar_from'    => $found['avatar_from'],
            'avatar_to'      => $found['avatar_to'],
            'hasAcademicAge' => 5 + (self::seedFromString($sintaId) % 25),
        ], $stat);
    }

    public static function publikasi(string $sintaId): array
    {
        $dosen = self::findDosenBySintaId($sintaId);
        
        $count = 18 + (self::seedFromString($sintaId) % 10);
        $sumbers = ['Scopus', 'Google Scholar', 'Web of Science', 'SINTA'];
        
        $templateJudul = [
            'Studi %s: Pendekatan Berbasis Graf untuk Kolaborasi Akademik',
            'Optimasi Sistem Rekomendasi Peneliti Berbasis Algoritma %s',
            'Analisis Jaringan Kolaborasi Multidisiplin Menggunakan Model %s',
            'Penerapan %s dalam Pemetaan Kepakaran dan Publikasi Ilmiah',
            'Evaluasi Kinerja Algoritma %s pada Dataset Graph Pengetahuan',
            'Integrasi Metode %s untuk Deteksi Komunitas Riset Perguruan Tinggi',
            'Arsitektur Terdistribusi Berbasis %s untuk Pemrosesan Big Data Sitasi',
            'Pengembangan Visualisasi Interaktif %s pada Repositori Akademik',
            'Peningkatan Akurasi Hybrid Matching Menggunakan %s dan Deep Learning',
            'Studi Meta-Analisis Tren Riset %s dalam Ekosistem SINTA Indonesia',
        ];

        $publikasi = [];

        for ($i = 0; $i < $count; $i++) {
            $topikIndex = (self::seedFromString($sintaId) + $i * 7) % count(self::TOPIK);
            $topik = self::TOPIK[$topikIndex];
            $tmpl = $templateJudul[$i % count($templateJudul)];
            $judul = sprintf($tmpl, $topik);
            
            $tahun = 2025 - (($i * 2 + self::seedFromString($sintaId) % 3) % 10);
            $sumber = $sumbers[($i + self::seedFromString($sintaId)) % count($sumbers)];
            $sitasi = (self::seedFromString($sintaId . $i) % 45) + ($tahun > 2022 ? 2 : 12);
            $doiSuffix = strtolower(self::slug($sintaId)) . '.' . ($i + 1);

            $publikasi[] = [
                'judul'   => $judul,
                'tahun'   => $tahun,
                'doi'     => '10.1000/' . $doiSuffix,
                'sumber'  => $sumber,
                'sitasi'  => $sitasi,
                'topik'   => $topik,
            ];
        }

        usort($publikasi, fn ($a, $b) => $b['tahun'] <=> $a['tahun']);

        return $publikasi;
    }

    public static function evaluasi(bool $useCascading): array
    {
        if ($useCascading) {
            return [
                'metode' => 'Cascading Hybrid',
                'precision_at_5' => 0.812,
                'recall' => 0.734,
                'f1_score' => 0.771,
                'map_at_5' => 0.789,
            ];
        }

        return [
            'metode' => 'Standard ANE',
            'precision_at_5' => 0.706,
            'recall' => 0.658,
            'f1_score' => 0.681,
            'map_at_5' => 0.664,
        ];
    }

    public static function penilaianRekap(): array
    {
        $rekap = [];
        $rng = self::seededRandom(42);

        foreach (array_slice(self::DOSEN, 0, 10) as $dosen) {
            $ratings = [];
            for ($i = 0; $i < 5; $i++) {
                $ratings[] = 3 + (self::seedFromString($dosen['sinta_id'].$i) % 3);
            }

            $rekap[] = [
                'nama' => $dosen['nama'],
                'ratings' => $ratings,
                'rata_rata' => round(array_sum($ratings) / count($ratings), 2),
                'komentar' => self::komentarFor($dosen['sinta_id']),
            ];
        }

        return $rekap;
    }

    private static function komentarFor(string $sintaId): string
    {
        $komentar = [
            'Rekomendasi relevan dengan bidang riset saya.',
            'Cukup membantu menemukan kolaborator baru.',
            'Beberapa kandidat kurang sesuai topik.',
            'Sangat akurat, langsung diajak diskusi.',
            'Perlu penyesuaian lebih lanjut pada bobot topik.',
        ];

        return $komentar[self::seedFromString($sintaId) % count($komentar)];
    }

    private static function statistikFor(string $sintaId): array
    {
        $seed = self::seedFromString($sintaId);

        return [
            'ns0__hasCollaborator' => 3 + ($seed % 15),
            'ns0__hasAverageCitationScholar' => round(5 + (($seed % 40) / 2), 1),
            'ns0__hasAverageCitationScopus' => round(3 + (($seed % 30) / 2), 1),
            'ns0__hasAverageCitationWos' => round(2 + (($seed % 25) / 2), 1),
            'ns0__hasHIndexScholar' => 2 + ($seed % 18),
            'ns0__hasHIndexScopus' => 1 + ($seed % 14),
            'ns0__hasHIndexWos' => 1 + ($seed % 10),
            'ns0__hasPublicationScholar' => 10 + ($seed % 60),
            'ns0__hasPublicationScopus' => 5 + ($seed % 40),
            'ns0__hasPublicationWos' => 2 + ($seed % 25),
            'ns0__hasDepartment' => self::pick(self::DEPARTEMEN, $sintaId),
        ];
    }

    private static function pick(array $items, string $seedKey): string
    {
        return $items[self::seedFromString($seedKey) % count($items)];
    }

    private static function slug(string $value): string
    {
        return preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($value)));
    }

    private static function seedFromString(string $value): int
    {
        return abs(crc32($value));
    }

    private static function seededRandom(int $seed): \Closure
    {
        $state = $seed;

        return function () use (&$state) {
            $state = ($state * 1103515245 + 12345) & 0x7fffffff;

            return $state / 0x7fffffff;
        };
    }
}
