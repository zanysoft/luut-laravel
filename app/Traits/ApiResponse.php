<?php

namespace App\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait ApiResponse
{
    protected function successResponse($data, $message = '', $code = 200): JsonResponse
    {
        if (is_string($data)) {
            $message = $data;
            $data = [];
        }

        $output = [
            'status' => true,
            'message' => $message ?: (str_replace('Controller', '', class_basename($this)) . ' data loaded'),
        ];

        if ($data instanceof AnonymousResourceCollection) {
            $response = $data->toResponse(request());
            $responseData = $response->getData(true);

            if (isset($responseData['data'])) {
                $output += $responseData;
            } else {
                $output['data'] = $responseData;
            }
        } elseif ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $output['current_page'] = $data->currentPage();
            $output['last_page'] = $data->lastPage();
            $output['total_record'] = $data->total();
            $output['per_page'] = $data->perPage();
            $output['to'] = $data->lastItem();
            $output['data'] = $data->getCollection()->toArray();
        } else {
            if ($data instanceof Arrayable) {
                $data = $data->toArray();
            }

            $output['data'] = $data;
        }

        return response()->json($output, $code);
    }

    protected function errorResponse($message, $code, $errors = []): JsonResponse
    {
        if (empty($message) && $code == 422) {
            $message = 'An error occurred while validating the data.';
        }

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
