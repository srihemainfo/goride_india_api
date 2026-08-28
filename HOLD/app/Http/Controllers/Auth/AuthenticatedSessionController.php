<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Arr;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            Session::forget('login_attempts');
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $attempts = Session::get('login_attempts', 0);
        $attempts++;
        dd($attempts);
        Session::put('login_attempts', $attempts);

        if ($attempts >= 2) {
            $request->session()->flash('show_signup_link', true);
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }



    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    public function mycheck(Request $request){
        if (Arr::has($_COOKIE, 'd_token')) {
            return 1;
         } else {
             return 2;
         }
    }
    
}
