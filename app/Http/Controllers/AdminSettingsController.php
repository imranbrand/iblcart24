<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:100'],
            'phone'                  => ['nullable', 'string', 'max:30'],
            'whatsapp'               => ['nullable', 'string', 'max:30'],
            'email'                  => ['nullable', 'email', 'max:150'],
            'address'                => ['nullable', 'string', 'max:255'],
            'hours'                  => ['nullable', 'string', 'max:150'],
            'return_days'            => ['required', 'integer', 'min:0', 'max:365'],
            'delivery_inside_dhaka'  => ['required', 'numeric', 'min:0', 'max:100000'],
            'delivery_outside_dhaka' => ['required', 'numeric', 'min:0', 'max:100000'],
            'free_delivery_above'    => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'delivery_days_inside'   => ['nullable', 'string', 'max:20'],
            'delivery_days_outside'  => ['nullable', 'string', 'max:20'],
            'announcement_text'      => ['nullable', 'string', 'max:200'],
        ]);

        // Empty text boxes are stored as '' so they really clear the value.
        foreach (['phone', 'whatsapp', 'email', 'address', 'hours', 'delivery_days_inside', 'delivery_days_outside'] as $field) {
            $validated[$field] = $validated[$field] ?? '';
        }

        $validated['announcement_text'] = $validated['announcement_text'] ?? '';
        $validated['announcement_enabled'] = $request->boolean('announcement_enabled') ? '1' : '0';
        $validated['free_delivery_above'] = $validated['free_delivery_above'] ?? 0;
        $validated['reviews_require_approval'] = $request->boolean('reviews_require_approval') ? '1' : '0';

        Setting::saveMany($validated);

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Settings saved.');
    }
}
