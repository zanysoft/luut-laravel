<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\BusinessTypeResource;
use App\Models\BusinessType;

/**
 * @group General
 *
 * @unauthenticated
 */
class BusinessTypeController extends ApiController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        $types = BusinessType::active()->get();

        return $this->successResponse(BusinessTypeResource::collection($types));
    }
}
