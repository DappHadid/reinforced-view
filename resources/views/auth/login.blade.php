<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — REINFORCED</title>
    <meta name="description" content="Masuk ke sistem REINFORCED — Research Collaborator Recommendation Engine">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-8 bg-[#ffedd5]" x-data="loginForm()">

    <div class="flex w-full max-w-5xl bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] overflow-hidden min-h-[600px]">
        
        {{-- ── LEFT SIDE (FORM) ── --}}
        <div class="flex-1 p-8 sm:p-12 flex flex-col justify-center relative">
            <div class="max-w-[360px] w-full mx-auto">
                {{-- Logo & Brand --}}
                <div class="flex flex-col items-center mb-7">
                    <!-- <div class="logo-icon">R</div> -->
                    <span class="text-xl font-bold text-gray-900 tracking-wide">REINFORCED</span>
                    <span class="text-xs text-gray-400 mt-1 text-center leading-relaxed">Research Collaborator Recommendation Engine</span>
                </div>

                <hr class="border-0 border-t border-gray-100 mb-6">

                {{-- Session error global --}}
                @if(session('error'))
                    <div class="flex items-start gap-2.5 border border-red-200 bg-red-50 rounded-xl p-3 mb-5">
                        <svg class="text-red-400 shrink-0 mt-[1px]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <p class="text-[13px] text-red-600 font-medium">{{ session('error') }}</p>
                    </div>
                @endif

                {{-- ── LOGIN FORM ── --}}
                <form method="POST" action="{{ route('login.post') }}" novalidate @submit="onSubmit">
                    @csrf

                    {{-- SINTA ID Field --}}
                    <div class="mb-4">
                        <label for="sinta_id" class="block text-[11px] font-semibold tracking-wider uppercase text-gray-500 mb-1.5">Nomor SINTA ID</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none flex items-center">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                id="sinta_id"
                                name="sinta_id"
                                inputmode="numeric"
                                pattern="[0-9]{7}"
                                maxlength="7"
                                placeholder=""
                                autocomplete="username"
                                value="{{ old('sinta_id') }}"
                                class="w-full bg-gray-50 border-[1.5px] border-gray-200 rounded-xl py-2.5 pr-3.5 pl-10 text-sm text-gray-900 transition-all duration-150 placeholder-gray-300 focus:outline-none focus:border-gray-500 focus:bg-white focus:ring-[3px] focus:ring-gray-500/10 {{ $errors->has('sinta_id') ? 'border-red-300 bg-red-50 focus:ring-red-300/20' : '' }}"
                                x-ref="sintaInput"
                                @input="filterNumericOnly($event)"
                            >
                        </div>
                        @error('sinta_id')
                            <p class="flex items-center gap-1.5 mt-1.5 text-xs text-red-500 font-medium">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password Field --}}
                    <div class="mb-5">
                        <label for="password" class="block text-[11px] font-semibold tracking-wider uppercase text-gray-500 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none flex items-center">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                                </svg>
                            </span>
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                id="password"
                                name="password"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                @input="filterNumericOnly($event)"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                class="w-full bg-gray-50 border-[1.5px] border-gray-200 rounded-xl py-2.5 pr-10 pl-10 text-sm text-gray-900 transition-all duration-150 placeholder-gray-300 focus:outline-none focus:border-gray-500 focus:bg-white focus:ring-[3px] focus:ring-gray-500/10 {{ $errors->has('password') ? 'border-red-300 bg-red-50 focus:ring-red-300/20' : '' }}"
                            >
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 flex items-center transition-colors duration-150 hover:text-gray-600 focus:outline-none"
                                    @click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                                <svg x-show="!showPassword" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg x-show="showPassword" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="flex items-center gap-1.5 mt-1.5 text-xs text-red-500 font-medium">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        id="btn-submit-login"
                        class="w-full bg-[#E66200] text-white border-0 rounded-xl py-3 px-4 text-sm font-semibold flex items-center justify-center gap-2 transition-all duration-150 mt-2 hover:bg-[#cc5700] hover:shadow-[0_4px_16px_rgba(230,98,0,0.3)] hover:-translate-y-[1px] active:translate-y-0 active:bg-[#b34c00] disabled:opacity-55 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none"
                        :disabled="loading"
                    >
                        <span x-show="!loading" class="flex items-center gap-2">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                            </svg>
                            Masuk ke Sistem
                        </span>
                        <span x-show="loading" class="flex items-center gap-2" style="display: none;">
                            <svg class="animate-spin" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            Memverifikasi...
                        </span>
                    </button>
                </form>

                {{-- Footer hint --}}
                <p class="mt-5 text-center text-xs text-gray-400 leading-relaxed">
                    Gunakan Nomor SINTA ID dan password
                </p>

                {{-- Copyright --}}
                <p class="mt-10 text-center text-[11px] text-gray-300 select-none">© {{ date('Y') }} REINFORCED &middot; Research System</p>
            </div>
        </div>

        {{-- ── RIGHT SIDE (IMAGE) ── --}}
        <div class="hidden md:block flex-1 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/login.webp') }}');"></div>

    </div>

    <script>
        function loginForm() {
            return {
                showPassword: false,
                loading: false,

                onSubmit(e) {
                    this.loading = true;
                },

                filterNumericOnly(e) {
                    const val = e.target.value.replace(/\D/g, '');
                    e.target.value = val;
                }
            };
        }
    </script>
</body>
</html>
