<?php

namespace App\Http\Controllers;

use App\Support\ApiDataProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (session('auth_sinta_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     * Validasi: SINTA ID harus ada di Neo4j, PIN dicek dari DB (default 1234).
     */
    public function login(Request $request)
    {
        $request->validate([
            'sinta_id' => ['required', 'digits:7'],
            'password' => ['required'],
        ], [
            'sinta_id.required' => 'Nomor SINTA ID wajib diisi.',
            'sinta_id.digits'   => 'SINTA ID harus tepat 7 digit angka.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $sintaId = $request->input('sinta_id');
        $pin     = $request->input('password');

        // ── Verifikasi SINTA ID ke API (Neo4j) ───────────────────────────────
        $detail = ApiDataProvider::dosenDetail($sintaId);
        if (!$detail) {
            return back()
                ->withInput($request->only('sinta_id'))
                ->withErrors(['sinta_id' => 'SINTA ID tidak ditemukan dalam sistem.']);
        }

        // ── Verifikasi PIN ────────────────────────────────────────────────────
        // Cek PIN kustom di DB; jika belum ada → gunakan default '1234'
        $record   = DB::table('user_pins')->where('sinta_id', $sintaId)->first();
        $pinValid = $record
            ? Hash::check($pin, $record->pin_hash)
            : ($pin === '1234');

        if (! $pinValid) {
            return back()
                ->withInput($request->only('sinta_id'))
                ->withErrors(['password' => 'PIN yang Anda masukkan salah.']);
        }

        // ── Simpan sesi ───────────────────────────────────────────────────────
        session([
            'auth_sinta_id' => $sintaId,
            'auth_nama'     => $detail['hasName'] ?? $detail['ns0__hasName'] ?? 'Dosen',
            'auth_prodi'    => $detail['hasDepartment'] ?? $detail['ns0__hasDepartment'] ?? '-',
        ]);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        $request->session()->forget(['auth_sinta_id', 'auth_nama', 'auth_prodi']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
