<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Required fields
            'name' => ['required', 'string', 'max:255'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'phone' => ['required', 'string', 'max:20'],
            'qualification' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'address' => ['required', 'string', 'max:1000'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'nationality' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Optional fields
            'batch_id' => ['nullable', 'exists:batches,id'],
            'course_fee' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
        ]);

        // Calculate age from date of birth
        $age = \Carbon\Carbon::parse($request->dob)->age;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student', // Set default role as student
            'guardian_name' => $request->guardian_name,
            'dob' => $request->dob,
            'age' => $age,
            'phone' => $request->phone,
            'qualification' => $request->qualification,
            'batch_id' => $request->batch_id,
            'address' => $request->address,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'course_fee' => $request->course_fee,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('filament.student.pages.profile'));
    }
}
