@if($method == 'stripe')
    <div class="form-group">
        <label>{{ __('Stripe Plans')}} <span class="text-red">*</span></label>
        <select class="form-control select2 plans" required name="stripe_plan" {{ $package_type == 'subscription' ? '' : 'disabled'}}>
            <option value="">{{ __('Select Plan') }}</option>
            @foreach($plans as $plan)
                <?php
                //$_currency_symbol = \App\Helpers\CurrencyHelper::symbol($plan['currency']);
                $_currency_symbol = $plan->currency_symbol;
                $_amount = $plan->amount;
                $_interval = ($plan->recurring['interval_count'] > 1 ? $plan->recurring['interval_count'] : '') . ' ' . ucfirst($plan->recurring['interval']);
                $_label = $plan->name . ' - ' . $_currency_symbol . $_amount . ' / ' . $_interval;
                $_title = \Illuminate\Support\Str::limit(strip_tags($plan['description'] ?? ''), '50');
                $_desc = strip_tags($plan['description'] ?? '');
                ?>
                <option value="{{ $plan['id'] }}"
                        data-pid="{{ $plan['id'] }}"
                        data-amount="{{ $_amount }}"
                        data-interval="{{ $plan->recurring['interval']}}"
                        data-interval-count="{{ $plan->recurring['interval_count'] }}"
                        data-duration=""

                        data-unit-amount="{{ $plan->unit_amount }}"
                        data-tax-code="{{ $plan['tax_code'] }}"
                        data-livemode="{{ $plan['livemode'] }}"
                        data-title="{{ $_title }}"
                        data-desc="{{ $_desc }}"
                >{{ $_label }}</option>
            @endforeach
        </select>
        <div class="help-block">Clear cache if plans not updated properly.</div>
    </div>
@endif
@if($method == 'nmi')
    <div class="form-group">
        <label>{{ __('NMI Plans')}} <span class="text-red">*</span></label>
        <select class="form-control select2 plans" required name="payment_plan" {{ $package_type == 'subscription' ? '' : 'disabled'}} >
            <option value="">{{ __('Select Plan') }}</option>
            @foreach($plans as $plan)
                <?php
                $_currency_symbol = \App\Helpers\CurrencyHelper::symbol("USD");
                $_plan_id = $plan['plan_id'];
                $_plan_name = $plan['plan_name'];
                $_amount = $plan['plan_amount'];
                $_day_frequency = $plan['day_frequency'];
                $_month_frequency = $plan['month_frequency'];
                $_day_of_month = $plan['day_of_month'];
                $_plan_payments = $plan['plan_payments'] > 0 ? $plan['plan_payments'] : 0;

                $_interval = $_day_frequency > 0 ? $_day_frequency . ' Day(s)' : ($_month_frequency > 0 ? $_month_frequency . ' Month(s)' : '');
                $_label = $_plan_name . ' - ' . $_currency_symbol . $_amount . ' / ' . $_interval;
                $_desc = strip_tags($plan['description'] ?? '');
                ?>
                <option value="{{ $_plan_id }}"
                        data-pid="{{ $_plan_id }}"
                        data-amount="{{ $_amount }}"
                        data-interval="{{ $_day_frequency > 0 ? 'day' : ($_month_frequency >0 ? 'month':'') }}"
                        data-interval-count="{{ $_day_frequency ?: $_month_frequency }}"
                        data-duration="{{ $_plan_payments }}"
                >{{ $_label }}</option>
            @endforeach
        </select>
        <div class="help-block">Clear cache if plans not updated properly.</div>
    </div>
@endif
<input type="hidden" name="payment_product" id="payment_product" value="">
