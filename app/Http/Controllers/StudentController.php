<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:teacher,super-admin');
    }

    public function index()
    {
        $students = Student::with('user')->where('is_deleted', false)->get();
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'class' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('slug', 'student')->first()->id
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'class' => $request->class
        ]);

        return response()->json([
            'message' => 'Öğrenci başarıyla oluşturuldu',
            'student' => $student->load('user')
        ], 201);
    }

    public function show($id)
    {
        $student = Student::with(['user', 'grades.course'])->findOrFail($id);
        return response()->json($student);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->user_id,
            'class' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $student->user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        $student->update([
            'class' => $request->class
        ]);

        return response()->json([
            'message' => 'Öğrenci başarıyla güncellendi',
            'student' => $student->load('user')
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->update(['is_deleted' => true]);
        $student->user->update(['is_deleted' => true]);

        return response()->json([
            'message' => 'Öğrenci başarıyla silindi'
        ]);
    }
}
