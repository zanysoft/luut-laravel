<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;

/**
 * @group Auth
 *
 * @unauthenticated
 */
class ProfileController extends ApiController
{
    /**
     * Register
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(UserUpdateRequest $request)
    {
        $user = User::where('id', auth('sanctum')->id())->with(['business'])->first();

        return $this->successResponse(UserResource::make($user));
    }

    /**
     * Register
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request)
    {

        $user = auth('sanctum')->user();

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

        $business = array_filter($request->input('business')?:[]);

        if (!$user->business) {
            if (!empty($business)) {
                $user->business()->create($business);
                $user->load('business');
            }
        } else if (!empty($business)) {
            $user->business->update( $request->input('business'));
        }

        $user->save();

        return $this->successResponse(
            'You have been successfully registered.',
            UserResource::make($user)
        );
    }
}
