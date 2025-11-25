<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index()
    {
        if (Session::has('user')) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Ambil user berdasarkan username, tapi case-insensitive dulu
        $user = DB::table('users')
            ->whereRaw('BINARY username = ?', [$request->username])   // ⬅ Tambahan penting!
            ->first();

        // Jika username cocok secara case-sensitive & password valid
        if ($user && Hash::check($request->password, $user->password)) {
            Session::put('user', $user);
            return redirect('/dashboard');
        }

        return back()->with('error', 'Username atau password salah.');
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/')->with('success', 'Kamu sudah logout.');
    }
}
