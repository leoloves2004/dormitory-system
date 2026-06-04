<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with('tenant.student.user', 'tenant.room')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, fn ($q, $search) => $q->where('reference_number', 'like', "%{$search}%"))
            ->latest('payment_date')->paginate(10)->withQueryString();

        return view('admin.payments.index', ['payments' => $payments, 'tenants' => Tenant::with('student.user', 'room')->get()]);
    }

    public function store(PaymentRequest $request): RedirectResponse
    {
        Payment::create($request->validated());

        return back()->with('status', 'Payment recorded.');
    }

    public function update(PaymentRequest $request, Payment $payment): RedirectResponse
    {
        $payment->update($request->validated());

        return back()->with('status', 'Payment updated.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return back()->with('status', 'Payment deleted.');
    }

}
