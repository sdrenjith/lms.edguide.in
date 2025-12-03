<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $userRole = strtolower(auth()->user()->role);
            if ($userRole === 'admin' || $userRole === 'teacher' || $userRole === 'accounts' || $userRole === 'dataentry' || $userRole === 'manager') {
                return $next($request);
            }

            if ($userRole === 'student') {
                return redirect()->route('filament.student.pages.dashboard');
            }
            
            // User is authenticated but doesn't have admin role
            abort(403, 'Unauthorized');
        }

        // User is not authenticated, redirect to main login page
        return redirect()->route('login');
    }
}
