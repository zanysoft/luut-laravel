<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Rules\MatchOldPassword;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * @group Auth
 *
 * @unauthenticated
 */
class ResetPasswordController extends ApiController
{
    /**
     * Reset Password
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam password string required Password should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
        ], [
            'password.regex' => 'Your password should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->successResponse(__($status), 200)
            : $this->errorResponse(['email' => [__($status)]], 403);
    }

    /**
     * Reset Password
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @authenticated
     *
     * @bodyParam current_password required Current password of logged user
     * @bodyParam password required Password should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character
     */
    public function loginUserRestPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'confirm_password' => ['same:password'],
        ], [
            'password.regex' => 'Your password should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character',
        ]);

        if ($request->user()->update(['password' => Hash::make($request->password)])) {
            return $this->successResponse('Password Change Susscessfully', 200);
        }

        return $this->errorResponse('user not found', 404);
    }
}
