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
                if ($this->key == 'seo') {
                    $value['robots_txt'] = $this->readRobotsTxt();
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
    private function readRobotsTxt()
    {
        $path = public_path('robots.txt');

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        return "User-agent: *\nDisallow:";
    }
}
