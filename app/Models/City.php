<?php

namespace App\Models;

use App\Helpers\Num;
use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\LocalizedScope;
use App\Models\Traits\CityTrait;
use App\Models\Traits\Common\AppendsTrait;
use App\Models\Traits\Common\HasCountryCodeColumn;
use App\Observers\CityObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\Crud;
use App\Http\Controllers\Admin\Panel\Library\Traits\Models\SpatieTranslatable\HasTranslations;

class City extends BaseModel
{
	//use HasCountryCodeColumn, CityTrait;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'cities';

	/**
	 * @var array<int, string>
	 */
	protected $appends = ['slug'];

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var boolean
	 */
	public $timestamps = true;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'id',
		'country_code',
		'name',
		'latitude',
		'longitude',
		'state_code',
		'population',
		'time_zone',
		'status',
	];

	public function state()
	{
		return $this->belongsTo(State::class, 'state_code', 'code');
	}

	protected function slug(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				$value = (is_null($value) && isset($this->name)) ? $this->name : $value;

				return slugify($value);
			},
		);
	}
}
