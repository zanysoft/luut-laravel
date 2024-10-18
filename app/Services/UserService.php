<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(Request $request, ?User $user): User
    {
        if (!$user) {
            $data = $request->except(['password_confirmation', 'token_id', 'state', 'password', 'token', 'provider']);
            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $user = User::create(['status' => 1] + $data);
        } else {
            $data = ['status' => 1];
            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $user->update($data);
        }

        return $user;
    }

    public function socialLogin(Request $request, $data)
    {
        $socialUserName = explode(' ', $data->name);

        return $this->createUser($request->merge([
            'email' => $data->email,
            'first_name' => $socialUserName[0],
            'last_name' => @$socialUserName[1],
            'provider_id' => $data->id ?? $data->jti,
            'provider_name' => $request->provider ?? 'google',
            'email_verified_at' => Carbon::now(),
        ]), $this->findUser($data->email));
    }

    protected function findUser(string $email): ?User
    {
        $user = User::whereEmail($email)->withTrashed()->first();

        if ($user && $user->trashed()) {
            $user->restore();
        }

        return $user;
    }

    public function createBusinessProfile(Request $request, User $user): User
    {

        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'status' => false,
            'ip_address' => $request->ip_address ?: $request->ip(),
            //'ip_address' => $request->ip_address(),
        ];

        if (!$user) {
            $user = User::create($userData);
        } else {
            $user->update(['first_name' => $request->first_name, 'last_name' => $request->last_name]);
        }

        return $user;
    }
}
