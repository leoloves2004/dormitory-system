<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index() { return Room::withCount('students')->paginate(20); }
    public function store(Request $request) { return Room::create($request->validate(['room_number' => ['required', 'unique:rooms'], 'building' => ['nullable'], 'floor' => ['required', 'integer'], 'capacity' => ['required', 'integer'], 'monthly_rate' => ['required', 'numeric'], 'status' => ['required'], 'amenities' => ['nullable']])); }
    public function show(Room $room) { return $room->load('students.user', 'tenants'); }
    public function update(Request $request, Room $room) { $room->update($request->all()); return $room; }
    public function destroy(Room $room) { $room->delete(); return response()->noContent(); }
}
