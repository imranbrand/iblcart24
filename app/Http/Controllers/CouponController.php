<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    /**
     * List all coupons (admin).
     */
    public function index(): View
    {
        $coupons = Coupon::query()
            ->latest()
            ->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show create form (admin).
     */
    public function create(): View
    {
        return view('admin.coupons.create');
    }

    /**
     * Store a new coupon (admin).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $validated['code'] = strtoupper(trim($validated['code']));

        Coupon::create($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    /**
     * Show edit form (admin).
     */
    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update a coupon (admin).
     */
    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $this->validated($request, $coupon->id);

        $validated['code'] = strtoupper(trim($validated['code']));

        $coupon->update($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    /**
     * Delete a coupon (admin).
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    /**
     * Apply a coupon code to the current cart/session (customer).
     */
    public function apply(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $coupon = Coupon::findValidByCode($request->input('code'));

        if (!$coupon) {
            return back()->with('error', 'This coupon code is invalid or expired.');
        }

        // Check minimum order amount against current cart subtotal.
        $cart = collect(session()->get('cart', []));

        $subtotal = $cart->sum(function ($item) {
            return ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 0));
        });

        if ($coupon->calculateDiscount($subtotal) <= 0) {
            return back()->with(
                'error',
                'This coupon requires a minimum order of ৳' . number_format((float) $coupon->min_order_amount, 2) . '.'
            );
        }

        session()->put('coupon_code', $coupon->code);

        return back()->with('success', "Coupon \"{$coupon->code}\" applied successfully.");
    }

    /**
     * Remove the applied coupon from session (customer).
     */
    public function remove(): RedirectResponse
    {
        session()->forget('coupon_code');

        return back()->with('success', 'Coupon removed.');
    }

    /**
     * Shared validation rules for store/update.
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:coupons,code' . ($ignoreId ? ",{$ignoreId}" : ''),
            ],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        if (($validated['type'] ?? null) === 'percent' && (float) $validated['value'] > 100) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'value' => 'Percentage coupon value cannot exceed 100%.',
            ]);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}