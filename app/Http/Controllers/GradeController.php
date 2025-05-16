<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:teacher,super-admin');
    }

    public function index()
    {
        $grades = Grade::with(['student.user', 'course'])->where('is_deleted', false)->get();
        return response()->json($grades);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'grade' => 'required|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $grade = Grade::create($request->all());

        return response()->json([
            'message' => 'Not başarıyla oluşturuldu',
            'grade' => $grade->load(['student.user', 'course'])
        ], 201);
    }

    public function show($id)
    {
        $grade = Grade::with(['student.user', 'course'])->findOrFail($id);
        return response()->json($grade);
    }

    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'grade' => 'required|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $grade->update($request->all());

        return response()->json([
            'message' => 'Not başarıyla güncellendi',
            'grade' => $grade->load(['student.user', 'course'])
        ]);
    }

    public function destroy($id)
    {
        $grade = Grade::findOrFail($id);
        $grade->update(['is_deleted' => true]);

        return response()->json([
            'message' => 'Not başarıyla silindi'
        ]);
    }
}
