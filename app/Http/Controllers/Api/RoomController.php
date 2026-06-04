<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Models\Room;

class RoomController extends Controller
{
    public function index() { return Room::withCount('students')->paginate(20); }
    public function store(RoomRequest $request) { return Room::create($request->validated()); }
    public function show(Room $room) { return $room->load('students.user', 'tenants'); }
    public function update(RoomRequest $request, Room $room) { $room->update($request->validated()); return $room; }
    public function destroy(Room $room) { $room->delete(); return response()->noContent(); }
}
