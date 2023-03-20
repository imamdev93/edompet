<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showFormLogin()
    {
        if (auth()->user()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function doLogin(LoginRequest $request)
    {
        try {
            if (! Auth::attempt($request->only('email', 'password'))) {
                return redirect()->route('admin.login')->with('error', 'Username dan password salah');
            }

            return redirect()->route('admin.dashboard')->with('success', 'Wilujeng Sumping auth()->user()->name '.auth()->user()->name);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('admin.login')->with('success', 'Logout berhasil');
    }
}
