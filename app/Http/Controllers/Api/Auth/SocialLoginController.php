<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Exception;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Resources\UserResource;
use App\Services\UserService;

/**
 * @group Auth
 *
 * @unauthenticated
 */
class SocialLoginController extends ApiController
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    /**
     * Social Login
     *
     * @return \Illuminate\Http\JsonResponse|UserResource
     */
    public function socialLogin(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'provider' => ['required', Rule::in(['facebook', 'google'])],

        ]);

        try {
            $socialUser = Socialite::driver($request->provider)->userFromToken($request->token);

            $user = $this->userService->socialLogin($request, $socialUser);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }

        $user->token = $user->createToken($user->email)->plainTextToken;
        $user->new = (!$user->password || !$user->first_name);

        // if ( $request->provider === 'facebook' ) {
        //     $avatar = "{$socialUser->avatar_original}&access_token={$socialUser->token}";
        // }

        // else {
        //     $avatar = $socialUser->avatar;
        // }

        // $user->addMediaFromUrl( $avatar )->toMediaCollection( 'default' );

        return UserResource::make($user);
    }

    /**
     * Google Tap Login
     *
     * @return UserResource
     *
     * @throws ValidationException
     */
    public function googleTapLogin(Request $request)
    {
        $client = new Google_Client(['client_id' => config('services.google.client_id')]);

        $payload = $client->verifyIdToken($request->token_id, true);

        if ($payload) {
            $user = $this->userService->socialLogin($request, (object) $payload);
        } else {
            throw ValidationException::withMessages(
                [
                    'message' => ['The provided credentials are incorrect.'],
                ]
            );
        }

        $user->new = (!$user->password || !$user->first_name);

        return new UserResource($user);
    }
}
