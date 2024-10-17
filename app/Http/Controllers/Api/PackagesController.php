<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\PackagesResource;
use App\Models\Packages;

/**
 * @group General
 *
 * @unauthenticated
 */
class PackagesController extends ApiController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        $packages = Packages::active()->get();

        return $this->successResponse(PackagesResource::collection($packages));
    }
}
