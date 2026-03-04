<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Logging\ActionLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            // Log failed login attempt
            app(ActionLogService::class)->log(
                userId: null,
                action: 'login_failed',
                dataAfter: json_encode(['email' => $request->input('email')]),
                request: $request
            );
            throw $e;
        }

        $request->session()->regenerate();

        // Log successful login
        app(ActionLogService::class)->log(
            userId: Auth::id(),
            action: 'login_success',
            request: $request
        );

        return redirect()->intended(route('ideas.index'));
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
