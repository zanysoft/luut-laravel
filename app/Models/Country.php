<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Country extends BaseModel
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'countries';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * @var array<int, string>
     */

    /**
     * @var array<int, string>
     */
    protected $visible = [
        'code',
        'name',
        'icode',
        'iso3',
        'currency_code',
        'phone',
        'languages',
        'currency',
        'time_zone',
        'date_format',
        'datetime_format',
        'admin_type',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'capital',
        'continent_code',
        'tld',
        'currency_code',
        'phone',
        'languages',
        'time_zone',
        'date_format',
        'datetime_format',
        'background_image',
        'admin_type',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function continent()
    {
        return $this->belongsTo(Continent::class, 'continent_code', 'code');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'country_code')->orderByDesc('created_at');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'country_code')->orderByDesc('created_at');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeActive(Builder $query): Builder
    {
        if (request()->segment(1) == admin_uri()) {
            if (str_contains(currentRouteAction(), 'Admin\CountryController')) {
                return $query;
            }
        }

        return $query->where('status', 1);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS | MUTATORS
    |--------------------------------------------------------------------------
    */
    protected function icode(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtolower($this->attributes['code']),
        );
    }

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->attributes['code'],
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (isset($this->attributes['name']) && !isJson($this->attributes['name'])) {
                    return $this->attributes['name'];
                }

                return $value;
            },
        );
    }

    protected function languages(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $value = explode(',', $value);

                return collect($value)
                    ->map(function ($item) {
                        $item = str_replace('-', '_', $item);

                        return getPrimaryLocaleCode($item);
                    })
                    ->implode(',');
            },
        );
    }

    protected function flagUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return getCountryFlagUrl($this->code);
            },
        );
    }

    protected function flag16Url(): Attribute
    {
        return Attribute::make(
            get: function () {
                return getCountryFlagUrl($this->code, 16);
            },
        );
    }

    protected function flag24Url(): Attribute
    {
        return Attribute::make(
            get: function () {
                return getCountryFlagUrl($this->code, 24);
            },
        );
    }

    protected function flag32Url(): Attribute
    {
        return Attribute::make(
            get: function () {
                return getCountryFlagUrl($this->code, 32);
            },
        );
    }

    protected function flag48Url(): Attribute
    {
        return Attribute::make(
            get: function () {
                return getCountryFlagUrl($this->code, 48);
            },
        );
    }

    protected function flag64Url(): Attribute
    {
        return Attribute::make(
            get: function () {
                return getCountryFlagUrl($this->code, 64);
            },
        );
    }

    protected function backgroundImageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $bgImageUrl = null;
                if (!empty($this->background_image)) {
                    $disk = StorageDisk::getDisk();
                    if ($disk->exists($this->background_image)) {
                        $bgImageUrl = imgUrl($this->background_image, 'bg-header');
                    }
                }

                return $bgImageUrl;
            },
        );
    }

    /*
    |--------------------------------------------------------------------------
    | OTHER PRIVATE METHODS
    |--------------------------------------------------------------------------
    */
}
