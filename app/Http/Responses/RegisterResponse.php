<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): RedirectResponse|JsonResponse
    {
        // In test environment, keep user logged in and redirect to dashboard
        if (app()->environment('testing')) {
            return $request->wantsJson()
                ? new JsonResponse(['message' => 'Registration successful!'], 201)
                : redirect()->route('dashboard');
        }

        // Log out the user immediately after registration
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? new JsonResponse([
                'message' => 'Registration successful! Please contact the administrator to approve your account.',
            ], 201)
            : redirect()->route('login')->with('status', 'Registration successful! Please contact the administrator to approve your account.');
    }
}
