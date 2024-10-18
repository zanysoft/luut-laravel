@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h3 class="card-title">Business Types</h3>
                </div>
                <div class="col-6 text-right">
                    @if(hasPermission('business-types.create'))
                        <a href="{{ route('admin.business-types.create') }}" class="btn btn-primary"><i
                                class="fa fa-plus"></i> Add New</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            {{ $html->table() }}
        </div>
    </div>
@endsection
@section('style')
@endsection
@section('script')
    {{ $html->scripts() }}
@endsection

