<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $rooms = Room::withCount('students')
            ->when($request->search, fn ($q, $search) => $q->where('room_number', 'like', "%{$search}%")->orWhere('building', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy('room_number')->paginate(10)->withQueryString();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());

        return back()->with('status', 'Room saved.');
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return back()->with('status', 'Room updated.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return back()->with('status', 'Room deleted.');
    }

}
