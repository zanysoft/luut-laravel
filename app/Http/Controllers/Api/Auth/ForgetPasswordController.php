<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

/**
 * @group Auth
 */
class ForgetPasswordController extends ApiController
{
    /**
     * Forgot Password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        // ResetPassword::createUrlUsing(function ($user, $token) {
        //     return config('app.web_domain') . '/password/reset/' . $token . '?email=' . $user->email;
        // });

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? $this->successResponse(__($status))
            : $this->errorResponse(__($status), 403);
    }
}
