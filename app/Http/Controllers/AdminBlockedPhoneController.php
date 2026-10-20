<?php

namespace App\Http\Controllers;

use App\Models\BlockedPhone;
use App\Services\SmsService;
use Illuminate\Http\Request;

class AdminBlockedPhoneController extends Controller
{
    public function index()
    {
        return view('admin.blocked-phones.index', [
            'phones' => BlockedPhone::latest()->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'phone'  => ['required', 'string', 'max:30'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $phone = SmsService::normalizePhone($data['phone']);

        if ($phone === null) {
            return back()->withInput()->withErrors(['phone' => 'Enter a valid Bangladeshi mobile number.']);
        }

        BlockedPhone::updateOrCreate(['phone' => $phone], ['reason' => $data['reason'] ?? null]);

        return back()->with('success', 'Phone number blocked.');
    }

    public function destroy(BlockedPhone $blockedPhone)
    {
        $blockedPhone->delete();

        return back()->with('success', 'Phone number unblocked.');
    }
}
