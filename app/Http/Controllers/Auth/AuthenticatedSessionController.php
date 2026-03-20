<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if ($request->user()?->is_blocked) {
            Auth::guard('web')->logout();

            return back()->withErrors([
                'email' => 'Your account is blocked. Please contact support.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($request->user()?->role === User::ROLE_ADMIN) {
            return redirect()->route('admin.dashboard');
        }

        if ($request->user()?->role === User::ROLE_STAFF) {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->intended(route('user.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
