<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index() { return Student::with('user', 'room')->paginate(20); }
    public function store(Request $request) { return Student::create($request->validate(['user_id' => ['required', 'exists:users,id'], 'student_number' => ['required', 'unique:students'], 'room_id' => ['nullable', 'exists:rooms,id'], 'course' => ['nullable'], 'year_level' => ['nullable'], 'phone' => ['nullable']])); }
    public function show(Student $student) { return $student->load('user', 'room', 'payments', 'roomApplications'); }
    public function update(Request $request, Student $student) { $student->update($request->all()); return $student; }
    public function destroy(Student $student) { $student->delete(); return response()->noContent(); }
}
