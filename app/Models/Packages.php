<?php


namespace App\Models;

use App\Helpers\NmiPaymentHelper;
use App\Helpers\StripeHelper;
use App\Models\Traits\ActiveTrait;
use App\Traits\Sluggable;
use Illuminate\Support\Str;

class Packages extends BaseModel
{
    use ActiveTrait, Sluggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'packages';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'payment_note',
        'content',
        'package_type', // subscription || payment
        'package_layout',
        'amount',

        'stripe_plan',

        'payment_method',
        'payment_product',
        'payment_plan',
        'payment_interval',
        'payment_interval_count',
        'payment_duration',
        'payment_currency',

        'setup_fee',
        'trial_period',
        'duration',
        'billing_cycle',
        'tax_rate_type',
        'tax_rate',
        'picture',
        'status'
    ];

    protected $appends = ['symbol', 'formatted_amount', 'title_with_amount', 'picture_url'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_title',
            ],
        ];
    }

    public function getStripePlane()
    {
        $payment_method = $this->payment_method ?: settings('payment.default_method');
        $payment_plan = $this->payment_plan;

        $plan = null;

        if ($payment_plan && $payment_method == 'stripe') {
            $stripe = new StripeHelper();

            $plan = $stripe->getSubscriptionPlan($payment_plan);
        }

        return $plan;
    }

    public function getNmiPaymentPlan()
    {
        $payment_method = $this->payment_method ?: settings('payment.default_method');
        $payment_plan = $this->payment_plan;

        $plan = null;
        if ($payment_plan) {
            if (Str::startsWith($payment_method, 'nmi')) {
                $nmi = new NmiPaymentHelper();

                $plan = $nmi->getPlan($payment_plan);
            }

            if ($payment_method == 'stripe') {
                $stripe = new StripeHelper();

                $plan = $stripe->getSubscriptionPlan($payment_plan);
            }
        }

        return $plan;
    }

    public function getPictureUrlAttribute($value)
    {
        return images_url($this->picture, asset('images/picture.jpg'));
    }

    public function getPaymentPlanAttribute($value)
    {
        if (!$value && $this->payment_method == 'stripe') {
            $value = $this->stripe_plan ?? null;
        }

        if (!$value && $this->payment_method == 'nmi') {
            $value = $this->nmi_plan ?? null;
        }

        return $value;
    }


    public function getPaymentIntervalAttribute($value)
    {

        if ($this->package_type != 'subscription') {
            $value = null;
        }

        return $value;
    }

    public function getPaymentIntervalCountAttribute($value)
    {
        if ($this->package_type != 'subscription') {
            $value = null;
        }

        return $value;
    }

    public function getSymbolAttribute()
    {
        $payment_currency = $this->payment_currency;

        return $payment_currency ? \App\Helpers\CurrencyHelper::symbol($payment_currency) : '$';
    }

    public function getFormattedAmountAttribute()
    {
        $amount = number_format($this->amount, 2);
        $symbol = $this->payment_currency ? \App\Helpers\CurrencyHelper::symbol($this->payment_currency) : '$';
        $formatted_amount = $symbol . $amount;
        if ($this->package_type == 'subscription' && $this->payment_interval) {
            $formatted_amount .= ' / ';
            if ($this->payment_interval_count > 1) {
                $formatted_amount .= $this->payment_interval_count . ' ';
            }
            $formatted_amount .= $this->payment_interval;
        }

        return $formatted_amount;
    }

    public function getTitleWithAmountAttribute()
    {
        return $this->title . ' - ' . $this->formatted_amount;
    }

    public function setRecurringAttribute($value)
    {
        $package_type = $this->package_type;

        if ($package_type == 'subscription') {
            $payment_method = $this->payment_method ?: settings('payment.default_method');
            if (Str::startsWith($payment_method, 'nmi')) {
                if (!$this->payment_duration || $this->payment_duration > 1) {
                    $value = 1;
                }
            }
        }

        $this->attributes['recurring'] = $value ?: 0;
    }

    public function setPaymentDurationAttribute($value)
    {
        $this->attributes['payment_duration'] = $value ?: 0;
    }

    public function setPackageTypeAttribute($value)
    {
        if ($value != 'subscription') {
            $this->attributes['stripe_plan'] = null;
            $this->attributes['payment_plan'] = null;
            $this->attributes['payment_product'] = null;
            $this->attributes['payment_method'] = null;
        }

        $this->attributes['package_type'] = $value;
    }

    public function setPaymentPlanAttribute($value)
    {
        $package_type = $this->package_type;

        if ($package_type != 'subscription') {
            $value = null;
        }

        $this->attributes['payment_plan'] = $value;
        $this->attributes['stripe_plan'] = $value;
    }

    public function setPaymentMethodAttribute($value)
    {
        $package_type = $this->attributes['package_type'] ?? $this->package_type;

        if ($package_type != 'subscription') {
            $value = null;
        }

        $this->attributes['payment_method'] = $value;
    }
}
