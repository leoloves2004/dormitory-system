<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function store(Request $request): RedirectResponse
    {
        Room::create($this->validated($request));

        return back()->with('status', 'Room saved.');
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $room->update($this->validated($request, $room));

        return back()->with('status', 'Room updated.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return back()->with('status', 'Room deleted.');
    }

    private function validated(Request $request, ?Room $room = null): array
    {
        return $request->validate([
            'room_number' => ['required', 'string', 'max:50', 'unique:rooms,room_number,'.($room?->id ?? 'NULL')],
            'building' => ['nullable', 'string', 'max:100'],
            'floor' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1'],
            'monthly_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,occupied,maintenance'],
            'amenities' => ['nullable', 'string'],
        ]);
    }
}
