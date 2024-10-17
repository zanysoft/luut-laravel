<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Exceptions\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

trait JsonRenderTrait
{
    public function jsonRender(Throwable $e, Request $request): \Illuminate\Http\JsonResponse
    {
        $detail = $this->geDetails($e, $request);

        // Memory Is Full Exception
        // Called only when reporting some Laravel error traces
        if ($this->isFullMemoryException($e)) {
            $message = strip_tags($this->getFullMemoryMessage($e));
            $data = ['success' => false, 'message' => $message] + $detail;

            return response()->json($data, 500);
        }

        // HTTP Exception
        if ($this->isHttpException($e)) {
            if ($this->isHttp404Exception($e)) {
                $message = ! empty($e->getMessage()) ? $e->getMessage() : 'Page not found.';

                $data = ['success' => false, 'message' => $message] + $detail;

                return response()->json($data, 404);
            }

            // Post Too Large Exception
            if ($this->isHttp413Exception($e)) {
                $message = 'Maximum data (including files to upload) size to post and memory usage are limited on the server.';
                $data = ['success' => false, 'message' => $message, 'code' => $e->getCode()] + $detail;

                if (isFromAjax($request)) {
                    $data['error'] = $message; // for bootstrap-fileinput
                }

                return response()->json($data, Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
            }

            // Authentication Timeout Exception
            if ($this->isHttp419Exception($e)) {
                $message = __('The page expired, please reload it and try again.');
                $data = ['success' => false, 'message' => $message, 'code' => $e->getCode()] + $detail;

                return response()->json($data, 419);
            }
        }

        // Model Not Found Exception
        if ($this->isModelNotFoundException($e)) {
            $message = 'Entry for '.str_replace('App\\', '', $e->getModel()).' not found.';
            $data = ['success' => false, 'message' => $message] + $detail;

            return response()->json($data, 404);
        }

        // DB Query Exception
        if ($this->isQueryException($e)) {
            $message = 'There was issue with the query.';
            $data = ['success' => false, 'message' => $message] + $detail;

            return response()->json($data, 500);
        }

        // Convert an authentication exception into an unauthenticated response
        if ($this->isAuthenticationException($e)) {
            $message = 'Unauthenticated or Token Expired, Please Login.';
            $data = ['success' => false, 'message' => $message] + $detail;

            return response()->json($data, Response::HTTP_UNAUTHORIZED);
        }

        // Throttle Requests Exception
        if ($this->isThrottleRequestsException($e)) {
            $data = [
                'success' => false,
                'message' => 'Too Many Requests, Please Slow Down.',
            ] + $detail;

            return response()->json($data, Response::HTTP_TOO_MANY_REQUESTS);
        }

        // Token Mismatch Exception
        if ($this->isTokenMismatchException($e)) {
            $message = __('The session expired, please reload the page and try again.');
            $data = ['success' => false, 'message' => $message] + $detail;

            return response()->json($data, Response::HTTP_UNAUTHORIZED);
        }

        // Validation Exception
        if ($this->isValidationException($e)) {
            $message = $e->getMessage();

            $data = ['success' => false, 'message' => $message];

            // Get validation error messages
            if (method_exists($e, 'errors')) {
                $errors = $e->errors();
                $data['errors'] = $errors;
            }

            if (isFromAjax($request)) {
                $data['error'] = $message; // for bootstrap-fileinput
            }

            return response()->json($data + $detail, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Error (Exception)
        if ($e instanceof \Error) {
            $message = $e->getMessage();
            if (empty($message)) {
                $message = 'There was some internal error.';
            }

            $data = ['success' => false, 'message' => $message] + $detail;

            return response()->json($data, 500);
        }

        // Other Exception

        // Get status code
        $status = (method_exists($e, 'getStatusCode')) ? $e->getStatusCode() : 500;
        $status = $this->isValidHttpStatus($status) ? $status : 500;

        // Get error message
        $message = $e->getMessage();
        if (! empty($message)) {
            $message = ! empty($e->getLine()) ? $message.' Line: '.$e->getLine() : $message;
        } else {
            $message = $this->getHttpErrorMessage($status);
        }

        $data = ['success' => false, 'message' => $message] + $detail;

        return response()->json($data, $status);
    }

    protected function geDetails(Throwable $e, Request $request): array
    {
        $data = [];

        if (isFromAjax($request)) {
            $data['error'] = $e->getMessage();
        }

        if (! app()->isProduction()) {
            $data = array_merge($data, [
                'code' => $e->getCode(),
                'file' => $e->getFile().' Line: '.$e->getLine(),
                'trace' => $e->getTrace(),
            ]);
        }

        return $data;
    }
}
