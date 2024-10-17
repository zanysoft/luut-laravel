<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Auth
 *
 * @unauthenticated
 */
class AuthController extends ApiController
{
    /**
     * Register
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRegisterRequest $request, UserService $userService)
    {

        $isExsist = User::withoutGlobalScopes()->whereEmail($request->email)->first();

        $user = $userService->createUser($request, $isExsist);

        $user->token = $user->createToken($request->email)->plainTextToken;

        $user->new = (!$user->password || !$user->first_name);

        //event(new Registered($user));

        return $this->successResponse(
            'You have been successfully registered.',
            UserResource::make($user)
        );
    }

    /**
     * Login
     *
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function login(UserLoginRequest $request)
    {
        $user = User::where([['email', $request->email], ['status', 1]])->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        /*if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            throw ValidationException::withMessages([
                'email' => ['Your email is not verified. We have sent you a verification email to confirm your email address.'],
            ]);
        }*/

        $user->token = $user->createToken($request->email)->plainTextToken;
        $user->new = (!$user->password || !$user->first_name);

        return $this->successResponse(
            'Logged in successfully.',
            UserResource::make($user)
        );
    }

    /**
     * Logout
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @authenticated
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return $this->successResponse(' ', 200, 'Logged out.');
    }

    /**
     * Mark As Verified
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsVerified($userId)
    {

        $user = User::find($userId);

        if ($user) {
            $user->markEmailAsVerified();
            event(new Verified($user));

            return $this->successResponse($user, 200, 'Email Verified.');
        }

        return $this->errorResponse('Email not verified', 403);
    }
}
