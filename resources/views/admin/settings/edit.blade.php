@extends('layouts.app')
@section('content')
    <div class="card">
        <form action="{{ route('admin.settings.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h3 class="card-title">{{ $setting->name }}</h3>

                    </div>
                    <div class="col-6 text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                {{ $setting->name }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                @foreach($settings as $row)
                                    <a href="{{ $row->key != $setting->key ? route('admin.settings.edit',$row->key) : '#' }}"
                                       class="dropdown-item {{ $row->key == $setting->key ? 'active':''}}" type="button">{{ $row->name }}</a>
                                @endforeach
                            </div>
                        </div>
                        <a class="btn btn-primary" href="{{ route('admin.settings.index') }}">Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <input type="hidden" name="key" value="{{ $setting->key }}">

                @includeIf('admin.settings.forms.'.$setting->key)
            </div>
            <div class="card-footer text-right">
                <a class="btn btn-danger" href="{{ route('admin.settings.index') }}">Cancel</a>
                <button type="submit" class="btn btn-success">Update</button>
            </div>
        </form>
    </div>
    {{--@dump(settings())--}}
@endsection
