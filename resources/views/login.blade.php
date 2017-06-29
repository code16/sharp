@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        <div class="container">
            <form method="POST" action="{{ route("code16.sharp.login.post") }}">
                {{ csrf_field() }}
                <div class="col-sm-6 push-sm-3">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @lang('sharp::auth.validation_error')
                        </div>

                    @elseif (session()->has('invalid'))
                        <div class="alert alert-danger">
                            @lang('sharp::auth.invalid_credentials')
                        </div>
                    @endif

                    <div class="SharpFieldContainer SharpForm--form-item">
                        <label class="SharpForm--label" for="login">
                            Login
                        </label>
                        <input type="text" name="login" id="login" class="SharpText" value="{{ old('login') }}">
                    </div>

                    <div class="SharpFieldContainer SharpForm--form-item">
                        <label class="SharpForm--label" for="password">
                            Password
                        </label>
                        <input type="password" name="password" id="password" class="SharpText">
                    </div>
                    <button type="submit" class="SharpButton SharpButton--primary">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection