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
        'Teknik Informatika',
        'Sistem Informasi',
        'Teknik Elektro',
        'Ilmu Komputer',
        'Teknik Industri',
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
        $dosen = self::findDosenBySintaId($sintaId);

        if (! $dosen) {
            return null;
        }

        $stat = self::statistikFor($sintaId);

        return array_merge([
            'hasSintaID' => $dosen['sinta_id'],
            'hasName' => $dosen['nama'],
            'hasDepartment' => self::pick(self::DEPARTEMEN, $sintaId),
            'hasAcademicAge' => 5 + (self::seedFromString($sintaId) % 25),
        ], $stat);
    }

    public static function publikasi(string $sintaId): array
    {
        $rng = self::seededRandom(self::seedFromString($sintaId.'pub'));
        $count = 4 + (self::seedFromString($sintaId) % 5);
        $dosen = self::findDosenBySintaId($sintaId);
        $namaPendek = $dosen ? ucwords(strtolower(explode(' ', $dosen['nama'])[0])) : 'Peneliti';

        $sumbers = ['Scopus', 'Google Scholar', 'Web of Science', 'SINTA'];
        $publikasi = [];

        for ($i = 0; $i < $count; $i++) {
            $topik = self::TOPIK[intdiv(self::seedFromString($sintaId).$i, 3) % count(self::TOPIK)];
            $tahun = 2016 + ((self::seedFromString($sintaId) + $i * 3) % 9);

            $publikasi[] = [
                'judul' => "Studi $topik: Pendekatan Berbasis Graf untuk Kolaborasi Akademik ($namaPendek, ".($i + 1).')',
                'tahun' => $tahun,
                'doi' => '10.1000/'.strtolower(self::slug($sintaId)).'.'.($i + 1),
                'sumber' => $sumbers[$i % count($sumbers)],
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
