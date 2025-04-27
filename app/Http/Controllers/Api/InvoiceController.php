<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::with('apartment')->orderBy('invoice_date', 'desc')->get();
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load('apartment', 'meterReading');
    }
    
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Paid',
        ]);

        $invoice->update($validated);

        return response()->json(['message' => 'Invoice updated successfully']);
    }
}
