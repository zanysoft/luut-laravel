<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">App Name</label>
            <input type="text" name="values[name]" id="name" class="form-control"
                   value="{{ data_get($setting->values,'name') }}" placeholder="Enter app name">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="values[email]" id="email" class="form-control"
                   value="{{ data_get($setting->values,'email') }}" placeholder="Enter email">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="values[phone]" id="phone" class="form-control"
                   value="{{ data_get($setting->values,'phone') }}" placeholder="Enter phone">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="values[address]" id="address" class="form-control"
                   value="{{ data_get($setting->values,'address') }}" placeholder="Enter address">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Logo</label>
            <input type="text" name="values[logo]" class="form-control filemanager"
                   value="{{ data_get($setting->values,'logo') }}"
                   data-rfm-key="@filemanager_get_key()"
                   data-type="image"
            >
        </div>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="notification-email">Send Notifications To:</label>
            <input type="email" name="values[notification-email]" id="notification-email" class="form-control"
                   value="{{ data_get($setting->values,'notification-email') }}" placeholder="Enter notification email">
            <div class="help-block">Set email for receiving all notifications like deal created etc.</div>
        </div>
    </div>
</div>
