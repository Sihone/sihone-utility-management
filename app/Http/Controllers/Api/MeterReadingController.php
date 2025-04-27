<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Setting;

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

        $previousReading = MeterReading::where('apartment_id', $validated['apartment_id'])
            ->where('reading_date', '<', $validated['reading_date'])
            ->orderBy('reading_date', 'desc')
            ->first();

        // 🚨 NEW: Only create invoice if previous reading exists
        if ($previousReading) {
            $startIndex = $previousReading->meter_index;
            $endIndex = $newReading->meter_index;
            $consumption = $endIndex - $startIndex;

            // Fetch settings
            $fixedFeeSetting = Setting::where('key', 'fixed_fee')->first();
            if (!$fixedFeeSetting) {
                throw new \Exception('Fixed fee setting not found.');
            }
            $fixedFee = (int) $fixedFeeSetting->value;

            $ratePerM3Setting = Setting::where('key', 'rate_per_m3')->first();
            if (!$ratePerM3Setting) {
                throw new \Exception('Rate per M3 setting not found.');
            }
            $ratePerM3 = (int) $ratePerM3Setting->value;

            $amount = ($consumption * $ratePerM3) + $fixedFee;

            Invoice::create([
                'apartment_id' => $newReading->apartment_id,
                'meter_reading_id' => $newReading->id,
                'start_index' => $startIndex,
                'end_index' => $endIndex,
                'consumption' => $consumption,
                'amount' => $amount,
                'invoice_date' => $newReading->reading_date,
                'fixed_fee_used' => $fixedFee,
                'rate_per_m3_used' => $ratePerM3,
            ]);
        }

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
