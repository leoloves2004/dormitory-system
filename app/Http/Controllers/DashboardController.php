<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomApplication;
use App\Models\Student;
use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        $stats = [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'total_students' => Student::count(),
            'total_tenants' => Tenant::where('status', 'active')->count(),
            'total_payments' => Payment::sum('amount'),
            'pending_applications' => RoomApplication::where('status', 'pending')->count(),
            'approved_applications' => RoomApplication::where('status', 'approved')->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentApplications' => RoomApplication::with('student.user', 'preferredRoom')->latest()->take(6)->get(),
            'monthlyPayments' => Payment::orderBy('payment_date')->get()
                ->groupBy(fn (Payment $payment) => $payment->payment_date?->format('Y-m') ?? 'undated')
                ->map(fn ($payments) => $payments->sum('amount'))->take(8),
        ]);
    }
}
