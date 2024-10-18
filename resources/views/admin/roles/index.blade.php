@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h3 class="card-title">Roles</h3>
                </div>
                <div class="col-6 text-right">
                    @if(hasPermission('roles.create'))
                        <a href="{{ route('admin.roles.reset-permissions') }}" class="btn btn-primary"><i class="fa fa-sync"></i> Reset Permissions</a>
                    @endif
                    @if(hasPermission('roles.create'))
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Role</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Name</th>
                    <th>Users</th>
                    <th style="width: 120px">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->title }}</td>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->users()->count() }}</td>
                        <td class="text-center nowrap">
                            @if(hasPermission('roles.edit'))
                                <a class="btn btn-primary btn-xs" href="{{ route('admin.roles.edit',$role->id) }}">
                                    <i class="fa fa-edit"></i> Edit</a>
                            @endif

                            @if(hasPermission('roles.delete') && !\App\Models\Role::isDefaultRole($role))
                                <a class="btn btn-danger btn-xs" data-confirm="Are you sure you want to remove this role?" data-method="DELETE" href="{{ route('admin.roles.destroy',$role->id) }}">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('style')
@endsection
@section('script')

@endsection

