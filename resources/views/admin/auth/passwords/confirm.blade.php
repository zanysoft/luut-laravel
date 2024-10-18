@extends('admin.layouts.auth')

@section('content')
    <p class="login-box-msg">{{ __('Confirm Password') }}</p>
    {{ __('Please confirm your password before continuing.') }}

    <form method="POST" action="{{ route('admin.password.confirm') }}">
        @csrf

        <div class="row mb-3">
            <label for="password" class="text-md-end">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   name="password" required autocomplete="current-password">

            @error('password')
            <div class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </div>
            @enderror
        </div>

        <div class="row mb-0">
            <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Confirm Password') }}
                </button>

                @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('admin.password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>
        </div>
    </form>
@endsection
