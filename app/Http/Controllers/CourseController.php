<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:teacher,super-admin');
    }

    public function index()
    {
        $courses = Course::with('teacher.user')->where('is_deleted', false)->get();
        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:teachers,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course = Course::create($request->all());

        return response()->json([
            'message' => 'Ders başarıyla oluşturuldu',
            'course' => $course->load('teacher.user')
        ], 201);
    }

    public function show($id)
    {
        $course = Course::with(['teacher.user', 'grades.student.user'])->findOrFail($id);
        return response()->json($course);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:teachers,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course->update($request->all());

        return response()->json([
            'message' => 'Ders başarıyla güncellendi',
            'course' => $course->load('teacher.user')
        ]);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->update(['is_deleted' => true]);

        return response()->json([
            'message' => 'Ders başarıyla silindi'
        ]);
    }
}
