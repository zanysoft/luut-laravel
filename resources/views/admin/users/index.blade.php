@extends('admin.layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h3 class="card-title">Users</h3>
                </div>
                <div class="col-6 text-right">
                    @if(hasPermission('users.create'))
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</a>
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

