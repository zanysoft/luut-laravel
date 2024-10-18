@extends('admin.layouts.app',['title'=>'Media'])
@section('content')
    <!-- Default box -->
    <div class="card">
        <div class="card-body">
            <iframe id="media" src="@filemanager_dialog(['type'=>0])" frameborder="0" width="100%" height="450"
                    style="width:100%; overflow: scroll; overflow-x: hidden; overflow-y: scroll;  min-height: 450px"></iframe>
        </div>
    </div>
    <!-- /.card -->
@stop

@section('script')
    <script>
        function resizeIframe(obj) {
            var _height = obj.contentWindow.document.body.scrollHeight;
            if (_height > window.innerHeight) {
                _height = window.innerHeight;
            }
            obj.style.height = _height + 'px';
        }

        $(window).on('resize', function () {
            resizeIframe($("#media").get(0));
        });

        $(document).ready(function (e) {
            $("#media").on('load', function () {
                resizeIframe(this);
            });
        });
    </script>

@stop
