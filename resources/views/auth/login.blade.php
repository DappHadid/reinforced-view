<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — REINFORCED</title>
    <meta name="description" content="Masuk ke sistem REINFORCED — Research Collaborator Recommendation Engine">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* ── Subtle background ── */
        .login-bg {
            background-color: #f3f4f6;
            background-image:
                radial-gradient(circle at 25% 25%, #e5e7eb 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, #e9eaec 0%, transparent 50%);
        }

        /* ── Card ── */
        .login-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            width: 100%;
            max-width: 380px;
            padding: 2.5rem 2rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07), 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        /* ── Logo ── */
        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.75rem;
        }
        .logo-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: #1f2937;
            color: #ffffff;
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.875rem;
            letter-spacing: -0.5px;
        }
        .brand-name {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            letter-spacing: 0.5px;
        }
        .brand-sub {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.25rem;
            text-align: center;
            line-height: 1.4;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid #f0f0f0;
            margin-bottom: 1.5rem;
        }

        /* ── Label ── */
        .field-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.4rem;
        }

        /* ── Input ── */
        .field-wrap {
            position: relative;
        }
        .field-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            display: flex;
            align-items: center;
        }
        .login-input {
            width: 100%;
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.7rem 0.875rem 0.7rem 2.5rem;
            font-size: 0.875rem;
            color: #111827;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }
        .login-input::placeholder { color: #d1d5db; }
        .login-input:focus {
            outline: none;
            border-color: #6b7280;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.12);
        }
        .login-input.error-state {
            border-color: #fca5a5;
            background: #fff5f5;
        }
        .login-input.error-state:focus {
            box-shadow: 0 0 0 3px rgba(252, 165, 165, 0.2);
        }

        /* ── Password toggle button ── */
        .pw-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            display: flex;
            align-items: center;
            transition: color 0.15s;
            padding: 0;
        }
        .pw-toggle:hover { color: #4b5563; }

        /* ── Error message ── */
        .field-error {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            margin-top: 0.375rem;
            font-size: 0.75rem;
            color: #ef4444;
            font-weight: 500;
        }

        /* ── Session alert ── */
        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            border: 1px solid #fecaca;
            background: #fef2f2;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
        }
        .alert-error svg { color: #f87171; flex-shrink: 0; margin-top: 1px; }
        .alert-error p  { font-size: 0.8125rem; color: #dc2626; font-weight: 500; }

        /* ── Submit button ── */
        .btn-login {
            width: 100%;
            background: #1f2937;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 0.15s, box-shadow 0.15s, transform 0.1s;
            margin-top: 0.5rem;
        }
        .btn-login:hover {
            background: #374151;
            box-shadow: 0 4px 16px rgba(31, 41, 55, 0.22);
            transform: translateY(-1px);
        }
        .btn-login:active  { transform: translateY(0); background: #111827; }
        .btn-login:disabled { opacity: 0.55; cursor: not-allowed; transform: none; box-shadow: none; }

        /* ── Footer hint ── */
        .hint-text {
            margin-top: 1.25rem;
            text-align: center;
            font-size: 0.72rem;
            color: #9ca3af;
            line-height: 1.5;
        }

        /* ── Copyright ── */
        .copyright {
            position: fixed;
            bottom: 1.25rem;
            left: 0; right: 0;
            text-align: center;
            font-size: 0.7rem;
            color: #d1d5db;
            user-select: none;
        }

        /* ── Spinner animation ── */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 0.8s linear infinite; }
    </style>
</head>
<body class="login-bg" x-data="loginForm()">

    {{-- ── MAIN CARD ── --}}
    <div class="login-card">

        {{-- Logo & Brand --}}
        <div class="logo-wrap">
            <div class="logo-icon">R</div>
            <span class="brand-name">REINFORCED</span>
            <span class="brand-sub">Research Collaborator Recommendation Engine</span>
        </div>

        <hr class="divider">

        {{-- Session error global --}}
        @if(session('error'))
            <div class="alert-error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- ── LOGIN FORM ── --}}
        <form method="POST" action="{{ route('login.post') }}" novalidate @submit="onSubmit">
            @csrf

            {{-- SINTA ID Field --}}
            <div style="margin-bottom:1rem">
                <label for="sinta_id" class="field-label">Nomor SINTA ID</label>
                <div class="field-wrap">
                    <span class="field-icon">
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
                        placeholder="Contoh: 6200123"
                        autocomplete="username"
                        value="{{ old('sinta_id') }}"
                        class="login-input {{ $errors->has('sinta_id') ? 'error-state' : '' }}"
                        x-ref="sintaInput"
                        @input="filterNumericOnly($event)"
                    >
                </div>
                @error('sinta_id')
                    <p class="field-error">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password Field --}}
            <div style="margin-bottom:1.25rem">
                <label for="password" class="field-label">Password</label>
                <div class="field-wrap">
                    <span class="field-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                        </svg>
                    </span>
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        class="login-input {{ $errors->has('password') ? 'error-state' : '' }}"
                        style="padding-right:2.75rem"
                    >
                    <button type="button" class="pw-toggle"
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
                    <p class="field-error">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button
                type="submit"
                id="btn-submit-login"
                class="btn-login"
                :disabled="loading"
            >
                <span x-show="!loading" style="display:flex;align-items:center;gap:0.5rem">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Masuk ke Sistem
                </span>
                <span x-show="loading" style="display:flex;align-items:center;gap:0.5rem">
                    <svg class="animate-spin" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    </svg>
                    Memverifikasi...
                </span>
            </button>
        </form>

        {{-- Footer hint --}}
        <p class="hint-text">
            Gunakan Nomor SINTA ID (7 digit) dan password default sistem
        </p>
    </div>

    {{-- Copyright --}}
    <p class="copyright">© {{ date('Y') }} REINFORCED &middot; PKM Research System</p>

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
