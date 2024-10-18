<?php

namespace App\Models;

use App\Observers\SettingObserver;
use App\Traits\UtilsTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use UtilsTrait;

    protected $fillable = [
        'key',
        'name',
        'values',
        'stats',
        'order',
    ];

    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        Setting::observe(SettingObserver::class);
    }

    protected function values(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (isJson($value)) {
                    $value = json_decode($value, true);
                }
                return $value;
            },
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode(array_filter($value));
                }
                return $value;
            }
        );
    }

    /**
     * @return false|string
     */
    public function readRobotsTxt()
    {
        $path = public_path('robots.txt');

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return "User-agent: *\nAllow: /";
    }

    /**
     * @param $new_content
     * @return void
     */
    public function updateRobotsTxt($new_content)
    {
        try {
            $path = public_path('robots.txt');
            if (!file_exists($path)) {
                file_put_contents($path, "");
            }
            $old_content = file_get_contents($path);
            if ($new_content) {
                if (preg_replace('/\s+/', '', $new_content) != preg_replace('/\s+/', '', $old_content)) {
                    $file = fopen($path, "w");
                    fwrite($file, $new_content);
                    fclose($file);
                }
            }
        } catch (\Exception $e) {
            alert_message($e->getMessage());
        }
    }
}
