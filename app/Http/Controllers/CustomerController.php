<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display customer management page.
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = trim((string) $request->input('search', ''));

        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        |
        | Exclude admin accounts from the customer list using the
        | `is_admin` column (rather than just hiding the logged-in user).
        */

        $customers = User::query()
            ->where('is_admin', false)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            })
            ->withCount('orders')
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact(
            'customers',
            'search'
        ));
    }


    /**
     * Display a specific customer.
     */
    public function show(User $user): View
    {
        $customerId = (int) $user->id;

        /*
        |--------------------------------------------------------------------------
        | Customer Orders
        |--------------------------------------------------------------------------
        */

        $orders = Order::query()
            ->where('user_id', $customerId)
            ->latest('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Order Statistics
        |--------------------------------------------------------------------------
        */

        $totalOrders = Order::query()
            ->where('user_id', $customerId)
            ->count();

        $completedOrders = Order::query()
            ->where('user_id', $customerId)
            ->whereIn('status', [
                'delivered',
                'completed',
            ])
            ->count();

        $pendingOrders = Order::query()
            ->where('user_id', $customerId)
            ->whereIn('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
            ])
            ->count();

        $cancelledOrders = Order::query()
            ->where('user_id', $customerId)
            ->where('status', 'cancelled')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Total Spent
        |--------------------------------------------------------------------------
        |
        | Cancelled orders are excluded.
        |
        */

        $totalSpent = Order::query()
            ->where('user_id', $customerId)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'cancelled');
            })
            ->sum('total_amount');

        /*
        |--------------------------------------------------------------------------
        | Paid Amount
        |--------------------------------------------------------------------------
        */

        $paidAmount = Order::query()
            ->where('user_id', $customerId)
            ->where('payment_status', 'Paid')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'cancelled');
            })
            ->sum('total_amount');

        /*
        |--------------------------------------------------------------------------
        | Latest Order
        |--------------------------------------------------------------------------
        */

        $latestOrder = Order::query()
            ->where('user_id', $customerId)
            ->latest('created_at')
            ->first();

        return view('customers.show', compact(
            'user',
            'orders',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'cancelledOrders',
            'totalSpent',
            'paidAmount',
            'latestOrder'
        ));
    }


    /**
     * Show customer edit form.
     */
    public function edit(User $user): View
    {
        return view('customers.edit', compact('user'));
    }


    /**
     * Update customer information.
     */
    public function update(
        Request $request,
        User $user
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Basic Information
        |--------------------------------------------------------------------------
        */

        $user->name = trim($validated['name']);

        $user->email = trim($validated['email']);

        /*
        |--------------------------------------------------------------------------
        | Phone
        |--------------------------------------------------------------------------
        */

        if (array_key_exists('phone', $validated)) {
            $user->phone = !empty($validated['phone'])
                ? trim($validated['phone'])
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['password'])) {
            $user->password = Hash::make(
                $validated['password']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $user->save();

        return redirect()
            ->route('admin.customers.show', [
                'user' => $user->id,
            ])
            ->with(
                'success',
                'Customer information updated successfully.'
            );
    }


    /**
     * PATCH alternative for customer update.
     */
    public function patch(
        Request $request,
        User $user
    ): RedirectResponse {

        return $this->update($request, $user);
    }
}