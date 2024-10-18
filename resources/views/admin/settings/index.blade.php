@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Settings</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Key</th>
                        <th class="text-right" style="width: 150px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($settings as $setting)
                        <tr>
                            <td>{{ $setting->name }}</td>
                            <td><code>{{ $setting->key }}</code></td>
                            <td class="text-right">
                                @if(hasPermission('settings.edit'))
                                    <a class="btn btn-info btn-sm" href="{{ route('admin.settings.edit',$setting->key) }}">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
