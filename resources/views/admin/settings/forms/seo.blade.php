<h4>Social Links</h4>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="facebook_link">Facebook</label>
            <input type="text" name="values[facebook_link]" id="facebook_link" class="form-control"
                   value="{{ data_get($setting->values,'facebook_link') }}" placeholder="Enter facebook link">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="instagram_link">Instagram</label>
            <input type="text" name="values[instagram_link]" id="instagram_link" class="form-control"
                   value="{{ data_get($setting->values,'instagram_link') }}" placeholder="Enter instagram link">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="twitter_link">Twitter</label>
            <input type="text" name="values[twitter_link]" id="twitter_link" class="form-control"
                   value="{{ data_get($setting->values,'twitter_link') }}" placeholder="Enter twitter link">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="linkedin_link">Linkedin</label>
            <input type="text" name="values[linkedin_link]" id="linkedin_link" class="form-control"
                   value="{{ data_get($setting->values,'linkedin_link') }}" placeholder="Enter linkedin link">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <textarea class="form-control" name="values[robots_txt]">{!! data_get($setting->values,'robots_txt') !!}</textarea>
    </div>
</div>
