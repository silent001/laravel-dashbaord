@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}</div>

                <div class="card-body">
                    <form class="d-inline" method="POST" action="{{ route('two-factor.login') }}">
                        @csrf
                        <input id="code" type="text" class="form-control" name="code" inputmode="numeric" autofocus autocomplete="one-time-code" />
                        <button type="submit" class="btn btn-primary">
                            {{ __('Verify') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
