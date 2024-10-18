@extends('admin.layouts.auth')
@section('content')
    <p class="login-box-msg">{{ __('Reset Password') }}</p>
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.password.email') }}">
        @csrf
        <div class="mb-3">
            <div class="input-group">
                <input type="email" name="email" id="email" required autocomplete="email"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="{{ __('Email Address') }}" autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            @error('email')
            <div class="invalid-feedback d-block" role="alert">
                <strong>{{ $message??' dssdfdsf dfdsf sd' }}</strong>
            </div>
            @enderror
        </div>
        <div class="row mb-0">
            <div class="col-md-8 offset-md-2">
                <button type="submit" class="btn btn-primary">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>
        </div>
    </form>
@endsection
