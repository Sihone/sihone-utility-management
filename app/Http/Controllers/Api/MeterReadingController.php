<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use Illuminate\Http\Request;

class MeterReadingController extends Controller
{
    public function index()
    {
        return MeterReading::with('apartment')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'reading_date' => 'required|date',
            'meter_index' => 'required|integer',
        ]);

        return MeterReading::create($request->all());
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
