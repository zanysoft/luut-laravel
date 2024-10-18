@extends('admin.layouts.auth')

@section('content')
    <p class="login-box-msg">Sign in to start your session</p>
    <form action="{{ route('admin.login') }}" method="post">
        @csrf
        {!! show_alert() !!}
        <div class="mb-3">
            <div class="input-group">
                <input type="text" id="login" name="login" value="{{ old('login','admin@admin.com') }}"
                       class="form-control  @error('login') is-invalid @enderror" placeholder="Email or Username"
                       required autocomplete="login" autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            @error('login')
            <div class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <div class="input-group">
                <input type="password" name="password" id="password" value="admin"
                       class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                       required autocomplete="current-password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            @error('password')
            <div class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>
        <div class="row">
            <div class="col-7">
                {{--<div class="icheck-primary">
                    <input type="checkbox" id="remember">
                    <label for="remember">
                        Remember Me
                    </label>
                </div>--}}
            </div>
            <div class="col-5 text-right">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
        </div>
    </form>
    @if(\Illuminate\Support\Facades\Route::has('password.request'))
        <p class="mb-1">
            <a href="{{ route('admin.password.request') }}">I forgot my password</a>
        </p>
    @endif
@endsection
