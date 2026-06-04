<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRoomApplicationRequest;
use App\Models\RoomApplication;

class RoomApplicationController extends Controller
{
    public function index() { return RoomApplication::with('student.user', 'preferredRoom')->paginate(20); }
    public function store(ApiRoomApplicationRequest $request)
    {
        $data = $request->validated();
        $data['application_date'] ??= now()->toDateString();

        return RoomApplication::create($data);
    }
    public function show(RoomApplication $roomApplication) { return $roomApplication->load('student.user', 'preferredRoom'); }
    public function update(ApiRoomApplicationRequest $request, RoomApplication $roomApplication) { $roomApplication->update($request->validated()); return $roomApplication; }
    public function destroy(RoomApplication $roomApplication) { $roomApplication->delete(); return response()->noContent(); }
}
