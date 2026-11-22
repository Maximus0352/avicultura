<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $identifiants = $request->only('email', 'password');
        $identifiants['actif'] = true;

        if (! Auth::attempt($identifiants)) {
            Log::warning('Echec de connexion', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Identifiants incorrects ou compte désactivé.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
