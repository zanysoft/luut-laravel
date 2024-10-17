<?php

namespace App\Models;

use App\Traits\Scopeable;
use App\Traits\Sluggable;
use App\Traits\UtilsTrait;
use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    use Scopeable, Sluggable, UtilsTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['slug', 'name', 'status'];
}
