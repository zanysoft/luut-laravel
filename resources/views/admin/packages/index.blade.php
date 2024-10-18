@extends('admin.layouts.app')
@section('breadcrumb')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>{{ __('Packages') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('Packages') }}</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">{{ __('All Packages') }}
            <div class="float-right">
                <a href="{{ route('admin.packages.create') }}" class="btn btn-primary"><i
                        class="fa fa-plus"></i> {{ __('Add Package') }}</a>
            </div>
        </div>
        <div class="card-body">
            {{ $html->table() }}
        </div>
    </div>
@stop
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-ui/jquery-ui.min.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/plugins/ui.multiselect/ui.multiselect.css') }}" type="text/css"/>
    <style>
        .subscription {
            display: none;
        }

        .ui-multiselect {
            width: 100%;
            min-height: 200px;
        }
    </style>
@endsection
@section('script')
    {{ $html->scripts() }}
    {{--<script type="text/javascript">
        $(function () {
            @include('admin.layouts.scripts.filters')
        });
    </script>--}}

@endsection
