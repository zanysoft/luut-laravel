@extends('admin.layouts.app')
@section('content')
    <div class="card">
        <form action="{{ $item->id ? route('admin.business-types.update',$item->id) :route('admin.business-types.store')  }}"
              method="post" enctype="multipart/form-data">
            @csrf
            @method($item->id ? "PUT" : "POST")
            <div class="card-header">
                <div class="row">
                    <div class="col-6"><h3 class="card-title">{{ $item->name }}</h3></div>
                    <div class="col-6 text-right">
                        <a class="btn btn-primary" href="{{ route('admin.business-types.index') }}"><i
                                class="fa fa-angle-left"></i>
                            Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <input type="hidden" name="id" value="{{ $item->id }}">
                <div class="row">
                    <div class="col-md-6 offset-3">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" required class="form-control"
                                   value="{{ data_get($item,'name') }}" placeholder="Enter name">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" required class="form-control">
                                <option value="">Select Status</option>
                                <option value="1" {{ old('status',$item) === 1 ? 'selected' : '' }} >Active</option>
                                <option value="0" {{ old('status',$item) === 0 ? 'selected' : '' }}>In-Active</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <a class="btn btn-danger" href="{{ route('admin.business-types.index') }}">Cancel</a>
                @if(hasPermission('business-types.edit'))
                    <button type="submit" class="btn btn-success">{{ $item->id ? __('Update') : __("Save") }}</button>
                @endif
            </div>
        </form>
    </div>
@endsection
