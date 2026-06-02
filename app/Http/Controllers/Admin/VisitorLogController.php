<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Student;
use App\Models\VisitorLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitorLogController extends Controller
{
    public function index(): View
    {
        return view('admin.visitor-logs.index', [
            'visitorLogs' => VisitorLog::with('student.user', 'room')->latest('time_in')->paginate(10),
            'students' => Student::with('user')->get(),
            'rooms' => Room::orderBy('room_number')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        VisitorLog::create($this->validated($request));

        return back()->with('status', 'Visitor logged.');
    }

    public function update(Request $request, VisitorLog $visitorLog): RedirectResponse
    {
        $visitorLog->update($this->validated($request));

        return back()->with('status', 'Visitor log updated.');
    }

    public function destroy(VisitorLog $visitorLog): RedirectResponse
    {
        $visitorLog->delete();

        return back()->with('status', 'Visitor log deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'student_id' => ['nullable', 'exists:students,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_phone' => ['nullable', 'string', 'max:30'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'time_in' => ['required', 'date'],
            'time_out' => ['nullable', 'date', 'after_or_equal:time_in'],
        ]);
    }
}
