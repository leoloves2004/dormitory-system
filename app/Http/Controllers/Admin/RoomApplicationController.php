<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomApplication;
use App\Models\Tenant;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoomApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applications = RoomApplication::with('student.user', 'preferredRoom')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()->paginate(10)->withQueryString();

        return view('admin.applications.index', ['applications' => $applications, 'rooms' => Room::orderBy('room_number')->get()]);
    }

    public function update(Request $request, RoomApplication $roomApplication): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'preferred_room_id' => ['nullable', 'exists:rooms,id'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $roomApplication->update($data + ['approved_by' => Auth::id(), 'reviewed_at' => now()]);

        if ($data['status'] === 'approved' && $data['preferred_room_id']) {
            $roomApplication->student->update(['room_id' => $data['preferred_room_id']]);
            Tenant::updateOrCreate(
                ['student_id' => $roomApplication->student_id, 'status' => 'active'],
                ['room_id' => $data['preferred_room_id'], 'move_in_date' => now()->toDateString()]
            );
        }

        $roomApplication->student->user->notify(new ApplicationStatusNotification($roomApplication->fresh('preferredRoom')));

        return back()->with('status', 'Application reviewed.');
    }

    public function destroy(RoomApplication $roomApplication): RedirectResponse
    {
        $roomApplication->delete();

        return back()->with('status', 'Application deleted.');
    }
}
