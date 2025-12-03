<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DayController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\StudentRegistrationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TranslationController;

Route::get('/', function () {
    if (Auth::check()) {
        // Check if user is a student
        if (strtolower(auth()->user()->role) === 'student') {
            return redirect()->route('filament.student.pages.profile');
        }
        return redirect()->route('filament.admin.pages.dashboard');
    }
    return redirect()->route('login');
});

// Student Registration Routes
Route::get('/student/register', [StudentRegistrationController::class, 'showRegistrationForm'])->name('student.register.form');
Route::post('/student/register', [StudentRegistrationController::class, 'register'])->name('student.register');
Route::get('/student/verification', [StudentRegistrationController::class, 'showVerificationForm'])->name('student.verification');
Route::post('/student/verify', [StudentRegistrationController::class, 'verifyAccount'])->name('student.verify');

// User profile routes (optional, keep if you want user-side features)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/student/courses', [\App\Http\Controllers\StudentCourseController::class, 'index'])->name('filament.student.pages.courses');
    
    // Redirect student panel root to profile page
    Route::get('/student', function () {
        return redirect()->route('filament.student.pages.profile');
    });
    
    // Redirect dashboard to profile page
    Route::get('/student/dashboard', function () {
        return redirect()->route('filament.student.pages.profile');
    })->name('filament.student.pages.dashboard');
    
    // Profile page route - handled by Filament
    // Route::get('/student/profile', [\App\Http\Controllers\StudentPageController::class, 'profile'])->name('filament.student.pages.profile');
    
    // Study materials page route
    Route::get('/student/study-materials', [\App\Http\Controllers\StudentPageController::class, 'studyMaterials'])->name('filament.student.pages.study-materials');
    
    // Speaking sessions page route
    Route::get('/student/speaking-sessions', [\App\Http\Controllers\StudentPageController::class, 'speakingSessions'])->name('filament.student.pages.speaking-sessions');

    Route::get('/student/questions', [\App\Http\Controllers\StudentCourseController::class, 'questions'])->name('filament.student.pages.questions');
    Route::get('/student/questions/{id}/answer', [\App\Http\Controllers\StudentQuestionController::class, 'answer'])->name('student.questions.answer');
    
    // Admin Student Progress Route
    Route::get('/admin/students/{student}/progress', function ($student) {
        $studentRecord = \App\Models\User::findOrFail($student);
        return view('admin.student-progress', ['record' => $studentRecord]);
    })->middleware(['auth'])->name('admin.students.progress');
    Route::post('/student/clear-result-modal', [\App\Http\Controllers\StudentQuestionController::class, 'clearResultModal'])->name('filament.student.clear_result_modal');
    
    // Explicitly define submit answer route with full path
    Route::post('/student/questions/{id}/submit-answer', function(Illuminate\Http\Request $request, $id) {
        \Log::info('Submit Answer Route Debug', [
            'route_name' => $request->route()->getName(),
            'route_parameters' => $request->route()->parameters(),
            'all_input' => $request->all(),
            'id' => $id
        ]);
        
        $controller = new \App\Http\Controllers\StudentQuestionController();
        return $controller->submitAnswer($request, $id);
    })->name('filament.student.submit_answer')
      ->where('id', '[0-9]+');

    // Remove any redundant routes for submitting answers
    // Route::post('/student/questions/{question}/answer', [\App\Http\Controllers\StudentQuestionController::class, 'submitAnswer'])->name('student.questions.answer.submit');
    // Route::post('/student/questions/{question}/submit', [\App\Http\Controllers\StudentQuestionController::class, 'submitAnswer'])->name('filament.student.pages.questions.submit');
    Route::get('/student/doubt-clearance', function () {
        // Track activity
        if (Auth::check() && Auth::user()->role === 'student') {
            $user = Auth::user();
            $activeActivity = \App\Models\StudentActivity::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest()
                ->first();
            
            if (!$activeActivity) {
                \App\Models\StudentActivity::create([
                    'user_id' => $user->id,
                    'login_at' => now(),
                    'last_activity_at' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } else {
                $activeActivity->updateActivity();
            }
        }
        
        $doubts = \App\Models\Doubt::where('user_id', auth()->id())->orderBy('created_at')->get();
        return view('filament.student.pages.doubt-clearance', compact('doubts'));
    })->name('filament.student.pages.doubt-clearance');
    Route::post('/student/doubt-clearance', function (\Illuminate\Http\Request $request) {
        $request->validate(['message' => 'required|string|max:1000']);
        \App\Models\Doubt::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);
        return redirect()->back()->with('success', 'Your doubt has been submitted!');
    })->name('student.doubt.submit');
    
    // Student activity tracking routes
    Route::post('/student/logout', [\App\Http\Controllers\StudentActivityController::class, 'logout'])->name('student.logout');
    Route::post('/student/auto-logout', [\App\Http\Controllers\StudentActivityController::class, 'autoLogout'])->name('student.auto-logout');
    Route::post('/student/update-activity', [\App\Http\Controllers\StudentActivityController::class, 'updateActivity'])->name('student.update-activity');
    
    // Secure file preview routes
    Route::get('/secure-preview/{type}/{id}', [\App\Http\Controllers\SecureFilePreviewController::class, 'previewFile'])->name('secure-file-preview');
    Route::get('/secure-modal/{type}/{id}', [\App\Http\Controllers\SecureFilePreviewController::class, 'previewModal'])->name('secure-file-modal');

    // Explicitly define test routes with full path
    Route::prefix('student/tests')->group(function () {
        Route::get('/', [\App\Http\Controllers\StudentTestController::class, 'index'])
            ->name('filament.student.pages.tests');
        
        Route::get('/{test}', [\App\Http\Controllers\StudentTestController::class, 'show'])
            ->name('filament.student.pages.tests.show');
        
        // Fallback route for tests.detail
        Route::get('/{test}/detail', function($test) {
            return redirect()->route('filament.student.pages.tests.show', $test);
        })->name('filament.student.pages.tests.detail');
        
        Route::get('/{test}/question/{question}', [\App\Http\Controllers\StudentTestController::class, 'question'])
            ->name('filament.student.pages.tests.question');
        
        // Specific route for test question submissions with explicit method
        Route::post('/{test}/question/{question}/submit', [\App\Http\Controllers\StudentTestController::class, 'submitAnswer'])
            ->name('filament.student.pages.tests.question.submit');
        
        Route::get('/test-questions', [\App\Http\Controllers\StudentTestController::class, 'testQuestions'])
            ->name('filament.student.pages.test-questions');
    });

    // Translation Routes
    Route::post('/translate', [TranslationController::class, 'translate'])->name('translate');
    Route::post('/translate-page', [TranslationController::class, 'translatePage'])->name('translate.page');
});

Route::post('/admin/set-locale', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale', 'en');
    session(['locale' => $locale]);
    app()->setLocale($locale);
    return back();
})->name('admin.set-locale');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Filament handles the dashboard and resource pages
    // Remove custom dashboard route
    // Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // Route::resource('courses', CourseController::class);
    // Route::resource('days', DayController::class);
    // Route::resource('questions', QuestionController::class);
    // Instead, redirect /admin to Filament's dashboard
    Route::redirect('/', '/admin/dashboard');
});

require __DIR__.'/auth.php';
