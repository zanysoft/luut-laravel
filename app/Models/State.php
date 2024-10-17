<?php

namespace App\Models;


class State extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'states';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['country_code', 'code', 'name', 'status'];

    public function cities()
    {
        return $this->hasMany(City::class, 'state_code', 'code');
    }
}
