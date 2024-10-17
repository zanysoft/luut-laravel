<?php

namespace App\Models;

use App\Http\Controllers\Admin\Panel\Library\Traits\Models\Crud;
use App\Models\Traits\Common\AppendsTrait;
use App\Models\Traits\CurrencyTrait;
use App\Observers\CurrencyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends BaseModel
{
	//use CurrencyTrait;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'currencies';

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
		'symbol',
		'html_entities',
		'in_left',
		'decimal_places',
		'decimal_separator',
		'thousand_separator',
	];

	protected static function boot()
	{
		parent::boot();

		//Currency::observe(CurrencyObserver::class);
	}

	public function countries()
	{
		return $this->hasMany(Country::class, 'currency_code', 'code');
	}

	protected function id(): Attribute
	{
		return Attribute::make(
			get: fn($value) => $this->attributes['code'] ?? $value,
		);
	}

	protected function symbol(): Attribute
	{
		return Attribute::make(
			get: function ($value) {
				if (trim($value) == '') {
					if (isset($this->attributes['symbol'])) {
						$value = $this->attributes['symbol'];
					}
				}
				if (trim($value) == '') {
					if (isset($this->attributes['html_entities'])) {
						$value = $this->attributes['html_entities'];
					}
				}
				if (trim($value) == '') {
					if (isset($this->attributes['code'])) {
						$value = $this->attributes['code'];
					}
				}

				return $value;
			},
		);
	}
}
