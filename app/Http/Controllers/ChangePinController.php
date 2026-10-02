<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChangePinController extends Controller
{
    /**
     * Tampilkan form ganti PIN (via AJAX — return JSON).
     * Form ada di sidebar sebagai modal, jadi response berupa JSON.
     */
    public function update(Request $request)
    {
        $request->validate([
            'pin_lama'     => ['required', 'digits:4'],
            'pin_baru'     => ['required', 'digits:4'],
            'pin_konfirm'  => ['required', 'same:pin_baru'],
        ], [
            'pin_lama.required'    => 'PIN lama wajib diisi.',
            'pin_lama.digits'      => 'PIN harus tepat 4 digit angka.',
            'pin_baru.required'    => 'PIN baru wajib diisi.',
            'pin_baru.digits'      => 'PIN baru harus tepat 4 digit angka.',
            'pin_konfirm.required' => 'Konfirmasi PIN wajib diisi.',
            'pin_konfirm.same'     => 'Konfirmasi PIN tidak cocok dengan PIN baru.',
        ]);

        $sintaId  = session('auth_sinta_id');
        $pinLama  = $request->input('pin_lama');
        $pinBaru  = $request->input('pin_baru');

        // Ambil record PIN kustom dari DB (jika ada)
        $record = DB::table('user_pins')->where('sinta_id', $sintaId)->first();

        // Verifikasi PIN lama:
        // Jika belum punya PIN kustom → bandingkan dengan default '1234'
        // Jika sudah punya PIN kustom → bandingkan dengan hash di DB
        if ($record) {
            $pinValid = Hash::check($pinLama, $record->pin_hash);
        } else {
            $pinValid = ($pinLama === '1234');
        }

        if (! $pinValid) {
            return response()->json([
                'success' => false,
                'message' => 'PIN lama yang Anda masukkan salah.',
            ], 422);
        }

        // Simpan / update PIN baru
        DB::table('user_pins')->updateOrInsert(
            ['sinta_id' => $sintaId],
            [
                'pin_hash'   => Hash::make($pinBaru),
                'updated_at' => now(),
                'created_at' => $record ? $record->created_at : now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'PIN berhasil diperbarui.',
        ]);
    }
}
