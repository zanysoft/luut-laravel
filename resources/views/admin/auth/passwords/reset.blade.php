@extends('admin.layouts.auth')

@section('content')
    <p class="login-box-msg">{{ __('Reset Password') }}</p>

    <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="row mb-3">
            <label for="email" class="text-md-end">{{ __('Email Address') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
            @error('email')
            <div class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>

        <div class="row mb-3">
            <label for="password" class="text-md-end">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   name="password" required autocomplete="new-password">

            @error('password')
            <div class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>

        <div class="row mb-3">
            <label for="password-confirm" class="text-md-end">{{ __('Confirm Password') }}</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                   required autocomplete="new-password">
        </div>

        <div class="row mb-0">
            <div class="col-md-8 offset-md-2">
                <button type="submit" class="btn btn-block btn-primary">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </div>
    </form>
@endsection
