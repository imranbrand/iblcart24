<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update User
        |--------------------------------------------------------------------------
        */

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        $user->save();

        /*
        |--------------------------------------------------------------------------
        | Email Verification Reset
        |--------------------------------------------------------------------------
        */

        if (
            $user->wasChanged('email') &&
            method_exists($user, 'hasVerifiedEmail') &&
            $user->hasVerifiedEmail()
        ) {
            $user->email_verified_at = null;
            $user->save();
        }

        return redirect('/profile')->with(
            'status',
            'profile-updated'
        );
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update Password
        |--------------------------------------------------------------------------
        */

        $user->update([
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Logout Other Sessions
        |--------------------------------------------------------------------------
        */

        if (method_exists(Auth::guard(), 'logoutOtherDevices')) {
            Auth::guard()->logoutOtherDevices(
                $validated['password']
            );
        }

        return back()->with(
            'status',
            'password-updated'
        );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => [
                'required',
                'current_password',
            ],
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        Auth::logout();

        /*
        |--------------------------------------------------------------------------
        | Delete User
        |--------------------------------------------------------------------------
        */

        $user->delete();

        /*
        |--------------------------------------------------------------------------
        | Invalidate Session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')
            ->with(
                'status',
                'account-deleted'
            );
    }
}