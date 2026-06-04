<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceRequestController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::latest()->get();

        return view(
            'maintenance.index',
            compact('requests')
        );
    }

    public function store(Request $request)
    {
        MaintenanceRequest::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
          'user_id' => Auth::user()->id,
        ]);

        return back()->with(
            'success',
            'Request submitted.'
        );
    }
}