<?php

namespace App\Models;

use App\Traits\Scopeable;
use App\Traits\Sluggable;
use App\Traits\UtilsTrait;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use Scopeable, Sluggable, UtilsTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type_id',
        'name',
        'slug',
        'description',
        'phone',
        'phone2',
        'mobile',
        'fax',
        'email',
        'website',

        'address',
        'address2',
        'city',
        'state',
        'country_code',
        'zipcode',
        'time_zone',
        'language',
        'social_links'
    ];

    protected $casts =[
        'social_links' => 'array'
    ];
}
