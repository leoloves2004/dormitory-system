<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceRequestController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::with('user')->latest()->get();

        return view(
            'maintenance.index',
            compact('requests')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'priority' => ['required', 'in:Low,Medium,High'],
        ]);

        MaintenanceRequest::create($data + [
            'user_id' => Auth::id(),
            'status' => 'Pending',
        ]);

        return back()->with(
            'status',
            'Maintenance request submitted.'
        );
    }
}
