@extends('admin.layouts.app')
@section('content')
    <div class="card">
        <form action="{{ route('admin.users.update',$user->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-header">
                <div class="row">
                    <div class="col-6"><h3 class="card-title">{{ $user->name }}</h3></div>
                    <div class="col-6 text-right">
                        <a class="btn btn-primary" href="{{ route('admin.users.index') }}"><i class="fa fa-angle-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <input type="hidden" name="id" value="{{ $user->id }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control"
                                   value="{{ data_get($user,'first_name') }}" placeholder="Enter first name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control"
                                   value="{{ data_get($user,'last_name') }}" placeholder="Enter last name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_of_birth">Gender</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="">Select</option>
                                <option value="male" {{ data_get($user,'gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ data_get($user,'gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                                   value="{{ data_get($user,'date_of_birth') }}" placeholder="Select date">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="{{ data_get($user,'email') }}" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                   value="{{ data_get($user,'phone') }}" placeholder="Enter phone">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" data-space="" class="form-control slugify"
                                   value="{{ data_get($user,'username') }}" placeholder="Enter username">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" minlength="6" name="password" id="password" class="form-control generate-password"
                                   value="{{ old('password') }}" placeholder="Enter password">
                            <small class="form-text text-muted">Leave empty if no need to change.</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" id="address" class="form-control"
                                   value="{{ data_get($user,'address') }}" placeholder="Enter address line 1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" name="city" id="city" class="form-control"
                                   value="{{ data_get($user,'city') }}" placeholder="Enter city">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="state">State</label>
                            <input type="text" name="state" id="state" class="form-control"
                                   value="{{ data_get($user,'state') }}" placeholder="Enter state">
                        </div>
                    </div>
                    <input type="hidden" value="UK" name="country_code">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="zipcode">PostalCode</label>
                            <input type="text" name="zipcode" id="zipcode" class="form-control"
                                   value="{{ data_get($user,'zipcode') }}" placeholder="Enter postal code">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="avatar">Image</label>
                            <div class="custom-file">
                                <input type="file" name="avatar" id="avatar" class="custom-file-input">
                                <label class="custom-file-label" for="customFile">Choose Image</label>
                            </div>
                            @if($user->avatar && \Illuminate\Support\Facades\Storage::exists($user->avatar))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar) }}">
                            @endif
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="powwr_id">Role <small>(for admin panel access)</small></label>
                            <div>
                                @foreach($roles as $role)
                                    @if($user->id == auth()->id() || ($user->hasRole('super-admin') && !auth()->user()->hasRole('super-admin')))
                                        <span class="badge badge-light mr-2 text-sm" style="font-weight: normal">{{ $role->title }} (<code>{{ $role->name }}</code>)</span>
                                    @else
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" {{ $user->hasRole($role->name) ?'checked':'' }} type="checkbox" id="role{{ $role->id }}" name="roles[]" value="{{ $role->name }}">
                                            <label class="form-check-label" for="role{{ $role->id }}">{{ $role->title }}</label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <a class="btn btn-danger" href="{{ route('admin.users.index') }}">Cancel</a>
                @if(hasPermission('users.edit'))
                    <button type="submit" class="btn btn-success">Update</button>
                @endif
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/plugins/bs-file-input/bs-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init()
        })
    </script>
@endsection
