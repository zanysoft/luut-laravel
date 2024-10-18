<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NmiPaymentHelper;
use App\Helpers\StripeHelper;
use App\Http\Requests\PackagesRequest;
use App\Models\Packages;
use App\Models\SaleInvoiceItems;
use App\Models\SaleItems;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Html\Column;

/**
 * Class PackagesController
 * @package App\Http\Controllers
 */
class PackagesController extends Controller
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            $from = $request->get('from');
            $to = $request->get('to');
            $payment_method = $request->get('payment_method');

            if ($from) {
                $from = Carbon::createFromTimestamp(strtotime($from))->startOfDay()->toDateTimeString();
            }

            if ($to) {
                $to = Carbon::createFromTimestamp(strtotime($to))->endOfDay()->toDateTimeString();
            }

            $query = Packages::select(['id', 'title', 'slug', 'amount', 'description', 'package_type', 'status', 'payment_method', 'created_at']);

            if ($from) {
                $query->where('created_at', '>=', $from);
            }

            if ($to) {
                $query->where('created_at', '<=', $to);
            }

            if ($payment_method) {
                if (Str::startsWith($payment_method, 'nmi')) {
                    $query->where('payment_method', 'LIKE', 'nmi-%');
                } elseif ($payment_method == 'payment') {
                    $query->whereNull('payment_method');
                } else {
                    $query->where('payment_method', $payment_method);
                }
            }

            return DataTables::eloquent($query)
                ->filterColumn('created_at', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(created_at,'%m-%d-%Y') like ?", ["%$keyword%"]);
                })
                ->setRowId('id')
                ->editColumn('title', function ($model) {
                    if (auth()->user()->can('package.update')) {
                        return '<a href="' . route('admin.packages.edit', $model->id) . '" >' . trim($model->title) . '</a>';
                    } else {
                        return trim($model->title);
                    }
                })
                ->editColumn('package_type', function ($model) {
                    return $model->package_type == 'subscription' ? 'Subscription' : "One Time";
                })
                ->editColumn('amount', function ($model) {
                    return $model->amount ? '$' . number_format($model->amount, 2) : '--';
                })
                ->editColumn('payment_method', function ($model) {
                    return $model->payment_method ? ($model->payment_method == 'stripe' ? 'Stripe' : (settings('payment.nmi_title') ?: 'NMI')) : '';
                })
                ->editColumn('status', function ($model) {
                    return $model->getActiveHtml();
                })
                ->editColumn('created_at', function ($model) {
                    return date('m-d-Y', strtotime($model->created_at));
                })
                ->addColumn('action', function ($model) {
                    return dtButtons([
                        'edit' => [
                            'url' => route('admin.packages.edit', [$model->id]),
                            'title' => 'Edit Package',
                            'can' => 'packages.edit',
                        ],
                        'delete' => [
                            'url' => route('admin.packages.destroy', [$model->id]),
                            'title' => 'Delete Package',
                            'can' => 'packages.delete',
                            //'hide' => ($model->sales->count()),
                            'data-method' => 'DELETE'
                        ]
                    ]);
                })
                ->addColumn('control', '')
                ->rawColumns(['title'], true)
                ->toJson();
        }

        $title = 'Packages List';
        $html = $builder->columns([
            Column::make('title'),
            Column::make('amount'),
            Column::make('package_type'),
            Column::make('status'),
            Column::make('action')->addClass('text-center')->orderable(false),
        ])->orderBy(1, 'ASC');

        return view('admin.packages.index', compact('html', 'title'));
    }

    /**
     * Show the form for creating a new Package.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $plans = [];
        if (settings('payment.default_method') == 'stripe') {
            $plans = Cache::remember('stripe.plans.' . settings('payment.stripe_mode', 'mode'), $this->cacheExpiration, function () {
                $stripe = new StripeHelper();
                return $stripe->getRecurringPlans();
            });
        }

        if (Str::startsWith(settings('payment.default_method'), 'nmi')) {
            $plans = Cache::remember('nmi.plans.' . settings('payment.nmi_mode', 'mode'), $this->cacheExpiration, function () {
                $nmi = new NmiPaymentHelper();
                return $nmi->getPlans();
            });
        }

        $data['title'] = 'New Package';
        $data['plans'] = $plans;

        return view('admin.packages.form', $data);
    }

    protected function addNmiPackage($name, $amount)
    {
        $sh = new NmiPaymentHelper();

        $duration = request()->get('duration', 0);

        $options = [];

        if ($duration) {
            $options['month_frequency'] = $duration;
            $options['day_frequency'] = $duration;
        }

        $result = $sh->addPlan($name, $amount, $options);

        return $result;
    }

    /**
     * @param PackagesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PackagesRequest $request)
    {
        $payment_plan = $request->get('payment_plan');

        /*$is_custom_package = $request->get('is_custom_package', 0);
        $custom_package_name = $request->get('custom_package_name', '');

        if ($is_custom_package) {
            $amount = $request->get('amount');
            $plan = $this->addNmiPackage($custom_package_name, $amount);
            $payment_plan = $plan->id;
        }*/

        //dd($request->all());

        $package = new Packages();
        $columns = $package->getFillable();
        foreach ($columns as $column) {
            if ($request->has($column) && $package->getKeyName() != $column) {
                $value = $request->get($column);
                $package->{$column} = $value;
            }
        }

        if ($request->has('setup_fee') && $request->get('setup_fee') < 1) {
            $package->setup_fee = 0;
        }


        $payment_method = $request->get('payment_method');


        if ($payment_plan && $payment_method == 'stripe') {
            $sh = new StripeHelper();
            $plan = $sh->getSubscriptionPlan($payment_plan);
            if ($plan) {
                $package->recurring = $plan->recurring == 'recurring' ? 1 : 0;
                $package->payment_interval = $plan->recurring->interval ?? $plan->interval;
                $package->payment_interval_count = $plan->recurring->interval_count ?? $plan->interval_count;
                $package->payment_currency = $plan->currency;
                $package->trial_period = $plan->recurring->trial_period_days ?? 0;
            } else {
                alert_message('Selected payment plan not exists in payment gateway.');
                $package->payment_plan = null;
            }
        } else if ($payment_plan && Str::startsWith($payment_method, 'nmi')) {
            $sh = new NmiPaymentHelper();
            $plan = $sh->getPlan($payment_plan);
            if ($plan) {
                $_day_frequency = $plan['day_frequency'];
                $_month_frequency = $plan['month_frequency'];
                $_day_of_month = $plan['day_of_month'];
                $_plan_payments = $plan['plan_payments'] > 0 ? $plan['plan_payments'] : 0;

                $package->recurring = (!$_plan_payments || $_plan_payments > 1) ? 1 : 0;
                $package->payment_interval = $_day_frequency > 0 ? 'day' : ($_month_frequency > 0 ? 'month' : '');
                $package->payment_interval_count = $_day_frequency ?: $_month_frequency;
                $package->payment_duration = $_plan_payments;
                $package->payment_currency = 'USD';
            } else {
                alert_message('Selected payment plan not exists in payment gateway.');
                $package->payment_plan = null;
            }
        } else {
            $package->recurring = 0;
            $package->payment_interval = null;
            $package->payment_interval_count = null;
            $package->payment_duration = null;
            $package->payment_currency = null;
            $package->trial_period = null;
        }

        $package->save();

        if ($package->id) {
            alert_message(__('Successfully saved'), 'success');
            return redirect()->route('admin.packages.edit', $package->id);
        } else {
            alert_message(__('Not saved'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function edit(Request $request, $id)
    {
        $package = Packages::where('id', '=', $id)->first();

        if (!$package) {
            alert_message(__('Package not found'));
            return redirect()->route('admin.packages.index');
        }

        $plans = [];
        if (settings('payment.default_method') == 'stripe') {
            $plans = Cache::remember('stripe.plans.' . settings('payment.stripe_mode', 'mode'), $this->cacheExpiration, function () {
                $stripe = new StripeHelper();
                return $stripe->getRecurringPlans();
            });
        }

        if (Str::startsWith(settings('payment.default_method'), 'nmi')) {
            $plans = Cache::remember('nmi.plans.' . settings('payment.nmi_mode', 'mode'), $this->cacheExpiration, function () {
                $nmi = new NmiPaymentHelper();
                return $nmi->getPlans();
            });
        }

        $data['title'] = 'Edit Package';
        $data['package'] = Packages::where('id', '=', $id)->first();
        $data['plans'] = $plans;

        return view('admin.packages.form', $data);
    }

    /**
     * Update the specified Package in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PackagesRequest $request)
    {
        $id = $request->id;

        $package = Packages::where('id', $id)->first();

        if (!$package) {
            alert_message(__('Package not found'));

            return redirect()->back();
        }

        $columns = $package->getFillable();
        foreach ($columns as $column) {
            if ($request->has($column) && $package->getKeyName() != $column) {
                $value = $request->get($column);
                $package->{$column} = $value;
            }
        }

        if ($request->has('setup_fee') && $request->get('setup_fee') < 1) {
            $package->setup_fee = 0;
        }

        $payment_plan = $request->get('payment_plan');
        if ($request->get('package_type') == 'subscription' && $payment_plan) {
            $sh = new StripeHelper();
            $plan = $sh->getSubscriptionPlan($payment_plan);
            if ($plan) {
                $package->recurring = $plan->recurring == 'recurring' ? 1 : 0;
                $package->payment_interval = $plan->recurring->interval ?? $plan->interval;
                $package->payment_interval_count = $plan->recurring->interval_count ?? $plan->interval_count;
                $package->payment_currency = $plan->currency;
                $package->trial_period = $plan->recurring->trial_period_days ?? 0;
            } else {
                alert_message('Selected payment plan not exists in stripe.');
                return redirect()->back();
            }
        } else {
            $package->recurring = 0;
            $package->payment_interval = null;
            $package->payment_interval_count = null;
            $package->payment_currency = null;
            $package->trial_period = null;
        }

        $payment_method = $request->get('payment_method');
        $payment_plan = $request->get('payment_plan');
        if ($payment_plan && $payment_method == 'stripe') {
            $sh = new StripeHelper();
            $plan = $sh->getSubscriptionPlan($payment_plan);
            if ($plan) {
                $package->recurring = $plan->recurring == 'recurring' ? 1 : 0;
                $package->payment_interval = $plan->recurring->interval ?? $plan->interval;
                $package->payment_interval_count = $plan->recurring->interval_count ?? $plan->interval_count;
                $package->payment_currency = $plan->currency;
                $package->trial_period = $plan->recurring->trial_period_days ?? 0;
            } else {
                alert_message('Selected payment plan not exists in payment gateway.');
                return redirect()->back();
            }
        } else if ($payment_plan && Str::startsWith($payment_method, 'nmi')) {
            $sh = new NmiPaymentHelper();
            $plan = $sh->getPlan($payment_plan);
            if ($plan) {
                $_day_frequency = $plan['day_frequency'];
                $_month_frequency = $plan['month_frequency'];
                $_day_of_month = $plan['day_of_month'];
                $_plan_payments = $plan['plan_payments'] > 0 ? $plan['plan_payments'] : 0;

                $package->recurring = (!$_plan_payments || $_plan_payments > 1) ? 1 : 0;
                $package->payment_interval = $_day_frequency > 0 ? 'day' : ($_month_frequency > 0 ? 'month' : '');
                $package->payment_interval_count = $_day_frequency ?: $_month_frequency;
                $package->payment_duration = $_plan_payments;
                $package->payment_currency = 'USD';

            } else {
                alert_message('Selected payment plan not exists in payment gateway.');
                $package->payment_plan = null;
            }
        } else {
            $package->recurring = 0;
            $package->payment_interval = null;
            $package->payment_interval_count = null;
            $package->payment_duration = null;
            $package->payment_currency = null;
            $package->trial_period = null;
        }

        $package->save();

        alert_message(__('Successfully updated'), 'success');
        return redirect()->route('admin.packages.edit', $package->id);
    }

    /**
     * Remove the specified Package from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id = null)
    {
        $id = $request->get('id', $id);

        $package = Packages::where('id', $id)->first();
        if ($package) {
            $sale_items = SaleItems::where('package_id', $package->id)->count();
            $sale_invoice_items = SaleInvoiceItems::where('package_id', $package->id)->count();

            if ($sale_items || $sale_invoice_items) {
                alert_message(__('You cannot delete this package.'), 'warning');
            } else {
                $package->delete();
                alert_message(__('Package successfully deleted'), 'success');
            }
        } else {
            alert_message(__('Package not found'));
        }

        return redirect()->route('admin.packages.index');
    }

    public function loadPlans(Request $request)
    {
        $method = $request->get('method');
        $package_type = $request->get('package_type');

        $plans = [];
        if ($method == 'stripe') {
            $plans = Cach::remember('stripe.plans.' . settings('payment.stripe_mode', 'mode'), $this->cacheExpiration, function () {
                $stripe = new StripeHelper();
                return $stripe->getRecurringPlans();
            });
        }

        if (Str::startsWith($method, 'nmi')) {
            $plans = Cache::remember('nmi.plans.' . settings('payment.nmi_mode', 'mode'), $this->cacheExpiration, function () {
                $nmi = new NmiPaymentHelper();
                return $nmi->getPlans();
            });
        }

        echo view('admin.packages.inc.plans-field', compact('plans', 'method', 'package_type'))->render();
    }
}
