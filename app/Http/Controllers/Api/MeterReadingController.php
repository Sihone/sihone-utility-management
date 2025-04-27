<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use App\Models\Invoice;

class MeterReadingController extends Controller
{
    public function index()
    {
        return MeterReading::with('apartment')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'reading_date' => 'required|date',
            'meter_index' => 'required|integer',
        ]);

        $newReading = MeterReading::create($validated);

        // Find previous reading
        $previousReading = MeterReading::where('apartment_id', $validated['apartment_id'])
            ->where('reading_date', '<', $validated['reading_date'])
            ->orderBy('reading_date', 'desc')
            ->first();

        $startIndex = $previousReading ? $previousReading->meter_index : 0;
        $endIndex = $newReading->meter_index;
        $consumption = $endIndex - $startIndex;

        $indexRate = 1000; // FCFA per m³
        $fixedFee = 5000;  // FCFA
        $amount = ($consumption * $indexRate) + $fixedFee;

        Invoice::create([
            'apartment_id' => $newReading->apartment_id,
            'meter_reading_id' => $newReading->id,
            'start_index' => $startIndex,
            'end_index' => $endIndex,
            'consumption' => $consumption,
            'amount' => $amount,
            'invoice_date' => $newReading->reading_date,
        ]);

        return $newReading;
    }

    public function show(MeterReading $meterReading)
    {
        return $meterReading;
    }

    public function update(Request $request, MeterReading $meterReading)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'reading_date' => 'required|date',
            'meter_index' => 'required|integer',
        ]);

        $meterReading->update($request->all());

        return $meterReading;
    }

    public function destroy(MeterReading $meterReading)
    {
        $meterReading->delete();

        return response()->noContent();
    }
}
