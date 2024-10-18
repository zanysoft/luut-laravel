<?php
try {


    $_messages = Alert::getMessages();

    $_errors = request()->session()->get('errors');
    if (isset($_errors) && $_errors->any()) {
        foreach ($_errors->all() as $error) {
            $_messages['error'][] = $error;
        }
    }
    Alert::flush();
} catch (\Exception $e) {
}
?>
@if(count($_messages))
    <script>
        $('document').ready(function () {
            toastr_options = {
                "closeButton": true,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "extendedTimeOut": 0,
                "timeOut": 10000
            };
            var msgs = {!! json_encode($_messages) !!};
            $.each(msgs, function (type, messages) {
                var alerter = toastr[type];
                if (alerter) {
                    $.each(messages, function (i, m) {
                        alerter(m, type.ucfirst(), toastr_options);
                    });
                } else {
                    toastr.error("toastr alert-type " + type + " is unknown", '', toastr_options);
                }
            });
        });
    </script>
@endif
