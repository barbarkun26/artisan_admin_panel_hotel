<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show login view.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Authenticate session.
     *
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            ActivityLog::log(
                $user->id,
                'Authentication',
                'Login',
                "User {$user->name} logged in successfully."
            );

            return $this->redirectBasedOnRole($user);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Terminate session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::log(
                $user->id,
                'Authentication',
                'Logout',
                "User {$user->name} logged out."
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Helper to redirect users to their respective workspace.
     */
    protected function redirectBasedOnRole($user): RedirectResponse
    {
        if ($user->hasRole('Administrator')) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->hasRole('Front Office')) {
            return redirect()->intended('/fo/dashboard');
        } elseif ($user->hasRole('Housekeeping')) {
            return redirect()->intended('/hk/dashboard');
        } elseif ($user->hasRole('Food & Beverage')) {
            return redirect()->intended('/fnb/dashboard');
        }

        return redirect('/login')->with('error', 'Unauthorized role.');
    }
}
