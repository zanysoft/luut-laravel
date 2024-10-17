<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;

/**
 * @group Auth
 *
 * @unauthenticated
 */
class VerificationController extends ApiController
{
    /**
     * Send Verification Email
     *
     * @return string[]
     */
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Already Verified',
            ];
        }

        $request->user()->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }

    /**
     * Verify Email
     *
     * @return \Illuminate\Http\JsonResponse|string[]
     */
    public function verify(EmailVerificationRequest $request)
    {

        $user = User::find($request->route('id'));

        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Email already verified',
            ];
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->successResponse('Email has been verified', 200);
    }
}
