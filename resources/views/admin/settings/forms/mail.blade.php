<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="exampleInputEmail1">From Email</label>
            <input type="text" name="values[from_email]" id="from_email" class="form-control"
                   value="{{ data_get($setting->values,'from_email') }}" placeholder="Enter email">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="exampleInputEmail1">Driver</label>
            <select class="form-control" name="values[driver]" id="driver">
                <option value="sendmail" {{ data_get($setting->values,'driver') == 'sendmail'? 'selected' :'' }} > Sendmail</option>
                <option value="smtp" {{ data_get($setting->values,'driver') == 'smtp'? 'selected' :'' }}>SMTP</option>
                <option value="ses" {{ data_get($setting->values,'driver') == 'ses'? 'selected' :'' }}>Amazon SES</option>
                <option value="preview" {{ data_get($setting->values,'driver') == 'preview'? 'selected' :'' }}>Preview (only for dev mode)</option>
            </select>
        </div>
    </div>
</div>

<div class="driver-setting sendmail" style="display: {{ data_get($setting->values,'driver') == 'sendmail' ? 'block' : 'none' }}">
    <div class="row">
        <div class="col-12"><h4>Sendmail Setting</h4></div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="sendmail_path">Sendmail Path</label>
                <input type="text" name="values[sendmail_path]" id="sendmail_path" class="form-control"
                       value="{{ data_get($setting->values,'sendmail_path') }}">
            </div>
        </div>
    </div>
</div>
<div class="driver-setting mailgun" style="display: {{ data_get($setting->values,'driver') == 'mailgun' ? 'block' : 'none' }}">
    <div class="row">
        <div class="col-12"><h4>Mailgun Setting</h4></div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="mailgun_domain">Domain</label>
                <input type="text" name="values[mailgun_domain]" id="mailgun_domain" class="form-control"
                       value="{{ data_get($setting->values,'mailgun_domain') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="mailgun_secret">Secret</label>
                <input type="text" name="values[mailgun_secret]" id="mailgun_secret" class="form-control"
                       value="{{ data_get($setting->values,'mailgun_secret') }}">
            </div>
        </div>
    </div>
</div>
<div class="driver-setting ses" style="display: {{ data_get($setting->values,'driver') == 'ses' ? 'block' : 'none' }}">
    <div class="row">
        <div class="col-12"><h4>Amazon SES Setting</h4></div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="ses_key">Key</label>
                <input type="text" name="values[ses_key]" id="ses_key" class="form-control"
                       value="{{ data_get($setting->values,'ses_key') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="ses_secret">Key</label>
                <input type="text" name="values[ses_secret]" id="ses_secret" class="form-control"
                       value="{{ data_get($setting->values,'ses_secret') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="ses_region">Key</label>
                <input type="text" name="values[ses_region]" id="ses_region" class="form-control"
                       value="{{ data_get($setting->values,'ses_region') }}">
            </div>
        </div>
    </div>
</div>
<div class="driver-setting smtp" style="display: {{ data_get($setting->values,'driver') == 'smtp' ? 'block' : 'none' }}">
    <div class="row">
        <div class="col-12"><h4>SMTP Setting</h4></div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="host">Mail Host</label>
                <input type="text" name="values[host]" id="host" class="form-control"
                       value="{{ data_get($setting->values,'host') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="port">Mail Port</label>
                <input type="text" name="values[port]" id="port" class="form-control"
                       value="{{ data_get($setting->values,'port') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleInputEmail1">Mail Encryption</label>
                <select class="form-control" name="values[encryption]">
                    <option value="null" {{ data_get($setting->values,'encryption') == 'null'? 'selected' :'' }} >None</option>
                    <option value="tls" {{ data_get($setting->values,'encryption') == 'tls'? 'selected' :'' }}>TLS</option>
                    <option value="ssl" {{ data_get($setting->values,'encryption') == 'ssl'? 'selected' :'' }}>SSL</option>
                    <option value="starttls" {{ data_get($setting->values,'encryption') == 'starttls'? 'selected' :'' }}>StartTLS</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="timeout">Timeout</label>
                <input type="text" name="values[timeout]" id="timeout" class="form-control"
                       value="{{ data_get($setting->values,'timeout','300') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="username">Mail Username</label>
                <input type="text" name="values[username]" id="username" class="form-control"
                       value="{{ data_get($setting->values,'username') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="password">Mail Password</label>
                <div class="input-group">
                    <input type="password" name="values[password]" id="password" class="form-control"
                           value="{{ data_get($setting->values,'password') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePass"><i class="fa fa-eye"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="row my-3">
    <div class="col-md-6">
        <div class="form-group">
            <label for="test_email">Test Email Setting</label>
            <div class="input-group">
                <input type="text" id="test_email" value="{{ settings('app.email') }}" class="form-control" placeholder="Enter email">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="checkMailSetting"><i class="fa fa-paper-plane"></i></button>
                </div>
            </div>
            <small id="emailHelp" class="form-text text-muted">Send test email for checking the mail setting. <b>Save settings before test.</b></small>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(function () {
            $('#driver').on('change', function () {
                var _val = $(this).val();
                $('.driver-setting').hide();
                $('.' + _val).show();
            });
            $('#togglePass').on('click', function () {
                var input = $(this).closest('.input-group').find('input');
                var type = input.attr('type');
                if (type === 'text') {
                    input.attr('type', 'password');
                    $(this).find('i').removeClass('fa-eye-slash');
                } else {
                    input.attr('type', 'text');
                    $(this).find('i').addClass('fa-eye-slash');
                }
            });

            $(document).on('click', '#checkMailSetting', function (e) {
                var email = $('#test_email').val();
                if (email) {
                    if (confirm("Are you sure you saved the settings and \nwant to send test email?")) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('admin.settings.test-email') }}',
                            data: {email: email},
                            dataType: 'json',
                            success: function (data) {
                                if (data.success) {
                                    toastr.success(data.msg);
                                } else {
                                    toastr.error(data.msg);
                                }
                            }
                        });
                    }
                } else {
                    toastr.error("Enter email address.");
                }
            });
        });
    </script>
@endpush
