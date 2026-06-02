<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with('student.user')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, fn ($q, $search) => $q->where('reference_number', 'like', "%{$search}%"))
            ->latest('payment_date')->paginate(10)->withQueryString();

        return view('admin.payments.index', ['payments' => $payments, 'students' => Student::with('user')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Payment::create($this->validated($request));

        return back()->with('status', 'Payment recorded.');
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $payment->update($this->validated($request));

        return back()->with('status', 'Payment updated.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return back()->with('status', 'Payment deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'method' => ['required', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:paid,pending,overdue,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
