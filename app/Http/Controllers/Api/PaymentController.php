<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index() { return Payment::with('tenant.student.user', 'tenant.room')->paginate(20); }
    public function store(PaymentRequest $request) { return Payment::create($request->validated()); }
    public function show(Payment $payment) { return $payment->load('tenant.student.user', 'tenant.room'); }
    public function update(PaymentRequest $request, Payment $payment) { $payment->update($request->validated()); return $payment; }
    public function destroy(Payment $payment) { $payment->delete(); return response()->noContent(); }
}
