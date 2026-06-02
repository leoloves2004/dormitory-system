<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomApplication;
use Illuminate\Http\Request;

class RoomApplicationController extends Controller
{
    public function index() { return RoomApplication::with('student.user', 'preferredRoom')->paginate(20); }
    public function store(Request $request) { return RoomApplication::create($request->validate(['student_id' => ['required', 'exists:students,id'], 'preferred_room_id' => ['nullable', 'exists:rooms,id'], 'status' => ['required'], 'preferred_move_in_date' => ['nullable', 'date'], 'reason' => ['nullable'], 'admin_notes' => ['nullable']])); }
    public function show(RoomApplication $roomApplication) { return $roomApplication->load('student.user', 'preferredRoom'); }
    public function update(Request $request, RoomApplication $roomApplication) { $roomApplication->update($request->all()); return $roomApplication; }
    public function destroy(RoomApplication $roomApplication) { $roomApplication->delete(); return response()->noContent(); }
}
