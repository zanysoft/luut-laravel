<h4>Social Links</h4>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="facebook_link">Facebook</label>
            <input type="text" name="values[links][facebook]" id="facebook_link" class="form-control"
                   value="{{ data_get($setting->values,'links.facebook') }}" placeholder="Enter facebook link">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="instagram_link">Instagram</label>
            <input type="text" name="values[links][instagram]" id="instagram_link" class="form-control"
                   value="{{ data_get($setting->values,'links.instagram') }}" placeholder="Enter instagram link">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="twitter_link">Twitter</label>
            <input type="text" name="values[links][twitter]" id="twitter_link" class="form-control"
                   value="{{ data_get($setting->values,'links.twitter') }}" placeholder="Enter twitter link">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="linkedin_link">Linkedin</label>
            <input type="text" name="values[links][linkedin]" id="linkedin_link" class="form-control"
                   value="{{ data_get($setting->values,'links.linkedin') }}" placeholder="Enter linkedin link">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="youtube_link">Youtube</label>
            <input type="text" name="values[links][youtube]" id="youtube_link" class="form-control"
                   value="{{ data_get($setting->values,'links.youtube') }}" placeholder="Enter youtube link">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="tiktok_link">TikTok</label>
            <input type="text" name="values[links][tiktok]" id="tiktok_link" class="form-control"
                   value="{{ data_get($setting->values,'links.tiktok') }}" placeholder="Enter tiktok link">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <label for="linkedin_link">Edit robots.txt file</label>
        <textarea class="form-control" name="robots_txt" rows="8"

        >{!! data_get($setting,'robots_txt') !!}</textarea>
    </div>
</div>
