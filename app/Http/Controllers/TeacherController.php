<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:super-admin');
    }

    public function index()
    {
        $teachers = Teacher::with('user')->where('is_deleted', false)->get();
        return response()->json($teachers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('slug', 'teacher')->first()->id
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id
        ]);

        return response()->json([
            'message' => 'Öğretmen başarıyla oluşturuldu',
            'teacher' => $teacher->load('user')
        ], 201);
    }

    public function show($id)
    {
        $teacher = Teacher::with(['user', 'courses'])->findOrFail($id);
        return response()->json($teacher);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $teacher->user_id
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $teacher->user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return response()->json([
            'message' => 'Öğretmen başarıyla güncellendi',
            'teacher' => $teacher->load('user')
        ]);
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update(['is_deleted' => true]);
        $teacher->user->update(['is_deleted' => true]);

        return response()->json([
            'message' => 'Öğretmen başarıyla silindi'
        ]);
    }
}
