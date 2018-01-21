@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col col-sm-10 col-md-9 col-lg-6 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        Login
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}

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
                                <div class="form-check">
                                    <input id="remember"
                                           type="checkbox"
                                           name="remember"
                                           {{ old('remember') ? 'checked' : '' }}
                                           class="form-check-input {{ $errors->has('remember') ? ' is-invalid' : '' }}">
                                    <label for="remember"
                                           class="form-check-label">
                                        Remember Me
                                    </label>
                                </div>
                                @if ($errors->has('remember'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('remember') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
