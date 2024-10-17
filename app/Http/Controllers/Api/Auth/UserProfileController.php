<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Modules\Auth\DataObject\UserProfileData;
use Modules\Auth\Queries\UserProfileQuery;

class UserProfileController extends ApiController
{
    public function __invoke()
    {

        $query = new UserProfileQuery(auth()->user()->username);

        $user = $query->firstOrFail();

        return UserProfileData::from($user);
    }
}
