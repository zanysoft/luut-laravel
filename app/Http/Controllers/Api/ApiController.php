<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    use ApiResponse;

    /**
     * @param $message
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    protected function successResponse($message, $data = [], $code = 200): JsonResponse
    {
        if (!is_string($message) && empty($data)) {
            $data = $message;
            $message = '';
        }

        $output = [
            'status' => true,
            'message' => $message ?: (str_replace('Controller', '', class_basename($this)) . ' data loaded'),
        ];

        if ($data instanceof AnonymousResourceCollection || $data instanceof JsonResource) {
            $response = $data->response();
            $responseData = $response->getData(true);

            if (isset($responseData['data'])) {
                $output += $responseData;
            } else {
                $output['data'] = $responseData;
            }
        } else {
            if ($data instanceof Arrayable) {
                $data = $data->toArray();
            }

            if ($data instanceof JsonResponse) {
                $data = $data->getData(true);
            }

            if ($data instanceof Response) {
                $data = $data->getContent();
            }

            if (is_array($data)) {
                array_walk_recursive($data, function ($item) {
                    if ($item instanceof Closure) {
                        throw new \InvalidArgumentException('Invalid data format: contains Closure');
                    }
                });

                if (isset($data['data'])) {
                    $output += $data;
                } else {
                    $output['data'] = $data;
                }
            } else {
                // Handle the case where $data is not an array
                $output['data'] = $data;
            }
        }

        return response()->json($output, $code);
    }

    protected function errorResponse($message, $code = null, $errors = []): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => !empty($errors) ? $errors : (is_array($message) ? $message : null),
        ], $code ?: Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function successResponseJson($data, $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message ?? 'Data Found',
            'data' => $data,
        ], $code);
    }
}
