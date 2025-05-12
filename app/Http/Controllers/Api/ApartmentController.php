<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    // List all apartments
    public function index()
    {
      return Apartment::with(['payments'])
          ->get();
    }

    // Create a new apartment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'tenant_name' => 'nullable|string|max:255',
            'tenant_phone' => 'nullable|string|max:20',
            'tenant_email' => 'nullable|email|max:255',
        ]);

        $apartment = Apartment::create($validated);

        return response()->json($apartment, 201); // 201 = Created
    }

    // Show a single apartment (optional for now)
    public function show(Apartment $apartment)
    {
        $apartment->load('payments');
        return $apartment;
    }

    // Update an apartment
    public function update(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string|max:255',
            'tenant_name' => 'nullable|string|max:255',
            'tenant_phone' => 'nullable|string|max:20',
            'tenant_email' => 'nullable|email|max:255',
        ]);

        $apartment->update($validated);

        return response()->json($apartment);
    }

    // Delete an apartment
    public function destroy(Apartment $apartment)
    {
        $apartment->delete();

        return response()->json(null, 204); // 204 = No Content
    }
}
