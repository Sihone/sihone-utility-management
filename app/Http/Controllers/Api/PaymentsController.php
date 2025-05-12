<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'amount' => 'required|integer|min:1',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create($validated);

        return response()->json($payment);
    }

    public function list($apartmentId)
    {
        $payments = Payment::where('apartment_id', $apartmentId)->get();
        return response()->json($payments);
    }
    
    public function index()
    {
        $payments = Payment::with('apartment')
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json($payments);
    }
    
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->noContent();
    }

}
