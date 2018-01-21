@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col col-sm-10 col-md-9 col-lg-6 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        Reset Password
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
                            {{ csrf_field() }}

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <label for="email"
                                       class="form-control-label">
                                    E-Mail Address
                                </label>
                                <input id="email"
                                       type="email"
                                       class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus>
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="password"
                                       class="form-control-label">
                                    Password
                                </label>
                                <input id="password"
                                       type="password"
                                       class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                       name="password"
                                       required>
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="password-confirm">
                                    Confirm Password
                                </label>
                                <input id="password-confirm"
                                       type="password"
                                       class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                       name="password_confirmation"
                                       required>
                                @if ($errors->has('password_confirmation'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('password_confirmation') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
