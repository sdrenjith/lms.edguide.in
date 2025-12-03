<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentActivity;

class StudentActivityController extends Controller
{
    /**
     * Handle logout and update activity record
     */
    public function logout(Request $request)
    {
        if (Auth::check() && Auth::user()->role === 'student') {
            $user = Auth::user();
            
            // Find the current active session
            $activeActivity = StudentActivity::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first();
            
            if ($activeActivity) {
                // Update the activity record with logout information
                $activeActivity->update([
                    'logout_at' => now(),
                    'logout_type' => 'manual', // or 'auto' for automatic logout
                ]);
                
                // Calculate session duration
                $activeActivity->calculateSessionDuration();
            }
        }
        
        // Perform the actual logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }

    /**
     * Handle automatic logout due to inactivity
     */
    public function autoLogout(Request $request)
    {
        if (Auth::check() && Auth::user()->role === 'student') {
            $user = Auth::user();
            
            // Find the current active session
            $activeActivity = StudentActivity::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first();
            
            if ($activeActivity) {
                // Update the activity record with auto logout information
                $activeActivity->update([
                    'logout_at' => now(),
                    'logout_type' => 'auto',
                ]);
                
                // Calculate session duration
                $activeActivity->calculateSessionDuration();
            }
        }
        
        // Perform the actual logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json(['message' => 'Logged out due to inactivity'], 200);
    }

    /**
     * Update last activity timestamp
     */
    public function updateActivity(Request $request)
    {
        if (Auth::check() && Auth::user()->role === 'student') {
            $user = Auth::user();
            
            // Find the current active session
            $activeActivity = StudentActivity::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first();
            
            if ($activeActivity) {
                $activeActivity->updateActivity();
                return response()->json(['success' => true]);
            }
        }
        
        return response()->json(['success' => false], 400);
    }
}