<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rekomendasi Kolaborasi</title>
    <!-- Include Vis.js -->
    <script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <style>
        body { font-family: sans-serif; margin: 20px; line-height: 1.6; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size: 14px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        .container { max-width: 1000px; margin: auto; }
        pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
        .form-group { margin-bottom: 15px; }
        #network-graph {
            width: 100%;
            height: 600px;
            border: 1px solid lightgray;
            background-color: #ffffff;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Halaman Rekomendasi Dosen</h1>
    
    <form action="/rekomendasi" method="GET" style="border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; border-radius: 8px;">
        <div class="form-group">
            <label><strong>Pilih Dosen Target:</strong></label><br>
            <select name="name" style="width: 100%; padding: 8px; margin-top: 5px;">
                @foreach($dosenList as $dosen)
                    <option value="{{ $dosen['nama'] }}" {{ $currentName == $dosen['nama'] ? 'selected' : '' }}>
                        {{ $dosen['nama'] }} (SINTA: {{ $dosen['sinta_id'] }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label><strong>Metode Algoritma:</strong></label><br>
            <label style="margin-right: 20px; display: inline-block; margin-top: 5px;">
                <input type="radio" name="use_cascading" value="false" {{ !$useCascading ? 'checked' : '' }}> 
                Standar (H-Index & Graf)
            </label>
            <label style="display: inline-block; margin-top: 5px;">
                <input type="radio" name="use_cascading" value="true" {{ $useCascading ? 'checked' : '' }}> 
                Cascading Hybrid (S-BERT)
            </label>
        </div>

        <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Cari Rekomendasi</button>
    </form>

    <h2>Hasil Pencarian untuk: {{ $currentName }}</h2>

    @if(count($rekomendasi) > 0)
        <!-- TEMPAT GRAFIK -->
        <h3>Grafik Relasi Jaringan (Interactive)</h3>
        <div id="network-graph"><div style="padding: 20px; color: gray;">Memuat grafik relasi...</div></div>
        
        <h3>Tabel Rekomendasi</h3>
        <table>
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Nama Rekomendasi</th>
                    <th>SINTA ID</th>
                    <th>Skor Kemiripan (ANE)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekomendasi as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['Rekomendasi_Nama'] }}</td>
                    <td>{{ $item['Rekomendasi_SINTA_ID'] }}</td>
                    <td>{{ round($item['Skor Kemiripan'], 4) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: red;">Tidak ada data rekomendasi yang ditemukan untuk dosen ini.</p>
    @endif

</div>

@if(count($rekomendasi) > 0)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const targetName = "{{ $currentName }}";
        const rekomendasiData = @json($rekomendasi);
        const rekomNames = rekomendasiData.map(item => item.Rekomendasi_Nama).join(",");

        if (rekomNames.length > 0) {
            const container = document.getElementById('network-graph');

            fetch(`/graph-data?target_name=${encodeURIComponent(targetName)}&rekom_names=${encodeURIComponent(rekomNames)}`)
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        container.innerHTML = ''; 
                        
                        const nodes = new vis.DataSet(response.data.nodes.map(n => {
                            let bg = '#E0F2FE'; 
                            let border = '#38BDF8';
                            let fontColor = '#1E293B';
                            let size = 18;
                            let borderWidth = 2;
                            
                            if (n.group === 'target') {
                                bg = '#FEE2E2';
                                border = '#EF4444'; 
                                size = 28;
                                borderWidth = 3;
                            } else if (n.group === 'recommendation') {
                                bg = '#DCFCE7'; // light green
                                border = '#22C55E'; // green
                                size = 24;
                                borderWidth = 3;
                            }
                            
                            return {
                                id: n.id,
                                label: n.label,
                                group: n.group,
                                color: { 
                                    background: bg, 
                                    border: border,
                                    hover: { background: border, border: bg },
                                    highlight: { background: bg, border: '#0F172A' }
                                },
                                shape: 'box',
                                margin: { top: 10, right: 15, bottom: 10, left: 15 },
                                borderWidth: borderWidth,
                                font: { 
                                    size: size - 4, // menyesuaikan ukuran font 
                                    color: fontColor, 
                                    face: 'Inter, sans-serif',
                                    bold: n.group === 'target' ? true : false
                                },
                                shadow: { enabled: true, color: 'rgba(0,0,0,0.15)', size: 10, x: 2, y: 3 },
                                title: n.group === 'target' ? 'Dosen Target: ' + n.label : (n.group === 'recommendation' ? 'Rekomendasi: ' + n.label : 'Koneksi: ' + n.label)
                            };
                        }));
                        
                        // Parse edges
                        const edges = new vis.DataSet(response.data.edges.map(e => {
                            const isRecommended = (e.label === 'recommended');
                            return {
                                from: e.from,
                                to: e.to,
                                label: e.label,
                                font: { align: 'top', size: 11, color: '#64748B', face: 'Inter, sans-serif', background: 'rgba(255,255,255,0.9)' },
                                color: { color: isRecommended ? '#CBD5E1' : '#94A3B8', highlight: '#475569', hover: '#64748B' },
                                dashes: isRecommended,
                                width: isRecommended ? 2 : 1.5,
                                arrows: { to: { enabled: true, scaleFactor: 0.6 } },
                                smooth: { type: 'continuous', roundness: 0.3 }
                            };
                        }));
                        
                        const data = { nodes: nodes, edges: edges };
                        const options = {
                            physics: {
                                solver: 'barnesHut',
                                barnesHut: {
                                    gravitationalConstant: -8000,
                                    centralGravity: 0.3,
                                    springLength: 200,
                                    springConstant: 0.04,
                                    damping: 0.9,
                                    avoidOverlap: 1
                                },
                                stabilization: {
                                    enabled: true,
                                    iterations: 200,
                                    updateInterval: 25
                                }
                            },
                            interaction: { 
                                hover: true, 
                                tooltipDelay: 150,
                                zoomView: true,
                                dragView: true,
                                zoomSpeed: 0.5,
                                keyboard: false,
                                navigationButtons: false,
                                minZoom: 0.5,
                                maxZoom: 2.5
                            },
                            layout: { improvedLayout: true }
                        };
                        
                        const network = new vis.Network(container, data, options);
                        
                        // Matikan physics setelah grafik stabil agar tidak bergerak terus
                        network.on("stabilized", function () {
                            network.setOptions({ physics: false });
                            network.fit({ animation: { duration: 600, easingFunction: "easeOutQuad" } });
                        });

                        // Batasi zoom in dan zoom out dengan flag guard agar tidak loop
                        const MIN_ZOOM = 0.5;
                        const MAX_ZOOM = 2.5;
                        let isZoomClamping = false;

                        network.on("zoom", function () {
                            if (isZoomClamping) return;
                            const scale = network.getScale();
                            if (scale < MIN_ZOOM) {
                                isZoomClamping = true;
                                network.moveTo({ scale: MIN_ZOOM });
                                isZoomClamping = false;
                            } else if (scale > MAX_ZOOM) {
                                isZoomClamping = true;
                                network.moveTo({ scale: MAX_ZOOM });
                                isZoomClamping = false;
                            }
                        });

                        network.on("dragEnd", function () {
                            const containerWidth = container.offsetWidth;
                            const containerHeight = container.offsetHeight;
                            const nodeIds = nodes.getIds();
                            let anyVisible = false;

                            for (const id of nodeIds) {
                                const pos = network.canvasToDOM(network.getPosition(id));
                                if (
                                    pos.x >= -50 && pos.x <= containerWidth + 50 &&
                                    pos.y >= -50 && pos.y <= containerHeight + 50
                                ) {
                                    anyVisible = true;
                                    break;
                                }
                            }

                            if (!anyVisible) {
                                network.fit({ animation: { duration: 500, easingFunction: "easeOutQuad" } });
                            }
                        });

                    } else {
                        container.innerHTML = '<div style="color:red; padding: 20px;">Gagal memuat grafik dari API.</div>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    container.innerHTML = '<div style="color:red; padding: 20px;">Terjadi kesalahan saat memuat grafik.</div>';
                });
        }
    });
</script>
@endif

</body>
</html>
