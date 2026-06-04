<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\RoomApplicationRequest;
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

    public function apply(RoomApplicationRequest $request): RedirectResponse
    {
        $student = $request->user()->student;
        $data = $request->validated();
        $data['application_date'] ??= now()->toDateString();

        RoomApplication::create($data + [
            'student_id' => $student->id,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Room application submitted.');
    }

    public function profile(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $request->user()->student->update($data);

        return back()->with('status', 'Profile updated.');
    }
}
