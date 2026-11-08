<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Reset password memakai tabel password_resets dari versi lama
 * (token disimpan dalam bentuk hash SHA-256, berlaku 2 jam).
 */
class PasswordResetController extends Controller
{
    private const VALID_HOURS = 2;

    public function showRequest()
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', strtolower($request->input('email')))->first();
        $devLink = null;

        if ($user) {
            $token = Str::random(64);

            DB::table('password_resets')->where('expires_at', '<', now())->delete();
            DB::table('password_resets')->where('user_id', $user->id)->delete();
            DB::table('password_resets')->insert([
                'user_id' => $user->id,
                'token' => hash('sha256', $token),
                'expires_at' => now()->addHours(self::VALID_HOURS),
            ]);

            $link = route('password.reset', $token);

            try {
                Mail::raw(
                    "Halo {$user->full_name},\n\nKlik link berikut untuk mereset password SWAP-IT Anda:\n{$link}\n\n"
                    . 'Link berlaku ' . self::VALID_HOURS . " jam. Abaikan email ini jika Anda tidak memintanya.",
                    fn ($m) => $m->to($user->email)->subject('Reset Password - SWAP-IT')
                );
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim email reset password: ' . $e->getMessage());
            }

            // Mode pengembangan: tampilkan link langsung karena email belum dikonfigurasi
            if (config('app.debug')) {
                $devLink = $link;
            }
        }

        // Pesan sama untuk email terdaftar/tidak (tidak membocorkan data akun)
        return back()
            ->with('success', 'Jika email terdaftar, link reset password telah dikirim. Link berlaku ' . self::VALID_HOURS . ' jam.')
            ->with('dev_reset_link', $devLink);
    }

    public function showReset(string $token)
    {
        $reset = $this->findValidReset($token);

        if (!$reset) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link reset password tidak valid atau sudah kedaluwarsa. Silakan minta link baru.']);
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request, string $token)
    {
        $reset = $this->findValidReset($token);
        if (!$reset) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link reset password tidak valid atau sudah kedaluwarsa.']);
        }

        $request->validate(['password' => ['required', 'string', 'min:6', 'max:255', 'confirmed']]);

        $user = User::findOrFail($reset->user_id);
        $user->password = $request->input('password');
        $user->setRememberToken(Str::random(60));
        $user->save();

        DB::table('password_resets')->where('user_id', $user->id)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }

    private function findValidReset(string $token): ?object
    {
        return DB::table('password_resets')
            ->where('token', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->first();
    }
}
