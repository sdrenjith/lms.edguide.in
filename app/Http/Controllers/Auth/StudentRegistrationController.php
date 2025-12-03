<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class StudentRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.student-register');
    }

    public function showVerificationForm()
    {
        return view('auth.student-verification');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'dob' => ['required', 'date', 'before:today'],
            'nationality' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the user without verification code initially
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'nationality' => $request->nationality,
            'address' => $request->address,
            'guardian_name' => $request->guardian_name,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'role' => 'student',
            'is_verified' => false, // Will be verified after code activation
        ]);

        // Log the user in
        Auth::login($user);

        return redirect()->route('filament.student.pages.profile')
            ->with('success', 'Registration successful! Please enter your verification code to access your assigned courses.');
    }

    public function verifyAccount(Request $request)
    {
        $request->validate([
            'verification_code' => ['required', 'string'],
        ]);

        $user = Auth::user();
        
        if ($user->is_verified) {
            return redirect()->route('filament.student.pages.profile')
                ->with('info', 'Your account is already verified.');
        }

        // Check if verification code is valid and not used
        $verificationCode = VerificationCode::where('code', $request->verification_code)
            ->where('is_active', true)
            ->where('is_used', false)
            ->first();

        if (!$verificationCode) {
            return redirect()->back()
                ->withErrors(['verification_code' => 'Invalid or already used verification code.']);
        }

        if ($verificationCode->isExpired()) {
            return redirect()->back()
                ->withErrors(['verification_code' => 'This verification code has expired.']);
        }

        // Update the user with verification code details
        $user->update([
            'verification_code_id' => $verificationCode->id,
            'verification_code' => $request->verification_code,
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // Mark verification code as used
        $verificationCode->markAsUsed($user);

        return redirect()->route('filament.student.pages.profile')
            ->with('success', 'Account verified successfully! You now have access to your assigned courses and study materials.');
    }
}
