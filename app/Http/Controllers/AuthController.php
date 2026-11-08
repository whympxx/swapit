<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = strtolower($credentials['email']);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah!'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', 'Selamat datang kembali, ' . Auth::user()->full_name . '!');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->merge(['email' => strtolower((string) $request->input('email'))]);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'regex:/^[A-Za-z0-9_.]{3,30}$/', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['nullable', 'regex:/^\+?[0-9\s\-]{8,20}$/'],
            'university' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'username.regex' => 'Username 3-30 karakter, hanya huruf, angka, titik, atau underscore.',
            'phone.regex' => 'Nomor HP tidak valid.',
        ]);

        User::create($data);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}
