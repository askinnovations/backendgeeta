<?php


namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function login(Request $request)
   {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // Custom credentials check
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->route('user.dashboard'); // 👈 Correct route name
        }

        return back()->withErrors([
            'mobile_number' => 'Invalid credentials.',
        ]);
   }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }
}
