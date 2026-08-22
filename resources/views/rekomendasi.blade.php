s<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rekomendasi Kolaborasi</title>
    <style>
        body { font-family: sans-serif; margin: 20px; line-height: 1.6; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size: 14px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        .container { max-width: 1000px; margin: auto; }
        pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
        .form-group { margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h1>Halaman Rekomendasi Dosen (Sederhana)</h1>
    
    <form action="/rekomendasi" method="GET" style="border: 1px solid #ccc; padding: 20px; margin-bottom: 20px;">
        <div class="form-group">
            <label>Pilih Dosen Target:</label><br>
            <select name="name" style="width: 100%; padding: 5px;">
                @foreach($dosenList as $dosen)
                    <option value="{{ $dosen['nama'] }}" {{ $currentName == $dosen['nama'] ? 'selected' : '' }}>
                        {{ $dosen['nama'] }} (SINTA: {{ $dosen['sinta_id'] }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Metode Algoritma:</label><br>
            <label>
                <input type="radio" name="use_cascading" value="false" {{ !$useCascading ? 'checked' : '' }}> 
                Standar (H-Index & Graf)
            </label>
            <label style="margin-left: 20px;">
                <input type="radio" name="use_cascading" value="true" {{ $useCascading ? 'checked' : '' }}> 
                Cascading Hybrid (S-BERT)
            </label>
        </div>

        <button type="submit" style="padding: 10px 20px;">Cari Rekomendasi</button>
    </form>

    <h2>Hasil Pencarian untuk: {{ $currentName }}</h2>

    @if(count($rekomendasi) > 0)        
        <h3>Raw Data JSON (Seluruh Response API):</h3>
        <pre>{{ json_encode($rekomendasi, JSON_PRETTY_PRINT) }}</pre>

    @else
        <p style="color: red;">Tidak ada data rekomendasi yang ditemukan untuk dosen ini.</p>
    @endif

</div>

</body>
</html>
