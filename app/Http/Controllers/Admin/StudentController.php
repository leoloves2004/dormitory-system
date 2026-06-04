<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
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

    public function store(StudentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? 'password'),
            'role' => 'student',
        ]);
        Student::create($data + ['user_id' => $user->id]);

        return back()->with('status', 'Student created.');
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        $data = $request->validated();
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

}
