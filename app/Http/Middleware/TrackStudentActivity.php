<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentActivity;
use Symfony\Component\HttpFoundation\Response;

class TrackStudentActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track for authenticated students
        if (Auth::check() && Auth::user()->role === 'student') {
            $user = Auth::user();
            
            // Check if user has an active session
            $activeActivity = StudentActivity::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first();
            
            if (!$activeActivity) {
                // Create new activity record for login
                StudentActivity::create([
                    'user_id' => $user->id,
                    'login_at' => now(),
                    'last_activity_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } else {
                // Update last activity timestamp
                $activeActivity->updateActivity();
            }
        }

        return $next($request);
    }
}