<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index() { return Payment::with('student.user')->paginate(20); }
    public function store(Request $request) { return Payment::create($request->validate(['student_id' => ['required', 'exists:students,id'], 'amount' => ['required', 'numeric'], 'payment_date' => ['required', 'date'], 'due_date' => ['nullable', 'date'], 'method' => ['required'], 'reference_number' => ['nullable'], 'status' => ['required'], 'notes' => ['nullable']])); }
    public function show(Payment $payment) { return $payment->load('student.user'); }
    public function update(Request $request, Payment $payment) { $payment->update($request->all()); return $payment; }
    public function destroy(Payment $payment) { $payment->delete(); return response()->noContent(); }
}
