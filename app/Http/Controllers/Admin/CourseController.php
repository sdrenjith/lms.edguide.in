<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Day;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with(['days' => function($q){ $q->orderBy('course_day.order'); }, 'days.questions'])->get();
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $days = Day::orderBy('day_number')->get();
        return view('admin.courses.create', compact('days'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'days' => 'array',
            'days.*' => 'exists:days,id',
            'order' => 'array',
        ]);
        $course = Course::create(['name' => $request->name]);
        if ($request->days) {
            $syncData = [];
            foreach ($request->days as $i => $dayId) {
                $syncData[$dayId] = ['order' => $request->order[$i] ?? $i];
            }
            $course->days()->sync($syncData);
        }
        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load(['days' => function($q){ $q->orderBy('course_day.order'); }, 'days.questions']);
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $days = Day::orderBy('day_number')->get();
        $selectedDays = $course->days()->pluck('day_id')->toArray();
        $orders = $course->days()->pluck('course_day.order', 'day_id')->toArray();
        return view('admin.courses.edit', compact('course', 'days', 'selectedDays', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'days' => 'array',
            'days.*' => 'exists:days,id',
            'order' => 'array',
        ]);
        $course->update(['name' => $request->name]);
        if ($request->days) {
            $syncData = [];
            foreach ($request->days as $i => $dayId) {
                $syncData[$dayId] = ['order' => $request->order[$i] ?? $i];
            }
            $course->days()->sync($syncData);
        } else {
            $course->days()->detach();
        }
        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully!');
    }
}
