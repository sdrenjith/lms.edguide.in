<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Day;
use App\Models\Question;

class DashboardController extends Controller
{
    public function index()
    {
        $coursesCount = Course::count();
        $daysCount = Day::count();
        $questionsCount = Question::count();
        return view('admin.dashboard', compact('coursesCount', 'daysCount', 'questionsCount'));
    }
}
