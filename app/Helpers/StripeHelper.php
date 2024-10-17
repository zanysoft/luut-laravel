<?php


namespace App\Helpers;


use App\Models\Setting;
use Carbon\Carbon;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeHelper
{

    protected $api_mode = '';

    protected $api_key = '';

    protected $public_key = '';

    public $client = null;

    public function __construct($config = [])
    {
        $this->api_mode = settings('payment.stripe_mode', 'test');

        $this->api_key = static::getSecretKey();

        $this->public_key = static::getPublicKey();

        try {
            if ($this->api_key) {
                $config['api_key'] = $this->api_key;
                $config['stripe_version'] = '2020-03-02';

                $this->client = new StripeClient($config);
            }
        } catch (\Exception $e) {
            dd("Stripe Error: " . $e->getMessage());
        }
    }

    /**
     * @return string
     */
    public static function getSecretKey()
    {
        $api_mode = settings('payment.stripe_mode', 'test');

        return $api_mode == 'live' ? settings('payment.stripe_secret_key') : settings('payment.stripe_test_secret_key');
    }

    /**
     * @return string
     */
    public static function getPublicKey()
    {
        $api_mode = settings('payment.stripe_mode', 'test');

        return $api_mode == 'live' ? settings('payment.stripe_public_key') : settings('payment.stripe_test_public_key');
    }

    /**
     * @param null $params
     * @param null $opts
     * @return array|mixed|null
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getProductList($productIds = null)
    {
        $products = [];
        if (!$this->client) {
            alert_message("Stripe api keys not set properly");
            return $products;
        }
        try {
            $params = [
                'limit' => 100,
                'include[]' => 'total_count',
                'expand' => ['data.prices']
            ];

            if (!is_null($productIds)) {
                if (is_string($productIds)) {
                    $productIds = explode(',', $productIds);
                }
                if (count($productIds) > 0) {
                    $params['ids'] = $productIds;
                }
            }

            $productCollection = $this->client->products->all($params);
            foreach ($productCollection->autoPagingIterator() as $product) {
                $products[$product->id] = $product;
            }
        } catch (ApiErrorException $e) {
            alert_message("Get stripe product list error<br>" . $e->getMessage());
            $products = [];
        }

        return $products;
    }

    /**
     * @param $id
     * @return \Stripe\Price|null
     */
    public function getSubscriptionPlan($id)
    {
        $plan = null;
        if (!$this->client) {
            alert_message("Stripe api keys not set properly");
            return $plan;
        }
        try {
            $plan = $this->client->prices->retrieve($id, ['expand' => ['product']]);

            if ($plan->id) {
                $plan->name = $plan->product->name;
                if ($plan->recurring) {
                    $plan->interval = $plan->recurring['interval'] ?? null;
                    $plan->interval_count = $plan->recurring['interval_count'] ?? null;
                }
                $plan->amount = $plan->unit_amount ? $plan->unit_amount / 100 : 0;
                if ($plan->currency) {
                    $plan->currency_symbol = CurrencyHelper::symbol($plan->currency);
                }
            }
        } catch (ApiErrorException $e) {
            alert_message("Get stripe product list error<br>" . $e->getMessage());
            $plans = null;
        }

        return $plan;
    }

    /**
     * @return string
     * @throws ApiErrorException
     */
    public static function setupIntentClientSecret()
    {
        $instant = new static();
        $intent = $instant->client->setupIntents->create();

        return $intent->client_secret ?? '';
    }

    /**
     * @param string $type type should be recurring or one_time
     * @param string | null $product_id
     * @return array
     */
    public function getSubscriptionPlans($type = null, $product_id = null)
    {
        $plans = [];
        if (!$this->client) {
            alert_message("Stripe api keys not set properly");
            return $plans;
        }
        try {
            $params = [
                'active' => true,
                'limit' => 100,
                'include[]' => 'total_count',
                'expand' => ['data.product']
            ];

            if ($type) {
                $params['type'] = $type;
            }

            if ($product_id) {
                $params['product'] = $product_id;
            }

            $planCollection = $this->client->prices->all($params);
            foreach ($planCollection->autoPagingIterator() as $plan) {
                $plan->name = $plan->product->name;
                if ($plan->recurring) {
                    $plan->interval = $plan->recurring['interval'] ?? 0;
                    $plan->interval_count = $plan->recurring['interval_count'] ?? 0;
                }
                $plan->amount = $plan->unit_amount ? $plan->unit_amount / 100 : 0;
                if ($plan->currency) {
                    $plan->currency_symbol = CurrencyHelper::symbol($plan->currency);
                }

                $plans[$plan->id] = $plan;
            }
        } catch (ApiErrorException $e) {
            alert_message("Get stripe product list error<br>" . $e->getMessage());
            $plans = [];
        }
        if ($type == 'recurring') {
            $plans = array_filter($plans, function ($a) {
                return !in_array($a['billing_scheme'], ['metered', 'tiered']);
            });
        }

        return $plans;
    }

    /**
     * @param null $product_id
     * @return array
     */
    public function getRecurringPlansByProduct($product_id = null)
    {
        $plans = $this->getSubscriptionPlans('recurring', $product_id);

        $output = [];
        foreach ($plans as $plan) {

            $product_id = $plan->product->id;
            if (!isset($output[$product_id])) {
                $output[$product_id] = $plan->product;
            }
            unset($plan->product);
            $output[$product_id]->prices[] = $plan;
        }

        return $output;
    }

    /**
     * @param null $product_id
     * @return array
     */
    public function getRecurringPlans($product_id = null)
    {
        return $this->getSubscriptionPlans('recurring', $product_id);
    }

    /**
     * @param null $product_id
     * @return array
     */
    public function getOneTimePlans($product_id = null)
    {
        return $this->getSubscriptionPlans('one_time', $product_id);
    }

    public function getTaxRates()
    {
        $taxRates = [];
        if (!$this->client) {
            alert_message("Stripe api keys not set properly");
            return $taxRates;
        }

        do {
            $params = [
                'active' => true,
                'inclusive' => false,
                'limit' => 100,
                'include[]' => 'total_count'
            ];

            $lastTaxRate = end($taxRates);
            if ($lastTaxRate) {
                $params['starting_after'] = $lastTaxRate['id'];
            }
            $taxRateCollection = $this->client->taxRates->all($params);
            $taxRates = array_merge($taxRates, $taxRateCollection['data']);
        } while ($taxRateCollection['has_more']);

        return $taxRates;
    }

    public static function getLastWebHookEventStatus($isLiveMode)
    {
        $time = Setting::getOption('last_webhook_event_' . ($isLiveMode ? 'live' : 'test'));
        $status = 'danger';
        $title = '';

        if (is_null($time)) {
            $description = __('Never received event');
            if ($isLiveMode) {
                $title = __('Live webhook is not set up');
            } else {
                $title = __('Test webhook is not set up');
            }
        } else {
            $time = Carbon::createFromTimestamp($time);
            $description = 'Last event ' . formatted_time($time);

            if ($time->diffInDays() < 7) {
                $status = 'success';
                if ($isLiveMode) {
                    $title = __('Live webhook works properly');
                } else {
                    $title = __('Test webhook works properly');
                }
            } else {
                $status = 'warning';
                if ($isLiveMode) {
                    $title = __('Live webhook may not work properly');
                } else {
                    $title = __('Test webhook may not work properly');
                }
            }
        }
        return [$title, $description, $status];
    }


    /**
     * @param $id
     * @return \Stripe\Customer|null
     */
    public function getCustomer($id)
    {
        $plan = null;
        if (!$this->client) {
            alert_message("Stripe api keys not set properly");
            return $plan;
        }
        try {
            $customer = $this->client->customers->retrieve($id);
        } catch (ApiErrorException $e) {
            alert_message("Get customer error<br>" . $e->getMessage());
            $customer = null;
        }

        return $customer;
    }
}
