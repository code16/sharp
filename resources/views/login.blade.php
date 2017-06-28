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

                    <div>
                        <input type="text" id="login" name="login" value="{{ old('login') }}">
                    </div>
                    <div>
                        <input type="password" id="password" name="password">
                    </div>
                    <button type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>

@endsection