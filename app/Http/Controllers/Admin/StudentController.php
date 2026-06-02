<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = Student::with('user', 'room')
            ->when($request->search, function ($q, $search) {
                $q->where('student_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($request->room_id, fn ($q, $room) => $q->where('room_id', $room))
            ->latest()->paginate(10)->withQueryString();

        return view('admin.students.index', ['students' => $students, 'rooms' => Room::orderBy('room_number')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? 'password'),
            'role' => 'student',
        ]);
        Student::create($data + ['user_id' => $user->id]);

        return back()->with('status', 'Student created.');
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $this->validated($request, $student);
        $userData = ['name' => $data['name'], 'email' => $data['email']];
        if (! empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $student->user->update($userData);
        $student->update($data);

        return back()->with('status', 'Student updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->user()->delete();

        return back()->with('status', 'Student deleted.');
    }

    private function validated(Request $request, ?Student $student = null): array
    {
        $userId = $student?->user_id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$userId],
            'password' => [$student ? 'nullable' : 'required', 'string', 'min:8'],
            'student_number' => ['required', 'string', 'max:50', 'unique:students,student_number,'.($student?->id ?? 'NULL')],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'course' => ['nullable', 'string', 'max:100'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
