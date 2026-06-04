<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiStudentRequest;
use App\Models\Student;

class StudentController extends Controller
{
    public function index() { return Student::with('user', 'room')->paginate(20); }
    public function store(ApiStudentRequest $request) { return Student::create($request->validated()); }
    public function show(Student $student) { return $student->load('user', 'room', 'payments', 'roomApplications'); }
    public function update(ApiStudentRequest $request, Student $student) { $student->update($request->validated()); return $student; }
    public function destroy(Student $student) { $student->delete(); return response()->noContent(); }
}
