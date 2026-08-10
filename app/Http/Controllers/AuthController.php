<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Session::put('user', [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'login_time' => now()->toDateTimeString(),
            ]);
            return redirect()->route('movies');
        }

        return redirect()->back()->with('error', trans('auth.invalid_credentials'));
    }

    public function logout()
    {
        Session::forget('user');
        return redirect()->route('login');
    }
}
