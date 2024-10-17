<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ActionController extends Controller
{

    public function clearCache(Request $request)
    {

        $all = $request->get('all');
        $cache = $request->get('cache');
        $views = $request->get('views');
        $configs = $request->get('config');
        $log = $request->get('log');

        if ($request->method() == 'GET') {
            $all = true;
        }

        $errors = [];

        // Removing all Objects Cache
        if ($all || $cache) {
            try {
                Artisan::call('cache:clear');
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Removing all Views Cache
        if ($all || $views) {
            try {
                Artisan::call('view:clear');
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Removing all route Cache
        if ($all) {
            try {
                Artisan::call('route:clear');
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Removing all config Cache
        if ($all || $configs) {
            try {
                Cache::forget('settings.active');
                Artisan::call('config:clear');
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Removing all Logs
        if ($all || $log) {
            try {
                File::delete(File::glob(storage_path('logs') . '/laravel*.log'));
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Check if error occurred
        if (count($errors)) {
            foreach ($errors as $error) {
                alert_message($error, 'error');
            }
        } else {
            $message = __("The cache was successfully dumped.");
            alert_message($message, 'success');
        }

        return redirect()->back();
    }
}
