<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function index()
    {
        $user = User::all();

        return view('auth.signin', compact('user'));
    }

    public function store(Request $request)
    {
        try {
            $now = Carbon::now();
            $tahun_bulan = $now->year . str_pad($now->month, 2, '0', STR_PAD_LEFT); // Ensure the month is always two digits
    
            // Validate the request first
            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string',
                'level' => 'required|string',
            ]);
    
            // Determine the code prefix based on the level
            $prefix = $request->level === 'admin' ? 'A' : 'K';
    
            // Get the count of users with the same level
            $cek = User::where('kode', 'LIKE', $prefix . '%')->count();
    
            if ($cek == 0) {
                $urut = 100001;
                $kode = $prefix . '-' . $tahun_bulan . $urut;
            } else {
                $ambil = User::where('kode', 'LIKE', $prefix . '%')->orderBy('kode', 'desc')->first();
                $urut = (int)substr($ambil->kode, -6) + 1;
                $kode = $prefix . '-' . $tahun_bulan . $urut;
            }
    
            // Create and save the new user
            $user = new User;
            $user->kode = $kode;
            $user->nama = $request->nama;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->level = $request->level;
            $user->save();
    
            return redirect('login')->with('sukses', 'Berhasil Daftar, Silahkan Login!');
        } catch (\Exception $e) {
            return redirect('daftar')->with('status', 'Tidak Berhasil Daftar. Pesan Kesalahan: ' . $e->getMessage());
        }
    }
    
    public function login()
    {
        return view('auth.login');
    }

    public function postlogin(Request $request): RedirectResponse
    {
        if(Auth::attempt($request->only('email', 'password'))){
            $user = Auth::user();

            if($user->level == 'admin'){
                return redirect('/admin/dashboard');
            }else{
                return redirect('/kasir/dashboard');
            }
        }
        else {
            return back()->with('gagal', 'Email atau Password salah!');
        }
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/login');
    }

    public function forgotPw()
    {
        return view('auth.forgotPassword');
    }
}
