@extends('admin.layouts.app')
@section('breadcrumb')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>{{ __('Packages') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('/')}}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin.packages.index')}}">{{ __('Packages') }}</a></li>
                <li class="breadcrumb-item active">{{$title}}</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <?php
    $package = isset($package) ? $package : [];
    $action = empty($package) ? route('admin.packages.store') : route('admin.packages.update', $package->id);
    $package_type = old('package_type', data_get($package, 'package_type'));

    if (empty($plans)) {
        $package_type = 'payment';
    }
    $is_subscription = $package_type == 'subscription';
    $is_onetime = $package_type == 'payment';

    $payment_method = data_get($package, 'payment_method');

    $default_payment_method = settings('payment.default_method');

    if (!$is_subscription) {
        $payment_method = $default_payment_method;
    }
    ?>
    <form action="{{ $action }}" id="package-form" enctype="multipart/form-data" method="post" autocomplete="off" role="presentation">
        @method(empty($package) ? 'POST' : 'PUT')
        @csrf
        <div class="card">
            <div class="card-header">{{ $title }}
                <div class="float-right">
                    <a href="{{ route('admin.packages.index') }}"><i class="fa fa-angle-double-left"></i> {{ __("Back to list")}}</a>
                </div>
            </div>
            <div class="card-body">
                @if($is_subscription && $payment_method && $payment_method != $default_payment_method)
                    <div class="alert alert-danger">
                        You have changed the payment method so you need to set payment plans according to new payment method.
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="mr-2">{{ __('Package Type')}}: </label>
                            <div class="form-check-inl form-check-inline">
                                <input type="radio" class="form-check-input" required id="package_type1" name="package_type" value="payment" {{ $package_type == 'payment' ? ' checked': ''}}>
                                <label class="form-check-label" for="package_type1">{{ __('One-Time Payment') }}</label>
                            </div>
                            @if(!empty($plans))
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" required id="package_type2" name="package_type" value="subscription" {{ $package_type == 'subscription' ? ' checked': ''}}>
                                    <label class="form-check-label" for="package_type2">{{ __('Subscription') }}</label>
                                </div>
                            @endif
                            <div></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('Title') }} <span class="text-red">*</span></label>
                            <input type="text" name="title" id="title" required value="{{ old('title',data_get($package,'title')) }}"
                                   class="form-control" data-value="{{ old('title',data_get($package,'title')) }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('Description')}}</label>
                            <textarea class="form-control" name="description" id="description"
                                      data-value="{{ old('description',data_get($package,'description')) }}">{{ old('description',data_get($package,'description')) }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <input type="hidden" name="payment_method" id="payment_method" value="{{ $payment_method?:$default_payment_method }}">
                        <div class="subscription plan-field" style="display: {{$is_subscription ? 'block' : 'none'}}">
                            @if(!empty($plans))
                                @if($default_payment_method == 'stripe')
                                    <div class="form-group">
                                        <label>{{ __('Stripe Plans')}} <span class="text-red">*</span></label>
                                        <select class="form-control select2 plans" required name="payment_plan" {{ $is_subscription ? '' : 'disabled'}}>
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
                                                        data-name="{{ $plan->name }}"
                                                        data-desc="{{ $_desc }}"
                                                    {{old('stripe_plan',data_get($package,'stripe_plan')) == $plan['id'] ? 'selected' :'' }}>{{ $_label }}</option>
                                            @endforeach
                                        </select>
                                        <div class="help-block">Clear cache if plans not updated properly.</div>
                                    </div>
                                @elseif(\Illuminate\Support\Str::startsWith($default_payment_method,'nmi'))
                                    <div class="form-group">
                                        <label>{{ settings('payment.nmi_title')?:'NMI'  }} {{ __('Plans')}} <span class="text-red">*</span></label>
                                        <select class="form-control select2 plans" required name="payment_plan" {{ $is_subscription ? '' : 'disabled'}}>
                                            <option value="">{{ __('Select Plan') }}</option>
                                        <!--<option value="custom">{{ __('Custom Plan') }}</option>-->
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
                                                        data-name="{{ $_plan_name }}"
                                                    {{old('payment_plan',data_get($package,'payment_plan')) == $_plan_id ? 'selected' :'' }}>{{ $_label }}</option>

                                            @endforeach
                                        </select>
                                        <div class="help-block">Clear cache if plans not updated properly.</div>
                                    </div>
                                @else
                                    <p>For displaying payment plans please set default payment method from <a href="{{ route('admin.settings.edit','payment') }}">Payments</a> settings.</p>
                                @endif
                                <input type="hidden" name="payment_product" id="payment_product" value="{{ old('payment_product',data_get($package,'payment_product')) }}">
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Amount')}}</label>
                                    <input class="form-control" value="{{ old('amount',data_get($package,'amount')) }}"
                                           name="amount" id="amount" type="number" {{ $is_subscription ? 'readonly' :'' }}>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('Setup Fee')}}</label>
                                    <input class="form-control" min="0" value="{{ old('setup_fee',data_get($package,'setup_fee')) }}"
                                           name="setup_fee" id="setup_fee" type="number">
                                </div>
                            </div>
                        </div>
                        <div class="subscription" style="display: {{$is_subscription ? 'block' : 'none'}}">
                            <div class="form-group">
                                <label>{{ __('End subscription')}}</label>
                                <div class="form-check-list">
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input duration" name="duration" id="duration1" value="0" {{ old('duration',data_get($package,'duration')) == 0 ? ' checked': ''}}>
                                        <label class="form-check-label" for="duration1">{{ __('When customer cancels it') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input duration" name="duration" id="duration2" value="1" {{ old('duration',data_get($package,'duration')) >0 ? ' checked': ''}}>
                                        <label class="form-check-label" for="duration2">{{ __('After certain # of occurrences') }}:
                                            <input type="number" step="1" min="1" name="duration" id="duration"
                                                   value="{{ old('duration',data_get($package,'duration'))?:1 }}"
                                                   style="max-width: 55px"
                                                {{ old('duration',data_get($package,'duration')) < 1 ? ' disabled': ''}}>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="content">Payment Note <span class="font-weight-normal">to show at top of payment collection page.</span></label>
                            <textarea class="form-control" name="payment_note" id="payment_note">{{ old('payment_note',data_get($package,'payment_note'))}}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="content">Content <span class="font-weight-normal">to show at payment collection page.</span></label>
                            <textarea class="tinymce form-control" name="content" id="content">{{ old('content',data_get($package,'content'))}}</textarea>
                        </div>
                    </div>
                </div>

                @if(settings('hubspot.enable_sync'))
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Create Onboarding Form (using Hubspot properties)')}}</label>
                                <input type="hidden" name="hubspot_fields_group" value="">
                                <select name="hubspot_fields_group[]" id="contact_groups" multiple class="form-control">
                                    <?php
                                    $selected = old('hubspot_fields_group', data_get($package, 'hubspot_fields_group', []));
                                    ?>
                                    @if(is_array($selected))
                                        @foreach($selected as $row)
                                            @if (isset($contact_groups[$row]))
                                                <option value="{{ $contact_groups[$row]['name'] }}" selected>{{ $contact_groups[$row]['displayName'] }}</option>
                                                <?php unset($contact_groups[$row]) ?>
                                            @endif
                                        @endforeach
                                    @endif
                                    @foreach($contact_groups as $contact_group)
                                        <option value="{{ $contact_group['name'] }}" {{in_array($contact_group['name'],old('hubspot_fields_group',data_get($package,'hubspot_fields_group',[]))) ? 'selected' :'' }}>{{ $contact_group['displayName'] }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-link" id="form-preview"><i class="fa fa-eye"></i> Form Preview</button>
                            </div>
                        </div>
                    </div>
                @endif
                {{--<div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Banner</label>
                            <input type="text" class="form-control filemanager" name="picture" id="picture"
                                   value="{{ old('picture',data_get($package,'picture'))}}">
                        </div>
                    </div>
                </div>--}}
            </div>
            <div class="card-footer text-right">
                <a class="btn btn-danger" href="{{ route('admin.packages.index') }}">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
                <input type="hidden" name="id" value="{{ isset($package->id) ? $package->id : '' }}">
            </div>
        </div>
    </form>
@stop

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-ui/jquery-ui.min.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/plugins/ui.multiselect/ui.multiselect.css') }}" type="text/css"/>
    <style>
        .subscription {
            display: none;
        }

        .onetime {
            display: none;
        }

        #contact_groups {
            width: 100%;
            min-height: 200px;
        }
    </style>
@endsection

@push('script')
    <script src="{{asset('assets/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{asset('assets/plugins/ui.multiselect/ui.multiselect.js')}}"></script>
    <script src="{{asset('assets/plugins/tinymce/tinymce.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $("#contact_groups").multiselect();

            bsCustomFileInput.init();
            $('#package-form').validate();

            $('input, textarea').on('keyup', function () {
                $(this).data('value', $(this).val());
            });
            $('[name="package_type"]').on('change', function () {
                var pType = $(this).val();
                if (pType == 'subscription') {
                    if ($(".plans").length) {
                        $("#amount").attr('readonly', true);
                    }
                    $(".subscription").show().find('[required]').attr('disabled', false);
                    $(".onetime").hide().find('[required]').attr('disabled', true);
                } else {
                    $(".subscription").hide().find('[required]').attr('disabled', true);
                    $(".onetime").show().find('[required]').attr('disabled', false);
                    $("#amount").attr('readonly', false);
                }
            });

            $('.duration[type="radio"]').on('change', function () {
                var es = $(this).val();
                if (es == 1) {
                    $("#duration").attr('disabled', false);
                } else {
                    $("#duration").attr('disabled', true);
                }
            });

            {{--$(document).on('change', '#payment_method', function () {
                var method = $(this).val();
                var package_type = $("input[name='package_type']:checked").val();

                console.log(method);
                if (method) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('admin.packages.load-plans') }}",
                        data: {method: method, package_type: package_type},
                        dataType: 'html',
                        success: function (data) {
                            $(".plan-field").html(data);
                        }
                    });
                } else {
                    $(".plan-field").html('<p>Please select payment method for displaying plans</p>');
                }
            });--}}
            $(document).on('change', '.plans', function () {
                var elm = $(this), tf = $("#title"), df = $("#description");
                var selected = elm.find(":selected");

                var id = elm.val();
                var duration = selected.data('duration');
                var amount = selected.data('amount');
                var desc = selected.data('desc');
                var name = selected.data('name');
                var pid = selected.data('pid');
                var interval = selected.data('interval');
                var interval_count = selected.data('interval_count');

                if (!tf.data('value')) {
                    tf.val(name);
                }

                if (!df.data('value') && desc) {
                    df.val(desc ? desc : selected.text());
                }

                if ($("#amount").length) {
                    $("#amount").val(amount)
                }

                if ($("#payment_product").length) {
                    $("#payment_product").val(pid)
                }
                if ($("#payment_interval").length) {
                    $("#payment_interval").val(interval)
                }

                if ($("#payment_interval_count").length) {
                    $("#payment_interval_count").val(interval_count)
                }

                if ($('[name="duration"]').length) {
                    if (duration > 0) {
                        $("#duration2").prop('checked', true).trigger('change');
                    } else {
                        $("#duration1").prop('checked', true).trigger('change');
                    }
                    $("#duration").val(duration > 0 ? duration : 1);
                }
            });
            tinymce.init({
                selector: 'textarea.tinymce',
                min_height: 400,
                autoresize_min_height: 400,
                autoresize_bottom_margin: 5,
                document_base_url: '{{url('/')}}/',

                powerpaste_allow_local_images: true,
                powerpaste_word_import: 'prompt',
                powerpaste_html_import: 'prompt',

                removed_menuitems: 'newdocument',
                plugins: 'filemanager responsivefilemanager autoresize advcode powerpaste searchreplace autolink preview directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount contextmenu colorpicker textpattern help',
                toolbar: [
                    'styleselect forecolor backcolor table | bold italic underline strikethrough  | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent| code removeformat preview help',
                    'undo redo | responsivefilemanager image media link ',
                ],

                relative_urls: false,
                remove_script_host: true,
                convert_urls: true,

                image_advtab: true,
                filemanager_relative_url: true,
                filemanager_title: "File Manager",
                filemanager_access_key: '@filemanager_get_key()',
                external_filemanager_path: "@external_filemanager_path()",
                external_plugins: {
                    "filemanager": "/vendor/responsivefilemanager/plugin.min.js"
                }
            });
        });
    </script>
@endpush
