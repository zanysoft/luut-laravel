<?php


namespace App\Helpers;


use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class NmiPaymentHelper
{

    protected $public_key = '';

    protected $security_key = '';

    protected $test_mode = '';

    protected $payment_token = null;

    protected $endpoint = 'https://secure.nmi.com/api/transact.php';
    protected $query_endpoint = 'https://secure.nmi.com/api/query.php';

    public $cacheExpiration;

    public $client = null;

    protected $order = [
        'orderid' => '',
        'order_description' => '',
        'tax' => '',
        'shipping' => '',
        'ponumber' => '',
        'ipaddress' => ''
    ];

    protected $shipping = [
        'first_name' => '',
        'last_name' => '',
        'company' => '',
        'address1' => '',
        'address2' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => '',
        'email' => '',
    ];

    protected $billing = [
        'first_name' => '',
        'last_name' => '',
        'company' => '',
        'address1' => '',
        'address2' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => '',
        'phone' => '',
        'fax' => '',
        'email' => '',
        'website' => '',
    ];

    protected $card_details = [
        "ccnumber" => '',
        "ccexp" => '',
        "cvv" => ''
    ];

    protected $cvv_respons_codes = [
        'M' => 'CVV2/CVC2 match',
        'N' => 'CVV2/CVC2 no match',
        'P' => 'Not processed',
        'S' => 'Merchant has indicated that CVV2/CVC2 is not present on card',
        'U' => 'Issuer is not certified and/or has not provided Visa encryption keys',
    ];

    protected $respons_codes = [
        '100' => 'Transaction was approved.',
        '200' => 'Transaction was declined by processor.',
        '201' => 'Do not honor.',
        '202' => 'Insufficient funds.',
        '203' => 'Over limit.',
        '204' => 'Transaction not allowed.',
        '220' => 'Incorrect payment information.',
        '221' => 'No such card issuer.',
        '222' => 'No card number on file with issuer.',
        '223' => 'Expired card.',
        '224' => 'Invalid expiration date.',
        '225' => 'Invalid card security code.',
        '226' => 'Invalid PIN.',
        '240' => 'Call issuer for further information.',
        '250' => 'Pick up card.',
        '251' => 'Lost card.',
        '252' => 'Stolen card.',
        '253' => 'Fraudulent card.',
        '260' => 'Declined with further instructions available. (See response text)',
        '261' => 'Declined-Stop all recurring payments.',
        '262' => 'Declined-Stop this recurring program.',
        '263' => 'Declined-Update cardholder data available.',
        '264' => 'Declined-Retry in a few days.',
        '300' => 'Transaction was rejected by gateway.',
        '400' => 'Transaction error returned by processor.',
        '410' => 'Invalid merchant configuration.',
        '411' => 'Merchant account is inactive.',
        '420' => 'Communication error.',
        '421' => 'Communication error with issuer.',
        '430' => 'Duplicate transaction at processor.',
        '440' => 'Processor format error.',
        '441' => 'Invalid transaction information.',
        '460' => 'Processor feature not available.',
        '461' => 'Unsupported card type.',
    ];

    protected $avs_response_codes = [
        'X' => 'Exact match, 9-character numeric ZIP',
        'Y' => 'Exact match, 5-character numeric ZIP',
        'D' => 'Exact match, 5-character numeric ZIP',
        'M' => 'Exact match, 5-character numeric ZIP',
        '2' => 'Exact match, 5-character numeric ZIP, customer name',
        '6' => 'Exact match, 5-character numeric ZIP, customer name',
        'A' => 'Address match only',
        '3' => 'Address, customer name match only',
        '7' => 'Address, customer name match only',
        'W' => '9-character numeric ZIP match only',
        'Z' => '5-character ZIP match only',
        'P' => '5-character ZIP match only',
        'L' => '5-character ZIP match only',
        '1' => '5-character ZIP, customer name match only',
        '5' => '5-character ZIP, customer name match only',
        'N' => 'No address or ZIP match only',
        'C' => 'No address or ZIP match only',
        '4' => 'No address or ZIP or customer name match only',
        '8' => 'No address or ZIP or customer name match only',
        'U' => 'Address unavailable',
        'G' => 'Non-U.S. issuer does not participate',
        'I' => 'Non-U.S. issuer does not participate',
        'R' => 'Issuer system unavailable',
        'E' => 'Not a mail/phone order',
        'S' => 'Service not supported',
        '0' => 'AVS not available',
        'O' => 'AVS not available',
        'B' => 'AVS not available',
    ];

    public function __construct($config = [])
    {
        if (isset($config['test_mode'])) {
            if ($config['test_mode'] === true || $config['test_mode'] == 'enabled') {
                $this->test_mode = 'enabled';
            }
        } else {
            $this->test_mode = settings('payment.nmi_mode') == 'test' ? 'enabled' : '';
        }

        $this->payment_token = $config['payment_token'] ?? null;

        $this->public_key = $config['public_key'] ?? $config['tokenization_key'] ?? settings('payment.nmi_tokenization_key');

        $this->security_key = $config['security_key'] ?? settings('payment.nmi_security_key');

        $client_config = [
            'connect_timeout' => ($config['connect_timeout'] ?? 300),
            'allow_redirects' => (isset($config['allow_redirects']) ? $config['allow_redirects'] : true),
            'http_errors' => ($config['http_errors'] ?? false),
            'verify' => ($config['verify'] ?? false),
        ];

        if ($this->security_key) {
            $client_config['security_key'] = $this->security_key;
        }

        $client = new \GuzzleHttp\Client($client_config);

        $this->client = $client;

        $this->cacheExpiration = settings('others.cache_expiration', (60 * 24)) * 60;
    }

    /**
     * @param $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint): NmiPaymentHelper
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @param $payment_token
     * @return $this
     */
    public function setPaymentToken($payment_token): NmiPaymentHelper
    {
        $this->payment_token = $payment_token;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityKey(): string
    {
        return $this->security_key;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setSecurityKey($key): NmiPaymentHelper
    {
        $this->security_key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->public_key;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setPublicKey($key): NmiPaymentHelper
    {
        $this->public_key = $key;

        return $this;
    }

    /**
     * @param $mode
     * @return $this
     */
    public function setTestMode($mode = true): NmiPaymentHelper
    {
        $this->test_mode = $mode == true || $mode == 'enabled' ? 'enabled' : '';

        return $this;
    }

    /**
     * @param $order_id
     * @param $description
     * @param float|int $tax
     * @param float|int $shipping
     * @param string $ponumber
     * @return $this
     */
    function setOrder($order_id, $description, $tax, $shipping = 0, string $ponumber = ''): NmiPaymentHelper
    {

        $this->order = [
            'orderid' => $order_id,
            'order_description' => $description,
            'tax' => $tax,
            'shipping' => $shipping,
            'ponumber' => $ponumber,
            'ipaddress' => request()->ip()
        ];

        return $this;
    }

    /**
     * @param $number
     * @param $exp
     * @param $cvv
     * @return $this
     */
    public function setCard($number, $exp, $cvv)
    {
        $this->card_details = [
            "ccnumber" => $number,
            "ccexp" => $exp,
            "cvv" => $cvv
        ];

        return $this;
    }

    /**
     * @param array $address
     * @return $this
     */
    public function setBilling(array $address, $same_shipping_address = false): NmiPaymentHelper
    {
        if (isset($address['address'])) {
            $this->billing['address1'] = $address['address'];
        }

        if (isset($address['firstname'])) {
            $this->billing['first_name'] = $address['firstname'];
        }

        if (isset($address['lastname'])) {
            $this->billing['last_name'] = $address['lastname'];
        }

        if (isset($address['zipcode'])) {
            $this->billing['zip'] = $address['zipcode'];
        }

        foreach ($this->billing as $key => $val) {
            if (isset($address[$key])) {
                $this->billing[$key] = $address[$key];
            }
        }

        if ($same_shipping_address) {
            foreach ($this->shipping as $key => $val) {
                if (isset($this->billing[$key])) {
                    $this->shipping[$key] = $this->billing[$key];
                }
            }
        }

        return $this;
    }

    /**
     * @param array $address
     * @return $this
     */
    function setShipping(array $address): NmiPaymentHelper
    {
        if (isset($address['address'])) {
            $this->shipping['address1'] = $address['address'];
        }

        if (isset($address['firstname'])) {
            $this->shipping['first_name'] = $address['firstname'];
        }

        if (isset($address['lastname'])) {
            $this->shipping['last_name'] = $address['lastname'];
        }


        foreach ($this->shipping as $key => $val) {
            if (isset($address[$key])) {
                $this->shipping[$key] = $address[$key];
            }
        }
        return $this;
    }

    /**
     * Create Invoice
     * @param float|int $amount
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @TODO not tested yet.
     */
    public function addInvoice($amount, array $items, array $options = []): array
    {
        $params = array_merge([
            'invoicing' => 'add_invoice',
            'amount' => $this->numberToFloat($amount),
            'payment_terms' => 'upon_receipt',
            'currency' => 'USD',
        ], $this->getParameters());


        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        if (empty($items)) {
            $items = $options['items'] ?? [];
        }

        if (!empty($items)) {
            $params = array_merge($params, $this->parseItems($items));
        }

        return $this->request($params);
    }

    /**
     * Create Sale
     * @param float|int $amount
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addSale($amount, array $items, array $options = []): array
    {
        $params = array_merge([
            'type' => 'sale',
            'currency' => 'USD',
            'amount' => $this->numberToFloat($amount),
        ], $this->getParameters());

        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        if (empty($items)) {
            $items = $options['items'] ?? [];
        }

        if (!empty($items)) {
            $params = array_merge($params, $this->parseItems($items));
        }

        return $this->request($params);
    }

    /**
     * @param array $items
     * @return array
     */
    protected function parseItems(array $items)
    {
        $params = [];
        $index = 1;
        foreach ($items as $item) {
            if (is_array($item)) {
                $_items = [];
                foreach ($item as $key => $val) {

                    $key = trim(preg_replace('/[^a-z_]+/', '', strtolower($key)), '_ ');

                    if (Str::startsWith($key, 'item_')) {
                        $key = substr($key, strlen('item_'));
                    }

                    if (in_array($key, ['product_code', 'sku', 'id'])) {
                        $_items['item_product_code_' . $index] = $val;
                    }

                    if (in_array($key, ['description', 'desc'])) {
                        $_items['item_description_' . $index] = $val;
                    }

                    if (in_array($key, ['quantity', 'qty'])) {
                        $_items['item_quantity_' . $index] = (int)$val;
                    }

                    if (in_array($key, ['unit_cost', 'unit_price', 'amount', 'price'])) {
                        $_items['item_unit_cost_' . $index] = $this->numberToFloat($val);
                    }

                    if (in_array($key, ['total_amount', 'total_price', 'total'])) {
                        $_items['item_total_amount_' . $index] = $this->numberToFloat($val);
                    }

                    if (in_array($key, ['tax_amount', 'tax'])) {
                        $_items['item_tax_amount_' . $index] = $this->numberToFloat($val);
                    }

                    if (in_array($key, ['tax_rate'])) {
                        $_items['item_tax_rate_' . $index] = $this->numberToFloat($val);
                    }

                    if (in_array($key, ['tax_type'])) {
                        $_items['item_tax_type_' . $index] = $val;
                    }

                    if (in_array($key, ['discount_amount', 'discount_price', 'discount']) && !Str::contains($val, '%')) {
                        $_items['item_discount_amount_' . $index] = $this->numberToFloat($val);
                    }

                    if (in_array($key, ['discount_rate', 'discount']) && Str::contains($val, '%')) {
                        $_items['item_tax_rate_' . $index] = $val;
                    }

                    if (in_array($key, ['alternate_tax_id', 'alternate_tax', 'tax_id'])) {
                        $_items['item_alternate_tax_id_' . $index] = $val;
                    }
                }

                if (!isset($_items['item_quantity_' . $index])) {
                    $_items['item_quantity_' . $index] = 1;
                }

                $qty = $_items['item_quantity_' . $index] ?? 1;
                $unit_cost = $_items['item_unit_cost_' . $index] ?? 0;
                $total_amount = $_items['item_total_amount_' . $index] ?? 0;

                if (!$total_amount && $unit_cost) {
                    $total_amount = $unit_cost * $qty;
                }

                if ($total_amount) {
                    if (!$unit_cost) {
                        $_items['item_unit_cost_' . $index] = $total_amount / $qty;
                    }
                    $params = array_merge($params, $_items);
                }

            }
            $index++;
        }

        return $params;
    }

    /**
     * @param $transaction_id
     * @param array $options
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSale($transaction_id, array $options = [])
    {
        $params = [
            'action_type' => 'sale',
            'transaction_id' => $transaction_id,
        ];

        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        $result = $this->query_request($params);

        if ($result['status_code'] == 200) {
            $transaction = $result['transaction'] ?? [];
            $transaction = array_map(function ($a) {
                if (is_array($a)) {
                    if (empty($a)) {
                        $a = '';
                    } else {
                        foreach (array_keys($a) as $key) {
                            if (is_array($a[$key]) && empty($a[$key])) {
                                $a[$key] = '';
                            }
                        }
                    }
                }
                return $a;
            }, $transaction);

            return $transaction;
        } else {
            return null;
        }
    }


    /**
     * @param float|int $amount
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doAuth($amount, array $options = []): array
    {
        $params = array_merge([
            'type' => 'auth',
            'amount' => $this->numberToFloat($amount),
        ], $this->getParameters());


        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        return $this->request($params);
    }

    /**
     * @param float|int $amount
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doCredit($amount, array $options = []): array
    {
        $params = array_merge([
            'type' => 'credit',
            'amount' => $this->numberToFloat($amount),
        ], $this->getParameters());

        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        return $this->request($params);
    }

    /**
     * @param $authorizationcode
     * @param float|int $amount
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doOffline($authorizationcode, $amount, array $options = [])
    {
        $params = array_merge([
            'type' => 'offline',
            'authorizationcode' => $authorizationcode,
            'amount' => $this->numberToFloat($amount),
        ], $this->getParameters());

        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        return $this->request($params);
    }

    /**
     * @param $transactionid
     * @param float|int $amount
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doCapture($transactionid, float $amount = 0)
    {

        $params = [
            'type' => 'capture',
            'transactionid' => $transactionid,
        ];

        if ($amount > 0) {
            $params['amount'] = $this->numberToFloat($amount);
        }

        return $this->request($params);
    }

    /**
     * @param $transactionid
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doVoid($transactionid)
    {
        $params = [
            'type' => 'void',
            'transactionid' => $transactionid,
        ];

        return $this->request($params);
    }

    /**
     * @param $transactionid
     * @param $amount
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doRefund($transactionid, $amount = 0)
    {
        $params = [
            'type' => 'refund',
            'transactionid' => $transactionid,
        ];

        if ($amount > 0) {
            $params['amount'] = $this->numberToFloat($amount);
        }

        return $this->request($params);
    }

    /**
     *  Recurring Plans & Subscription
     */

    public function getPlan($plan_id)
    {
        $plans = $this->getPlans();

        return Arr::first(Arr::where($plans, function ($a) use ($plan_id) {
            return $a['plan_id'] == $plan_id;
        }));
    }

    public function getPlans()
    {
        $params = [
            'report_type' => 'recurring_plans',
            'result_limit' => 1000,
            'page_number' => 0,
            'result_order' => 'reverse', // standard | reverse
        ];

        $plans = Cache::remember('nmi.plans.' . settings('payment.nmi_mode', 'mode'), $this->cacheExpiration, function () use ($params) {
            $result = $this->query_request($params);
            $_plans = [];
            if ($result['status_code'] == 200) {
                $plans = $result['plan'] ?? [];
                $plans = array_map(function ($a) {
                    foreach (array_keys($a) as $key) {
                        if (is_array($a[$key]) && empty($a[$key])) {
                            $a[$key] = '';
                        }
                    }
                    return $a;
                }, $plans);

                foreach ($plans as &$plan) {
                    $_plan_id = $plan['plan_id'];
                    $_day_frequency = $plan['day_frequency'];
                    $_month_frequency = $plan['month_frequency'];
                    $_day_of_month = $plan['day_of_month'];
                    $_plan_payments = $plan['plan_payments'] > 0 ? $plan['plan_payments'] : 0;

                    $plan['recurring'] = (!$_plan_payments || $_plan_payments > 1) ? 1 : 0;
                    $plan['interval'] = $_day_frequency > 0 ? 'day' : ($_month_frequency > 0 ? 'month' : '');
                    $plan['interval_count'] = $_day_frequency ?: $_month_frequency;
                    $plan['duration'] = $_plan_payments;
                    $plan['currency'] = 'USD';

                    $_plans[$_plan_id] = $plan;
                }
                return $_plans;
            } else {
                Cache::forget('nmi.plans');
                return null;
            }
        });
        return $plans;
    }

    /**
     * Add New Recurring Plans
     * @param $plan_name
     * @param float|int $amount
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addPlan($plan_name, $amount, array $options = []): array
    {
        $plan_id = $options['plan_id'] ?? $options['id'] ?? $plan_name;
        $day_of_month = (int)($options['day_of_month'] ?? 1);
        $month_frequency = (int)($options['month_frequency'] ?? 1);
        $plan_payments = (int)($options['plan_payments'] ?? 0);

        $params = array_merge([
            'recurring' => 'add_plan',
            'plan_payments' => $plan_payments,
            'plan_name' => trim($plan_name),
            'plan_id' => strtoupper(Str::slug(trim($plan_id))),
            'month_frequency' => $month_frequency,
            'day_of_month' => $day_of_month,
            'plan_amount' => $this->numberToFloat($amount),
        ]);

        if (isset($options['day_frequency'])) {
            $params['day_frequency'] = $options['day_frequency'];
            unset($params['month_frequency']);
            unset($params['day_of_month']);
        }

        if (isset($options['month_frequency'])) {
            $params['month_frequency'] = $options['month_frequency'];
            unset($params['day_frequency']);
        }

        return $this->request($params);
    }

    /**
     * Edit Existing Recurring Plan
     * @param $current_plan_id
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function editPlan($current_plan_id, array $options = []): array
    {
        $plan_name = trim($options['plan_name'] ?? $options['name'] ?? '');
        $plan_id = trim($options['plan_id'] ?? $options['id'] ?? '');
        $day_of_month = $options['day_of_month'] ?? '';
        $month_frequency = $options['month_frequency'] ?? '';
        $day_frequency = $options['day_frequency'] ?? '';
        $plan_amount = $options['plan_amount'] ?? $options['amount'] ?? '';

        $params = array_merge([
            'recurring' => 'edit_plan',
            'current_plan_id' => $current_plan_id,
        ]);

        if ($plan_name) {
            $params['plan_name'] = $plan_name;
        }
        if ($plan_amount) {
            $params['plan_amount'] = $this->numberToFloat($plan_amount);
        }
        if ($plan_id) {
            $params['plan_id'] = strtoupper(Str::slug($plan_id));
        }

        if (isset($options['plan_payments']) && is_numeric($options['plan_payments'])) {
            $params['plan_payments'] = $options['plan_payments'];
        }

        if ($day_of_month && is_numeric($day_of_month)) {
            $params['day_of_month'] = $day_of_month;
        }

        if ($month_frequency && is_numeric($month_frequency)) {
            $params['month_frequency'] = $month_frequency;
        }

        if ($day_frequency && is_numeric($day_frequency)) {
            $params['day_frequency'] = $day_frequency;
        }

        return $this->request($params);
    }

    /**
     * Add New Recurring Plans
     * @param $plan_name
     * @param float|int $amount
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addSubscription($plan_id, array $options = []): array
    {
        $params = array_merge([
            'recurring' => 'add_subscription',
            'plan_id' => $plan_id,
            'customer_receipt' => true,
        ]);

        if (isset($options['start_date']) && $options['start_date']) {
            $params['start_date'] = date('Ymd', strtotime($options['start_date']));
        }


        $params = array_merge($params, $this->getCardDetails());

        $params = array_merge($params, $this->getBillingDetails());

        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        //dd($params);

        return $this->request($params);
    }

    /**
     * @param $amount
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addCustomSubscription($amount, array $options = []): array
    {

        $day_of_month = (int)($options['day_of_month'] ?? date('j', strtotime($options['start_date'] ?? 'now')));
        $month_frequency = (int)($options['month_frequency'] ?? 1);
        $plan_payments = (int)($options['plan_payments'] ?? 0);

        $params = array_merge([
            'recurring' => 'add_subscription',
            'plan_payments' => $plan_payments,
            'plan_amount' => $this->numberToFloat($amount),
            'month_frequency' => $month_frequency,
            'day_of_month' => $day_of_month,
        ]);

        if (isset($options['start_date']) && $options['start_date']) {
            $params['start_date'] = date('Ymd', strtotime($options['start_date']));
        }

        $params = array_merge($params, $this->getCardDetails());

        $params = array_merge($params, $this->getBillingDetails());

        if (!empty($options)) {
            $params = array_merge($params, $options);
        }

        return $this->request($params);
    }


    /**
     * @param $with_card
     * @return array
     */
    protected function getParameters($card_info = true)
    {
        $params = [];

        if ($card_info) {
            $params = array_merge($params, $this->getCardDetails());
        }

        $params = array_merge(
            $params,
            $this->getOrderDetails(),
            $this->getBillingDetails(),
            $this->getSippingDetails()
        );

        return $params;
    }

    /**
     * @return array
     */
    protected function getCardDetails(): array
    {
        if ($this->payment_token) {
            return ['payment_token' => $this->payment_token];
        } else {
            return $this->card_details;
        }
    }

    protected function getOrderDetails()
    {
        $order = array_filter($this->order);
        if (!empty($order)) {
            if ($order['tax']) {
                $order['tax'] = $this->numberToFloat($order['tax']);
            }
            if ($order['shipping']) {
                $order['shipping'] = $this->numberToFloat($order['shipping']);
            }
        }
        return $order ?? [];
    }

    /**
     * @return array|string[]
     */
    protected function getBillingDetails(): array
    {
        $billing_info = array_filter($this->billing);

        return $billing_info ?? [];
    }

    /**
     * @return array|string[]
     */
    protected function getSippingDetails(): array
    {
        $shipping_info = array_filter($this->shipping);

        return $shipping_info ?? [];
    }

    /**
     * @param array $params
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request(array $params)
    {
        $method = 'POST';

        $params['security_key'] = $this->security_key;

        if ($this->payment_token) {
            $params['payment_token'] = $this->payment_token;
        }

        if ($this->test_mode) {
            $params['test_mode'] = 'enabled';
        }

        if (isset($params['amount'])) {
            $params['amount'] = $this->numberToFloat($params['amount']);
        }

        if (isset($params['surcharge'])) {
            $params['surcharge'] = $this->numberToFloat($params['surcharge']);
        }

        // Call the correct method with parameters
        if (!empty($params)) {
            if (strtoupper($method) == 'POST') {
                $options['multipart'] = [];
                foreach (self::to_1_level_array($params) as $name => $value) {
                    $options['multipart'][] = ['name' => $name, 'contents' => $value];
                }
            } else {
                $options['query'] = $params;
            }
        }

        $response = $this->client->post($this->endpoint, $options);

        $contentType = $response->hasHeader('Content-Type') ? current($response->getHeader('Content-Type')) : '';

        return $this->formatResponse($response, $contentType);
    }

    protected function numberToFloat($number)
    {
        return number_format($number, 2, ".", "");
    }

    /**
     * @param array $params
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function query_request(array $params)
    {
        $method = 'post';

        $params['security_key'] = $this->security_key;

        if ($this->test_mode) {
            $params['test_mode'] = 'enabled';
        }

        // Call the correct method with parameters
        if (!empty($params)) {
            if (strtoupper($method) == 'POST') {
                $options['multipart'] = [];
                foreach (self::to_1_level_array($params) as $name => $value) {
                    $options['multipart'][] = ['name' => $name, 'contents' => $value];
                }
            } else {
                $options['query'] = $params;
            }
        }

        $response = $this->client->{$method}($this->query_endpoint, $options);

        $contentType = $response->hasHeader('Content-Type') ? current($response->getHeader('Content-Type')) : '';

        return $this->queryResponse($response, $contentType);
    }

    /**
     * @param ResponseInterface $response
     * @param $contentType
     * @return array
     */
    protected function queryResponse(ResponseInterface $response, $contentType)
    {
        $http_body = (string)$response->getBody();
        $status_code = $response->getStatusCode();

        if (Str::contains($contentType, ';')) {
            $contentType = array_first(explode(';', $contentType));
        }

        if ($status_code == 200) {
            $response_data = [
                'status_code' => $status_code,
                'test_mode' => $this->test_mode,
            ];

            $auto_detect_formats = array(
                'application/xml' => 'xml',
                'text/xml' => 'xml',
                'application/json' => 'json',
                'text/json' => 'json',
                'application/vnd.php.serialized' => 'serialize'
            );

            $format = $auto_detect_formats[$contentType] ?? 'default';

            switch ($format) {
                case 'json':
                    $data = json_decode(trim($http_body));
                    if (json_last_error() > 0) { // if response is a string
                        $data = $http_body;
                    }
                    break;
                case 'xml':
                    $xml = simplexml_load_string($http_body, 'SimpleXMLElement', LIBXML_NOCDATA);
                    $data = $xml ? @json_decode(@json_encode($xml), true) : [];
                    break;
                case 'serialize':
                    $data = unserialize(trim($http_body));
                    break;
                default:
                    $http_body_data = explode("&", $http_body);
                    $data = [];
                    for ($i = 0; $i < count($http_body_data); $i++) {
                        $rdata = explode("=", $http_body_data[$i]);
                        $data[$rdata[0]] = $rdata[1];
                    }
            }

            $response_data = array_merge($response_data, $data);
        } else {
            $response_data = [
                'status_code' => $status_code,
                'responsetext' => $response->getReasonPhrase(),
                'body' => trim($http_body),
            ];
        }

        return $response_data;
    }

    /**
     * @param ResponseInterface $response
     * @param $contentType
     * @return array
     */
    protected function formatResponse(ResponseInterface $response, $contentType)
    {
        $http_body = (string)$response->getBody();
        $status_code = $response->getStatusCode();

        if ($status_code == 200) {
            $response_data = [
                'status_code' => $status_code,
                'response' => '',
                'response_body' => trim($http_body),
                'response_code' => '',
                'responsetext' => '',
                'test_mode' => $this->test_mode,
                'avsresponse' => '',
                'cvvresponse' => '',
                'error' => null,
                'type' => '',
                'authcode' => null,
                'transactionid' => null,
                'orderid' => null,
            ];
            $auto_detect_formats = array(
                'application/xml' => 'xml',
                'text/xml' => 'xml',
                'application/json' => 'json',
                'text/json' => 'json',
                'application/vnd.php.serialized' => 'serialize'
            );

            $format = $auto_detect_formats[$contentType] ?? 'default';

            switch ($format) {
                case 'json':
                    $data = json_decode(trim($http_body));
                    if (json_last_error() > 0) { // if response is a string
                        $data = $http_body;
                    }
                    break;
                case 'xml':
                    $xml = simplexml_load_string($http_body, 'SimpleXMLElement', LIBXML_NOCDATA);
                    $data = $xml ? @json_decode(@json_encode($xml), true) : [];
                    break;
                case 'serialize':
                    $data = unserialize(trim($http_body));
                    break;
                default:
                    $http_body_data = explode("&", $http_body);
                    $data = [];
                    for ($i = 0; $i < count($http_body_data); $i++) {
                        $rdata = explode("=", $http_body_data[$i]);
                        $data[$rdata[0]] = $rdata[1];
                    }
            }

            $_response = $data['response'] ?? 0;
            $_response_code = $data['response_code'] ?? '';
            $_responsetext = $data['responsetext'] ?? '';
            $_avsresponse = $data['avsresponse'] ?? null;
            $_cvvresponse = $data['cvvresponse'] ?? null;

            if ($_responsetext) {
                list($_responsetext) = explode('REFID', $_responsetext);
                if (Str::contains($_responsetext, 'Card expiration should be in')) {
                    $_responsetext = 'Card expiration should be in the format MMYY, MM/YY or MM-YY.';
                }
                $data['responsetext'] = trim($_responsetext);
            }

            if ($_response != 1 || $_response_code != 100) {

                if ($_response == 3 && trim($_responsetext)) {
                    $response_data['error'][] = trim($_responsetext);
                } else if ($_response_code != 100 && array_key_exists($_response_code, $this->respons_codes)) {
                    $response_data['error'][] = str_replace('See response text', trim($_responsetext), $this->respons_codes[$_response_code]);
                }

                if ($_cvvresponse != 'M' && array_key_exists($_cvvresponse, $this->cvv_respons_codes)) {
                    $response_data['error'][] = $this->cvv_respons_codes[$_cvvresponse];
                }

                if ($_avsresponse && array_key_exists($_avsresponse, $this->avs_response_codes)) {
                    $response_data['error'][] = $this->avs_response_codes[$_avsresponse];
                }
            }

            $response_data = array_merge($response_data, $data);
        } else {
            $response_data = [
                'status_code' => $status_code,
                'responsetext' => $response->getReasonPhrase(),
                'body' => trim($http_body),
            ];
        }

        /*try {
            sendMail('mubashar.qrg@gmail.com', "NMI Response Data", json_encode($response_data));
        } catch (\Exception $e) {
        }*/

        return $response_data;
    }

    protected function to_1_level_array($array, $prefix = null)
    {
        $return = array();

        foreach ($array as $key => $value) {
            $name = $prefix ? "{$prefix}[{$key}]" : $key;

            if (is_array($value) || is_object($value)) {
                $return += $this->to_1_level_array($value, $name);
            } else {
                $return[$name] = $value;
            }
        }

        return $return;
    }

    public static function getLastWebHookEventStatus($isLiveMode)
    {
        $time = Setting::getOption('last_nmi_webhook_' . ($isLiveMode ? 'live' : 'test'));
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

}
