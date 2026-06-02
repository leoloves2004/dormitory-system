<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index() { return Tenant::with('student.user', 'room')->paginate(20); }
    public function store(Request $request) { return Tenant::create($request->validate(['student_id' => ['nullable', 'exists:students,id'], 'room_id' => ['required', 'exists:rooms,id'], 'move_in_date' => ['required', 'date'], 'move_out_date' => ['nullable', 'date'], 'status' => ['required'], 'remarks' => ['nullable']])); }
    public function show(Tenant $tenant) { return $tenant->load('student.user', 'room'); }
    public function update(Request $request, Tenant $tenant) { $tenant->update($request->all()); return $tenant; }
    public function destroy(Tenant $tenant) { $tenant->delete(); return response()->noContent(); }
}
