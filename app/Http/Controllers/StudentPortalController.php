<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $student = $request->user()->student()->with('room', 'payments', 'roomApplications.preferredRoom')->firstOrFail();

        return view('student.dashboard', [
            'student' => $student,
            'rooms' => Room::where('status', 'available')->orderBy('room_number')->get(),
        ]);
    }

    public function apply(Request $request): RedirectResponse
    {
        $student = $request->user()->student;
        $data = $request->validate([
            'preferred_room_id' => ['nullable', 'exists:rooms,id'],
            'preferred_move_in_date' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        RoomApplication::create($data + ['student_id' => $student->id, 'status' => 'pending']);

        return back()->with('status', 'Room application submitted.');
    }

    public function profile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course' => ['nullable', 'string', 'max:100'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'medical_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->student->update($data);

        return back()->with('status', 'Profile updated.');
    }
}
