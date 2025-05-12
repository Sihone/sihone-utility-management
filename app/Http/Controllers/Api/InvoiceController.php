<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
      return Invoice::with(['apartment.payments', 'meterReading'])
          ->orderBy('invoice_date', 'desc')
          ->get();
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load('apartment.payments', 'meterReading');
    }
}
