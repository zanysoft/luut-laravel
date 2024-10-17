<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
function isFromApi(?Request $request = null): bool
{
    if (! $request instanceof Request) {
        $request = request();
    }

    return
        str_starts_with($request->path(), 'api/')
        || $request->is('api/*')
        || $request->segment(1) == 'api'
        || ($request->hasHeader('X-API-CALLED') && $request->header('X-API-CALLED'));
}

function isFromAjax(?Request $request = null): bool
{
    if (! $request instanceof Request) {
        $request = request();
    }

    return $request->ajax() || $request->wantsJson();
}

/**
 * @param string|null $key
 * @param $default
 * @return mixed
 */
function settings(string $key = null, $default = null): mixed
{
    if ($key != null) {
        return config('settings.' . $key, $default);
    }

    return config('settings');
}

if (!function_exists('dbRaw')) {
    function dbRaw(string $query, string $connection = null)
    {
        $expression = DB::raw($query);

        return $expression->getValue(DB::connection($connection)->getQueryGrammar());
    }
}
if (!function_exists('formatDate')) {
    /**
     * @param $date
     * @param $format
     * @return mixed|string
     */
    function formatDate($date, $format = 'Y-m-d')
    {
        if ($date) {
            if ($date instanceof DateTime) {
                $date = $date->format($format);
            } else {
                $date = date($format, strtotime($date));
            }
        }
        return $date;
    }
}
/**
 * @param $string
 * @return bool
 */
function isJson($string)
{
    if (!$string) {
        return false;
    }
    return !empty($string) && is_string($string) && is_array(json_decode($string, true)) && json_last_error() == 0;
}

if (!function_exists('array_filter_recursive')) {
    function array_filter_recursive(array $array, callable $callback = null)
    {
        $array = is_callable($callback) ? array_filter($array, $callback) : array_filter($array);
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = call_user_func(__FUNCTION__, $value, $callback);

                if (empty($value)) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }
}
if (!function_exists('mask_string')) {
    function mask_string(string $string, $visible = null)
    {
        $length = strlen($string);

        $visibleCount = $visible ?: (int)round($length / 4);
        $hiddenCount = $length - ($visibleCount * 2);

        if ($length <= ($visible * 2)) {
            return $string;
        }

        $prefix = substr($string, 0, $visibleCount);
        $suffix = substr($string, ($visibleCount * -1), $visibleCount);
        $mask = str_repeat('*', $hiddenCount);

        return $prefix . $mask . $suffix;
    }
}

if (!function_exists('storage')) {
    /**
     * @param $disk
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    function storage($disk = 'public'): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk($disk);
    }
}


/**
 * @param $url
 * @param null $default
 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string|void|null
 */
function images_url($url, $default = null)
{
    if ($url) {
        $app_url = trim(config('app.url'), '/');

        $url = trim(str_replace($app_url, '', $url), '/');

        if (storage()->exists($url)) {
            return storage()->url($url);
        }

        if (file_exists(public_path($url))) {
            return url($url);
        }
    }

    if ($default && !Str::startsWith($default, asset(''))) {
        $default = asset($default);
    }

    return $default;
}
function in_arrayi($needle, $haystack)
{
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

function num_pad(int|float $number, int $length, int $pad_number = 0, int $pad_type = 1)
{
    if (\Illuminate\Support\Str::contains($number, '.') && $pad_type == 0) {
        list($num, $dec) = explode('.', $number);
        return str_pad($num, $length, $pad_number, $pad_type) . '.' . $dec;
    }

    return str_pad($number, $length, $pad_number, $pad_type);
}

