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

namespace App\Exceptions;

use App\Exceptions\Traits\ExceptionTrait;
use App\Exceptions\Traits\JsonRenderTrait;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;

class Handler
{
    use ExceptionTrait, JsonRenderTrait;

    protected mixed $app;

    protected ConfigRepository $config;

    public function __construct()
    {
        $this->app = app();
        $this->config = $this->app->instance('config', new ConfigRepository);

        // Fix the 'files' & 'filesystem' binging.
        $this->app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);

    }

    public function __invoke(Exceptions $exceptions): void
    {
        /*
         * Report or log an exception
         */
        $exceptions->report(function (\Throwable $e) {});

        /*
         * Render an exception into an HTTP response
         */
        $exceptions->render(function (\Throwable $e, Request $request) {
            // API or AJAX requests exception
            if (isFromApi($request) || isFromAjax($request)) {
                return $this->jsonRender($e, $request);
            }
        });
    }
}
