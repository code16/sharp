@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="login">
        <div class="container">
            <form method="POST" action="{{ route("code16.sharp.login.post") }}">
                {{ csrf_field() }}
                <div class="row justify-content-center">
                    <div class="col-sm-9 col-md-6 col-lg-5 col-xl-4">

                        <h1 class="text-center mb-3">Sharp</h1>

                        @if ($errors->any())

                            <div role="alert" class="SharpNotification SharpNotification--error">
                                <div class="SharpNotification__details">
                                    <div class="SharpNotification__text-wrapper">
                                        <p class="SharpNotification__subtitle">@lang('sharp::auth.validation_error')</p>
                                    </div>
                                </div>
                            </div>

                        @elseif (session()->has('invalid'))

                            <div role="alert" class="SharpNotification SharpNotification--error">
                                <div class="SharpNotification__details">
                                    <div class="SharpNotification__text-wrapper">
                                        <p class="SharpNotification__subtitle">@lang('sharp::auth.invalid_credentials')</p>
                                    </div>
                                </div>
                            </div>

                        @endif
                        <div class="SharpModule">
                            <div class="SharpModule__inner">
                                <div class="SharpModule__content">
                                    <div class="SharpForm__form-item SharpForm__form-item--row">
                                        <input type="text" name="login" id="login" class="SharpText" value="{{ old('login') }}" placeholder="@lang('sharp::login.login_field')">
                                    </div>

                                    <div class="SharpForm__form-item SharpForm__form-item--row">
                                        <input type="password" name="password" id="password" class="SharpText" placeholder="@lang('sharp::login.password_field')">
                                    </div>
                                    <button type="submit" id="submit" class="SharpButton SharpButton--primary">
                                        @lang('sharp::login.button')
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection