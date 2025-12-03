<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            Log::error('LoginResponse error: User not found after authentication.');
            return redirect('/');
        }
        
        Log::info('LoginResponse triggered for user: ' . $user->email . ' with role: ' . $user->role);

        if ($user->role === 'admin') {
            Log::info('Redirecting admin user to: /admin');
            return redirect()->intended('/admin');
        }

        if ($user->role === 'student') {
            Log::info('Redirecting student user to: /student');
            return redirect()->intended('/student');
        }

        Log::warning('User with role ' . $user->role . ' has no panel access. Redirecting to fallback.');
        return redirect()->intended('/');
    }
} 