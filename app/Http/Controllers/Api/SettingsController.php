<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
      {
          $settings = Setting::all()->pluck('value', 'key');
          return response()->json($settings);
      }

      public function update(Request $request)
      {
          foreach ($request->all() as $key => $value) {
              Setting::updateOrCreate(
                  ['key' => $key],
                  ['value' => is_array($value) ? json_encode($value) : $value]
              );
          }

          return response()->json(['message' => 'Settings updated successfully']);
      }
}
