<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationReviewRequest;
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

    public function update(ApplicationReviewRequest $request, RoomApplication $roomApplication): RedirectResponse
    {
        $data = $request->validated();

        $roomApplication->update($data + ['approved_by' => Auth::id(), 'reviewed_at' => now()]);

        if ($data['status'] === 'approved' && $data['room_id']) {
            $roomApplication->student->update(['room_id' => $data['room_id']]);
            Tenant::updateOrCreate(
                ['student_id' => $roomApplication->student_id, 'status' => 'active'],
                ['room_id' => $data['room_id'], 'check_in_date' => now()->toDateString()]
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
