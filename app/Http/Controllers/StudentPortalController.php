<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\RoomApplicationRequest;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\RoomApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        return $this->housing($request);
    }

    public function housing(Request $request): View
    {
        return view('student.dashboard', $this->portalData($request) + [
            'section' => 'housing',
            'title' => 'Student Portal',
        ]);
    }

    public function maintenance(Request $request): View
    {
        return view('student.dashboard', $this->portalData($request) + [
            'section' => 'maintenance',
            'title' => 'Student Portal',
        ]);
    }

    private function portalData(Request $request): array
    {
        $student = $request->user()->student()->with('room', 'payments', 'roomApplications.preferredRoom')->firstOrFail();

        return [
            'student' => $student,
            'rooms' => Room::where('status', 'available')->orderBy('room_number')->get(),
            'maintenanceRequests' => MaintenanceRequest::where('user_id', $request->user()->id)
                ->latest()
                ->get(),
        ];
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
