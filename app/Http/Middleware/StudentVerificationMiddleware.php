<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Only apply to students
        if ($user && $user->role === 'student') {
            // Check if user is not verified
            if (!$user->is_verified) {
                // If it's an AJAX request for verification, allow it
                if ($request->is('student/verify') || $request->is('student/logout')) {
                    return $next($request);
                }
                
                // For all other requests, redirect to verification page
                return redirect()->route('student.verification');
            }
            
            // Check if verification code is expired (for verified students)
            if ($user->is_verified) {
                // Reload the verification code relationship to ensure we have the latest data
                $user->load('verificationCode');
                
                // Check if user has a verification code and if it's expired
                if ($user->verificationCode && $user->verificationCode->isExpired()) {
                    // Allow logout and verification routes
                    $routeName = $request->route() ? $request->route()->getName() : null;
                    $path = $request->path();
                    $url = $request->url();
                    
                    // Check if this is an allowed route (logout, verification)
                    $isAllowed = in_array($routeName, ['student.verification', 'student.verify', 'logout']) || 
                                 $request->is('student/verification') || 
                                 $request->is('student/verify') || 
                                 $request->is('logout') ||
                                 str_contains($path, 'logout') ||
                                 str_contains($url, 'logout') ||
                                 str_contains($url, 'verification');
                    
                    if (!$isAllowed) {
                        // Block access to all other student pages when verification code is expired
                        return redirect()->route('student.verification')
                            ->with('error', 'Your verification code has expired. Please contact your administrator for a new verification code.');
                    }
                }
            }
        }
        
        return $next($request);
    }
}
