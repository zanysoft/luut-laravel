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

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

trait ExceptionTrait
{
    /**
     * Is a PDO Exception
     */
    protected function isPDOException(\Throwable $e): bool
    {
        if (
            ($e instanceof \PDOException)
            || $e->getCode() == 1045
            || str_contains($e->getMessage(), 'SQLSTATE')
            || str_contains($e->getFile(), 'Database/Connectors/Connector.php')
        ) {
            return true;
        }

        return false;
    }

    protected function isValidHttpStatus($code): bool
    {
        $code = is_numeric($code) ? $code : 500;

        return array_key_exists($code, \Illuminate\Http\Response::$statusTexts);
    }

    protected function getHttpErrorMessage($code)
    {
        $code = is_numeric($code) ? $code : 500;

        return \Illuminate\Http\Response::$statusTexts[$code] ?? 'Internal Server Error';
    }

    protected function isQueryException(\Throwable $e): bool
    {
        return $e instanceof QueryException;
    }

    /**
     * Check if it is a DB connection exception
     *
     * DB Connection Error:
     * http://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
     */
    protected function isDBConnectionException(\Throwable $e): bool
    {
        $dbErrorCodes = [
            'mysql' => ['1042', '1044', '1045', '1046', '1049'],
            'standardized' => ['08S01', '42000', '28000', '3D000', '42000', '42S22'],
        ];

        return
            $this->isPDOException($e)
            || in_array($e->getCode(), $dbErrorCodes['mysql'])
            || in_array($e->getCode(), $dbErrorCodes['standardized']);
    }

    /**
     * Check if it is a DB table error exception
     *
     * DB Connection Error:
     * http://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
     */
    protected function isDBTableException(\Throwable $e): bool
    {
        $tableErrorCodes = [
            'mysql' => ['1051', '1109', '1146'],
            'standardized' => ['42S02'],
        ];

        return
            $this->isPDOException($e)
            || in_array($e->getCode(), $tableErrorCodes['mysql'])
            || in_array($e->getCode(), $tableErrorCodes['standardized']);
    }

    /**
     * Determine if the given exception is an HTTP exception.
     */
    protected function isHttpException(\Throwable $e): bool
    {
        return $e instanceof HttpExceptionInterface;
    }

    protected function isHttp404Exception(\Throwable $e): bool
    {
        return
            $this->isHttpException($e)
            && method_exists($e, 'getStatusCode')
            && $e->getStatusCode() == 404;
    }

    /**
     * Check if it is an HTTP Method Not Allowed exception
     */
    protected function isHttp405Exception(\Throwable $e): bool
    {
        return
            $e instanceof MethodNotAllowedHttpException
            || (
                $this->isHttpException($e)
                && method_exists($e, 'getStatusCode')
                && $e->getStatusCode() == 405
            );
    }

    /**
     * Check it is a 'Post Too Large' exception
     */
    protected function isHttp413Exception(\Throwable $e): bool
    {
        return
            $e instanceof PostTooLargeException
            || (
                $this->isHttpException($e)
                && method_exists($e, 'getStatusCode')
                && $e->getStatusCode() == 413
            );
    }

    /**
     * Check if the page is expired
     */
    protected function isHttp419Exception(\Throwable $e): bool
    {
        return
            $this->isHttpException($e)
            && method_exists($e, 'getStatusCode')
            && $e->getStatusCode() == 419;
    }

    /**
     * Check it is a Validation exception
     */
    protected function isValidationException(\Throwable $e): bool
    {
        return $e instanceof ValidationException || $e->getCode() == 422;
    }

    /**
     * Check if it is caching exception (APC or Redis)
     */
    protected function isCachingException(\Throwable $e): bool
    {
        return $this->isAPCCachingException($e) || $this->isRedisCachingException($e);
    }

    protected function isAPCCachingException(\Throwable $e): bool
    {
        return (bool) preg_match('#apc_#ui', $e->getMessage());
    }

    protected function isRedisCachingException(\Throwable $e): bool
    {
        return (bool) preg_match('#/predis/#i', $e->getFile());
    }

    protected function isModelNotFoundException(\Throwable $e): bool
    {
        return $e instanceof ModelNotFoundException;
    }

    protected function isTokenMismatchException(\Throwable $e): bool
    {
        return $e instanceof TokenMismatchException;
    }

    protected function isAuthenticationException(\Throwable $e): bool
    {
        return $e instanceof AuthenticationException;
    }

    protected function isThrottleRequestsException(\Throwable $e): bool
    {
        return $e instanceof ThrottleRequestsException;
    }

    protected function isFullMemoryException(\Throwable $e): bool
    {
        return
            str_contains($e->getMessage(), 'Allowed memory size of')
            && str_contains($e->getMessage(), 'tried to allocate');
    }

    protected function getFullMemoryMessage(\Throwable $e): string
    {
        // Memory is full
        $message = $e->getMessage().". \n";
        $message .= 'The server\'s memory must be increased so that it can support the load of the requested resource.';

        return $message;
    }
}
