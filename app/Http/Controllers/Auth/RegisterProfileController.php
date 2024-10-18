<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\Api\BusinessTypeResource;
use App\Http\Resources\Api\PackagesResource;
use App\Http\Resources\UserResource;
use App\Models\BusinessType;
use App\Models\Packages;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class RegisterProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $businessTypes = BusinessType::active()->get();
        if ($businessTypes->count()) {
            $businessTypes = BusinessTypeResource::collection($businessTypes)->toArray($request);
        }

        $packages = Packages::active()->get();
        if ($packages->count()) {
            $packages = PackagesResource::collection($packages)->toArray($request);
        }

        return Inertia::render('Auth/Profile', [
            'user' => UserResource::make($request->user())->toArray($request),
            'business_types' => $businessTypes,
            'packages' => $packages,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UserUpdateRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if ($request->has('first_name')) {
            $user->first_name = $request->input('first_name');
        }
        if ($request->has('last_name')) {
            $user->last_name = $request->input('last_name');
        }
        if ($request->has('gender')) {
            $user->gender = $request->input('gender');
        }
        if ($request->has('dob')) {
            $user->dob = $request->input('dob');
        }
        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }
        if ($request->has('address')) {
            $user->address = $request->input('address');
        }
        if ($request->has('address')) {
            $user->address = $request->input('address');
        }
        if ($request->has('city')) {
            $user->city = $request->input('city');
        }
        if ($request->has('state')) {
            $user->state = $request->input('state');
        }
        if ($request->has('country_code')) {
            $user->country_code = $request->input('country_code');
        }
        if ($request->has('zipcode')) {
            $user->zipcode = $request->input('zipcode');
        }

        if ($request->has('password') && $pass = $request->input('password')) {
            $user->password = bcrypt($pass);
        }

        $business = array_filter($request->input('business') ?: []);

        if (!$user->business) {
            if (!empty($business)) {
                $user->business()->create($business);
                $user->load('business');
            }
        } else {
            if (!empty($business)) {
                $user->business->update($request->input('business'));
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('register.profile');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
