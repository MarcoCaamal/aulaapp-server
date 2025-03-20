<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index() {
        return View('auth.login');
    }

    public function authenticate(Request $request) {

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required']
        ]);

        $remember_me = filled($request->remember);

        if(Auth::attempt($credentials, $remember_me)) {
            $request->session()->regenerate();

            return redirect()->intended('/home');
        }

        return back()->with([
            'error' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request) {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login.index');
    }
}
