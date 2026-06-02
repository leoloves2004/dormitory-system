<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $tenants = Tenant::with('student.user', 'room')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()->paginate(10)->withQueryString();

        return view('admin.tenants.index', [
            'tenants' => $tenants,
            'students' => Student::with('user')->orderBy('student_number')->get(),
            'rooms' => Room::orderBy('room_number')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Tenant::create($data);
        Student::find($data['student_id'])?->update(['room_id' => $data['room_id']]);

        return back()->with('status', 'Tenant saved.');
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $previousStudentId = $tenant->student_id;
        $data = $this->validated($request);
        $tenant->update($data);
        if ($previousStudentId && (int) $previousStudentId !== (int) $data['student_id']) {
            Student::whereKey($previousStudentId)->update(['room_id' => null]);
        }
        Student::find($data['student_id'])?->update(['room_id' => $data['room_id']]);

        return back()->with('status', 'Tenant updated.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        if ($tenant->student_id) {
            Student::whereKey($tenant->student_id)->update(['room_id' => null]);
        }
        $tenant->delete();

        return back()->with('status', 'Tenant deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'move_in_date' => ['required', 'date'],
            'move_out_date' => ['nullable', 'date', 'after_or_equal:move_in_date'],
            'status' => ['required', 'in:active,moved_out,suspended'],
            'remarks' => ['nullable', 'string'],
        ]);
    }
}
