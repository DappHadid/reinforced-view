            {{-- ── User Panel (bottom of sidebar) ── --}}
            @if(session('auth_sinta_id'))
            @php
                $authNama   = session('auth_nama', 'Dosen');
                $authSinta  = session('auth_sinta_id', '');
                $authProdi  = session('auth_prodi', '-');
                $words      = array_filter(explode(' ', $authNama));
                $initials   = '';
                foreach (array_slice($words, 0, 2) as $w) { $initials .= strtoupper($w[0]); }
            @endphp

            {{-- ── Panduan Trigger Button ── --}}
            <div class="px-4 pb-2">
                <button
                    type="button"
                    onclick="window.dispatchEvent(new CustomEvent('show-onboarding'))"
                    title="Panduan Penggunaan"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-400 hover:text-primary-600 hover:bg-primary-50 border border-transparent hover:border-primary-100 transition-all group focus:outline-none"
                >
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 group-hover:border-primary-200 group-hover:bg-primary-50 group-hover:text-primary-600 transition-all shadow-xs">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </span>
                    <span class="text-xs font-semibold tracking-wide">Panduan Penggunaan</span>
                </button>
            </div>

            {{-- ── Change PIN Modal ── --}}
            <div
                x-data="changePinPanel()"
                @keydown.escape.window="closePanel()"
                class="relative"
            >
                {{-- Slide-up Panel --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4"
                    x-cloak
                    class="absolute bottom-full left-0 right-0 mb-2 mx-3 rounded-2xl bg-white border border-slate-200 shadow-2xl shadow-slate-300/40 overflow-hidden z-50"
                >
                    {{-- Panel header --}}
                    <div class="flex items-center justify-between px-4 pt-4 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary-50">
                                <svg class="h-3.5 w-3.5 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-800">Ganti PIN</p>
                        </div>
                        <button @click="closePanel()" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form @submit.prevent="submitPin()" class="px-4 pt-3 pb-4 space-y-3" id="form-ganti-pin">
                        @csrf
                        {{-- PIN Lama --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">PIN Saat Ini</label>
                            <div class="flex gap-2 justify-center" x-ref="pinLamaSlots">
                                <template x-for="i in 4" :key="'lama-'+i">
                                    <input
                                        type="password"
                                        maxlength="1"
                                        inputmode="numeric"
                                        pattern="[0-9]"
                                        :id="'pin-lama-' + i"
                                        class="pin-slot w-11 h-11 text-center text-lg font-bold rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-800 focus:outline-none focus:border-primary-500 focus:bg-white transition-colors"
                                        @input="onPinInput($event, 'pin-lama-', i, 4)"
                                        @keydown.backspace="onPinBackspace($event, 'pin-lama-', i)"
                                        @paste.prevent="onPinPaste($event, 'pin-lama-', 4)"
                                    >
                                </template>
                            </div>
                            <p x-show="errors.pin_lama" x-text="errors.pin_lama" class="mt-1.5 text-xs text-red-500 font-medium text-center"></p>
                        </div>

                        {{-- PIN Baru --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">PIN Baru</label>
                            <div class="flex gap-2 justify-center">
                                <template x-for="i in 4" :key="'baru-'+i">
                                    <input
                                        type="password"
                                        maxlength="1"
                                        inputmode="numeric"
                                        pattern="[0-9]"
                                        :id="'pin-baru-' + i"
                                        class="pin-slot w-11 h-11 text-center text-lg font-bold rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-800 focus:outline-none focus:border-primary-500 focus:bg-white transition-colors"
                                        @input="onPinInput($event, 'pin-baru-', i, 4)"
                                        @keydown.backspace="onPinBackspace($event, 'pin-baru-', i)"
                                        @paste.prevent="onPinPaste($event, 'pin-baru-', 4)"
                                    >
                                </template>
                            </div>
                            <p x-show="errors.pin_baru" x-text="errors.pin_baru" class="mt-1.5 text-xs text-red-500 font-medium text-center"></p>
                        </div>

                        {{-- Konfirmasi PIN --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Konfirmasi PIN Baru</label>
                            <div class="flex gap-2 justify-center">
                                <template x-for="i in 4" :key="'konfirm-'+i">
                                    <input
                                        type="password"
                                        maxlength="1"
                                        inputmode="numeric"
                                        pattern="[0-9]"
                                        :id="'pin-konfirm-' + i"
                                        class="pin-slot w-11 h-11 text-center text-lg font-bold rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-800 focus:outline-none focus:border-primary-500 focus:bg-white transition-colors"
                                        @input="onPinInput($event, 'pin-konfirm-', i, 4)"
                                        @keydown.backspace="onPinBackspace($event, 'pin-konfirm-', i)"
                                        @paste.prevent="onPinPaste($event, 'pin-konfirm-', 4)"
                                    >
                                </template>
                            </div>
                            <p x-show="errors.pin_konfirm" x-text="errors.pin_konfirm" class="mt-1.5 text-xs text-red-500 font-medium text-center"></p>
                        </div>

                        {{-- Alert success/error --}}
                        <div x-show="alert.message" :class="alert.success ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-600'"
                             class="rounded-xl border px-3 py-2.5 text-xs font-semibold flex items-center gap-2">
                            <svg x-show="alert.success" class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <svg x-show="!alert.success" class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span x-text="alert.message"></span>
                        </div>

                        {{-- Submit --}}
                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full rounded-xl py-2.5 text-sm font-bold text-white transition-all"
                            :class="loading ? 'bg-slate-300 cursor-not-allowed' : 'bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 shadow-md shadow-primary-500/25'"
                        >
                            <span x-show="!loading">Simpan PIN Baru</span>
                            <span x-show="loading" class="flex items-center justify-center gap-1.5">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                Menyimpan...
                            </span>
                        </button>
                    </form>
                </div>

                {{-- ── User Info Strip ── --}}
                <div class="shrink-0 border-t border-slate-200/80 px-4 py-3">
                    <div class="flex items-center gap-3">

                        {{-- Avatar: foto SINTA + fallback initials --}}
                        <div class="relative h-10 w-10 shrink-0">
                            {{-- Initials (base layer) --}}
                            <div class="absolute inset-0 flex items-center justify-center rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 text-white text-sm font-extrabold select-none shadow-md shadow-primary-500/25">
                                {{ $initials ?: 'DS' }}
                            </div>
                            {{-- SINTA Photo (overlay — hide on error) --}}
                            <img
                                src="https://sinta.kemdikbud.go.id/assets/img/sinta-person/{{ $authSinta }}.jpg"
                                alt="{{ $authNama }}"
                                class="absolute inset-0 h-10 w-10 rounded-xl object-cover"
                                onerror="this.style.display='none'"
                            >
                        </div>

                        {{-- Nama & SINTA ID --}}
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-slate-800 leading-tight">{{ $authNama }}</p>
                            <p class="text-xs text-slate-400 font-medium leading-tight mt-0.5">SINTA: {{ $authSinta }}</p>
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex items-center gap-1 shrink-0">
                            {{-- Ganti PIN button --}}
                            <button
                                id="change-pin-btn"
                                @click="togglePanel()"
                                title="Ganti PIN"
                                aria-label="Ganti PIN"
                                :class="open ? 'text-primary-600 bg-primary-50 border-primary-200' : 'text-slate-400 hover:text-primary-600 hover:bg-primary-50 hover:border-primary-100'"
                                class="flex items-center justify-center h-9 w-9 rounded-xl transition-colors border border-transparent focus:outline-none"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </button>

                            {{-- Logout button --}}
                            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                                @csrf
                                <button
                                    type="submit"
                                    title="Keluar dari sistem"
                                    aria-label="Logout"
                                    class="flex items-center justify-center h-9 w-9 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors border border-transparent hover:border-red-100 focus:outline-none"
                                    onclick="return confirm('Yakin ingin keluar dari sistem?')"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END Change PIN Panel --}}

            @endif

    {{-- ── Change PIN Alpine.js Component ── --}}
    <script>
    function changePinPanel() {
        return {
            open: false,
            loading: false,
            errors: { pin_lama: '', pin_baru: '', pin_konfirm: '' },
            alert: { message: '', success: false },

            togglePanel() {
                this.open = !this.open;
                if (this.open) {
                    this.$nextTick(() => {
                        const el = document.getElementById('pin-lama-1');
                        if (el) el.focus();
                    });
                }
            },

            closePanel() {
                this.open = false;
                this.resetForm();
            },

            resetForm() {
                this.errors = { pin_lama: '', pin_baru: '', pin_konfirm: '' };
                this.alert  = { message: '', success: false };
                this.loading = false;
                ['pin-lama-', 'pin-baru-', 'pin-konfirm-'].forEach(prefix => {
                    for (let i = 1; i <= 4; i++) {
                        const el = document.getElementById(prefix + i);
                        if (el) el.value = '';
                    }
                });
            },

            getPin(prefix) {
                let pin = '';
                for (let i = 1; i <= 4; i++) {
                    const el = document.getElementById(prefix + i);
                    pin += el ? (el.value || '') : '';
                }
                return pin;
            },

            onPinInput(event, prefix, index, total) {
                // Only allow digits
                event.target.value = event.target.value.replace(/\D/g, '').slice(-1);
                if (event.target.value && index < total) {
                    const next = document.getElementById(prefix + (index + 1));
                    if (next) next.focus();
                }
                this.errors = { pin_lama: '', pin_baru: '', pin_konfirm: '' };
                this.alert  = { message: '', success: false };
            },

            onPinBackspace(event, prefix, index) {
                if (!event.target.value && index > 1) {
                    const prev = document.getElementById(prefix + (index - 1));
                    if (prev) { prev.value = ''; prev.focus(); }
                }
            },

            onPinPaste(event, prefix, total) {
                const text = (event.clipboardData || window.clipboardData).getData('text');
                const digits = text.replace(/\D/g, '').slice(0, total);
                for (let i = 0; i < digits.length; i++) {
                    const el = document.getElementById(prefix + (i + 1));
                    if (el) el.value = digits[i];
                }
                const last = document.getElementById(prefix + Math.min(digits.length, total));
                if (last) last.focus();
            },

            async submitPin() {
                const pinLama    = this.getPin('pin-lama-');
                const pinBaru    = this.getPin('pin-baru-');
                const pinKonfirm = this.getPin('pin-konfirm-');

                // Client-side validation
                this.errors = { pin_lama: '', pin_baru: '', pin_konfirm: '' };
                let hasError = false;
                if (pinLama.length < 4)    { this.errors.pin_lama = 'PIN saat ini harus 4 digit.'; hasError = true; }
                if (pinBaru.length < 4)    { this.errors.pin_baru = 'PIN baru harus 4 digit.'; hasError = true; }
                if (pinKonfirm.length < 4) { this.errors.pin_konfirm = 'Konfirmasi PIN harus 4 digit.'; hasError = true; }
                if (!hasError && pinBaru !== pinKonfirm) {
                    this.errors.pin_konfirm = 'Konfirmasi PIN tidak cocok.'; hasError = true;
                }
                if (hasError) return;

                this.loading = true;
                this.alert   = { message: '', success: false };

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content
                                  || document.querySelector('#form-ganti-pin [name="_token"]')?.value
                                  || '';
                    const res = await fetch('{{ route("profil.ganti_pin") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            pin_lama:    pinLama,
                            pin_baru:    pinBaru,
                            pin_konfirm: pinKonfirm,
                        }),
                    });

                    const json = await res.json();

                    if (res.ok && json.success) {
                        this.alert = { message: json.message || 'PIN berhasil diperbarui!', success: true };
                        this.resetForm();
                        setTimeout(() => { this.open = false; this.alert = { message: '', success: false }; }, 2000);
                    } else {
                        // Handle Laravel validation errors or custom errors
                        if (json.errors) {
                            if (json.errors.pin_lama)    this.errors.pin_lama    = json.errors.pin_lama[0];
                            if (json.errors.pin_baru)    this.errors.pin_baru    = json.errors.pin_baru[0];
                            if (json.errors.pin_konfirm) this.errors.pin_konfirm = json.errors.pin_konfirm[0];
                        } else {
                            this.alert = { message: json.message || 'Terjadi kesalahan.', success: false };
                        }
                    }
                } catch (err) {
                    this.alert = { message: 'Gagal menghubungi server. Coba lagi.', success: false };
                } finally {
                    this.loading = false;
                }
            },
        };
    }
    </script>
