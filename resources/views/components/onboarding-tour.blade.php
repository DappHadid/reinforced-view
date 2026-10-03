{{-- ═══════════════════════════════════════════════════════
     INTERACTIVE ONBOARDING TOUR (Driver.js)
═══════════════════════════════════════════════════════ --}}
@if(session('auth_sinta_id'))
<style>
    .driver-welcome-step {
        min-width: 400px !important;
        padding: 32px !important;
    }
    .driver-welcome-step .driver-popover-title {
        font-size: 1.5rem !important;
        margin-bottom: 0.75rem !important;
    }
    .driver-welcome-step .driver-popover-description {
        font-size: 1rem !important;
        line-height: 1.7 !important;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sintaId = "{{ session('auth_sinta_id') }}";
    const tourStatusKey = 'reinforced_tour_status_' + sintaId;
    const tourStepKey = 'reinforced_tour_step_' + sintaId;

    // Custom event triggered by the sidebar button
    window.addEventListener('show-onboarding', () => {
        localStorage.setItem(tourStatusKey, 'active');
        localStorage.setItem(tourStepKey, '0');
        // Force start at dashboard
        if (window.location.pathname !== '/' && window.location.pathname !== '/dashboard') {
            window.location.href = "{{ route('dashboard') }}";
        } else {
            startTour(0);
        }
    });

    // Auto-start for first timers
    if (!localStorage.getItem(tourStatusKey)) {
        localStorage.setItem(tourStatusKey, 'active');
        localStorage.setItem(tourStepKey, '0');
        if (window.location.pathname !== '/' && window.location.pathname !== '/dashboard') {
            window.location.href = "{{ route('dashboard') }}";
            return;
        }
    }

    if (localStorage.getItem(tourStatusKey) === 'active') {
        let startStep = parseInt(localStorage.getItem(tourStepKey) || '0', 10);
        startTour(startStep);
    }

    function startTour(startIdx) {
        const currentPath = window.location.pathname;

        const driverObj = window.driver.js.driver({
            showProgress: true,
            animate: true,
            allowClose: true, 
            doneBtnText: 'Selesai',
            nextBtnText: 'Lanjut &rarr;',
            prevBtnText: '&larr; Kembali',
            overlayColor: 'rgba(9, 9, 9, 0.4)', // Slate-900 with 40% opacity for soft dark
            overlayOpacity: 0.4, // Fallback for some versions
            popoverClass: 'driver-theme-soft !font-sans',
            onNextClick: (elem, step, options) => {
                const currentIdx = driverObj.getActiveIndex();
                const currentStepConf = steps[currentIdx];
                
                if (currentStepConf.isPageTransition) {
                    localStorage.setItem(tourStepKey, (currentIdx + 1).toString());
                    window.location.href = currentStepConf.transitionUrl;
                    return;
                }
                
                localStorage.setItem(tourStepKey, (currentIdx + 1).toString());
                driverObj.moveNext();
            },
            onPrevClick: (elem, step, options) => {
                const currentIdx = driverObj.getActiveIndex();
                const prevIdx = currentIdx - 1;
                if (prevIdx >= 0 && steps[prevIdx].isPageTransitionBack) {
                    localStorage.setItem(tourStepKey, prevIdx.toString());
                    window.location.href = steps[prevIdx].transitionUrlBack;
                    return;
                }
                localStorage.setItem(tourStepKey, prevIdx.toString());
                driverObj.movePrevious();
            },
            onDestroyed: () => {
                // Mark as completed
                localStorage.setItem(tourStatusKey, 'completed');
                localStorage.removeItem(tourStepKey);
            }
        });

        const steps = [
            // STEP 0: Welcome (Any page, usually Dashboard)
            { 
                popover: { 
                    title: '👋 Selamat Datang!', 
                    description: 'Halo! Mari kita mulai tur singkat untuk mengenal fitur-fitur utama sistem REINFORCED ini. Klik <strong>Lanjut</strong> untuk memulai.',
                    side: "over", align: 'center',
                    popoverClass: 'driver-welcome-step'
                } 
            },
            // STEP 1: Dashboard Link
            { 
                element: '#nav-dashboard', 
                popover: { 
                    title: 'Dashboard', 
                    description: 'Di menu ini, Anda bisa memantau ringkasan statistik dan grafik kolaborasi riset di seluruh institusi.',
                    side: "right", align: 'start'
                } 
            },
            // STEP 2: Rekomendasi Link (Transitions to /rekomendasi)
            { 
                element: '#nav-rekomendasi', 
                isPageTransition: true,
                transitionUrl: "{{ route('rekomendasi') }}",
                popover: { 
                    title: 'Cari Kolaborator', 
                    description: 'Ini adalah fitur utama sistem ini! Mari kita klik Lanjut untuk membuka halaman Rekomendasi.',
                    side: "right", align: 'start'
                } 
            },
            // STEP 3: On Rekomendasi Page (Search Box)
            { 
                element: '#tour-search-section', 
                isPageTransitionBack: true,
                transitionUrlBack: "{{ route('dashboard') }}",
                popover: { 
                    title: 'Pencarian Kolaborator', 
                    description: 'Sistem akan menampilkan kolaborator terbaik untuk Anda. Anda juga bisa mencari dosen lain untuk melihat jaringannya.',
                    side: "bottom", align: 'center'
                } 
            },
            // STEP 4: On Rekomendasi Page (Graph)
            { 
                element: '#tour-graph-section', 
                popover: { 
                    title: 'Visualisasi Jaringan', 
                    description: 'Grafik ini menunjukkan secara visual bagaimana Anda terhubung dengan kandidat kolaborator melalui peneliti penghubung.',
                    side: "top", align: 'center'
                } 
            },
            // STEP 5: On Rekomendasi Page (Table)
            { 
                element: '#tour-table-section', 
                popover: { 
                    title: 'Daftar Rekomendasi', 
                    description: 'Di sini Anda bisa melihat detail skor kemiripan, statistik tiap kandidat, dan tombol "Beri Nilai" untuk menilai seberapa cocok rekomendasi ini.',
                    side: "top", align: 'start'
                } 
            },
            // STEP 6: Dosen Link (Transitions to /dosen)
            { 
                element: '#nav-dosen', 
                isPageTransition: true,
                transitionUrl: "{{ route('dosen.index') }}",
                popover: { 
                    title: 'Profil Dosen', 
                    description: 'Direktori lengkap seluruh dosen. Mari kita Lanjut untuk melihatnya.',
                    side: "right", align: 'start'
                } 
            },
            // STEP 7: On Dosen Index (Search)
            {
                element: '#search-open-btn',
                isPageTransitionBack: true,
                transitionUrlBack: "{{ route('rekomendasi') }}",
                popover: { 
                    title: 'Cari Dosen', 
                    description: 'Gunakan fitur ini untuk memfilter atau mencari dosen spesifik berdasarkan nama atau fakultas.',
                    side: "bottom", align: 'start'
                }
            },
            // STEP 8: On Dosen Index (Grid -> Transitions to Show)
            {
                element: '#dosen-grid',
                isPageTransition: true,
                transitionUrl: "{{ route('dosen.show', session('auth_sinta_id', '0')) }}",
                popover: { 
                    title: 'Direktori Peneliti', 
                    description: 'Daftar semua peneliti ditampilkan di sini. Mari kita lihat profil Anda sendiri.',
                    side: "top", align: 'center'
                }
            },
            // STEP 9: On Dosen Show (Header)
            {
                element: '#tour-profile-header',
                isPageTransitionBack: true,
                transitionUrlBack: "{{ route('dosen.index') }}",
                popover: { 
                    title: 'Detail Profil', 
                    description: 'Ini adalah halaman profil personal. Menampilkan informasi detail seperti sinta id, usia akademik, dan total kolaborator.',
                    side: "bottom", align: 'center'
                }
            },
            // STEP 10: On Dosen Show (Graph)
            {
                element: '#tour-profile-graph',
                popover: { 
                    title: 'Jaringan Personal', 
                    description: 'Grafik ini fokus pada jaringan kolaborasi langsung dari peneliti yang sedang dilihat.',
                    side: "top", align: 'center'
                }
            },
            // STEP 11: On Dosen Show (Top Rekomendasi)
            {
                element: '#tour-profile-rekomendasi',
                popover: { 
                    title: 'Top Rekomendasi Personal', 
                    description: 'Daftar kandidat kolaborator teratas khusus untuk peneliti ini juga ditampilkan secara ringkas di sini.',
                    side: "top", align: 'start'
                }
            },
            // STEP 12: On Dosen Show (Publikasi)
            {
                element: '#tour-profile-publikasi',
                popover: { 
                    title: 'Riwayat Publikasi', 
                    description: 'Semua rekam jejak karya tulis dan publikasi ilmiah peneliti dapat ditelusuri di tabel ini.',
                    side: "top", align: 'start'
                }
            },
            // STEP 13: Evaluasi Link (Transitions to /evaluasi)
            { 
                element: '#nav-evaluasi', 
                isPageTransition: true,
                transitionUrl: "{{ route('evaluasi') }}",
                popover: { 
                    title: 'Hasil Evaluasi', 
                    description: 'Mari kita menuju ke menu Evaluasi untuk melihat riwayat penilaian dari pengguna.',
                    side: "right", align: 'start'
                } 
            },
            // STEP 14: Evaluasi Page (Table)
            { 
                element: '#tour-eval-table', 
                isPageTransitionBack: true,
                transitionUrlBack: "{{ route('dosen.show', session('auth_sinta_id', '0')) }}",
                popover: { 
                    title: 'Riwayat Penilaian', 
                    description: 'Tabel ini menampilkan daftar riwayat penilaian atau *feedback* mengenai kualitas rekomendasi yang sudah diberikan.',
                    side: "top", align: 'start'
                } 
            },
            // STEP 15: Ganti PIN
            { 
                element: '#change-pin-btn', 
                popover: { 
                    title: 'Keamanan Akun', 
                    description: 'Terakhir, jangan lupa ganti PIN Anda di sini demi keamanan.',
                    side: "top", align: 'start'
                } 
            }
        ];

        driverObj.setSteps(steps);
        
        // Safety logic if user goes to wrong page while tour is active
        if (startIdx >= 3 && startIdx <= 5 && currentPath.indexOf('rekomendasi') === -1) {
             startIdx = 2; localStorage.setItem(tourStepKey, '2');
        } else if (startIdx >= 7 && startIdx <= 8 && currentPath.indexOf('dosen') === -1) {
             startIdx = 6; localStorage.setItem(tourStepKey, '6');
        } else if (startIdx >= 9 && startIdx <= 12 && currentPath.indexOf('dosen/') === -1) {
             startIdx = 8; localStorage.setItem(tourStepKey, '8');
        } else if (startIdx === 14 && currentPath.indexOf('evaluasi') === -1) {
             startIdx = 13; localStorage.setItem(tourStepKey, '13');
        }

        driverObj.drive(startIdx);
    }
});
</script>
@endif
