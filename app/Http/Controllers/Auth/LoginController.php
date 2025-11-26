<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            // Attempt login using Laravel's built-in authentication
            if (Auth::attempt($request->validated(), $request->boolean('remember'))) {
                // Regenerate session to prevent fixation attacks
                $request->session()->regenerate();

                // Redirect to the dashboard
                return redirect()->intended(route('dashboard'))->with('success', 'Login successful');
            }

            return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
        } catch (Exception $exception) {
            // Log the exception and show a generic error message
            logger()->error('Login error: ' . $exception->getMessage());

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
