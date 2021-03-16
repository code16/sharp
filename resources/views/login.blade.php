@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="login">
        <div class="container">
            <form method="POST" action="{{ route("code16.sharp.login.post") }}">
                {{ csrf_field() }}
                <div class="row justify-content-center">
                    <div class="col-sm-9 col-md-6 col-lg-5 col-xl-4">

                        @if(file_exists(public_path($icon = '/sharp-assets/login-icon.png')))
                            <div class="text-center">
                                <img src="{{ asset($icon) }}?{{ filemtime(public_path($icon)) }}" alt="{{config("sharp.name", "Sharp")}}" width="300" class="w-auto h-auto" style="max-height: 100px;max-width: 200px">
                            </div>
                        @else
                            <h1 class="text-center mb-3">{{config("sharp.name", "Sharp")}}</h1>
                        @endif

                        @if ($errors->any())

                            <div role="alert" class="alert alert-danger">
                                @lang('sharp::auth.validation_error')
                            </div>

                        @elseif (session()->has('invalid'))

                            <div role="alert" class="alert alert-danger">
                                @lang('sharp::auth.invalid_credentials')
                            </div>

                        @endif

                        <div class="SharpModule">
                            <div class="SharpModule__inner">
                                <div class="SharpModule__content">
                                    <div class="SharpForm__form-item SharpForm__form-item--row">
                                        <input type="text" name="login" id="login" class="form-control" value="{{ old('login') }}" placeholder="@lang('sharp::login.login_field')">
                                    </div>

                                    <div class="SharpForm__form-item SharpForm__form-item--row">
                                        <input type="password" name="password" id="password" class="form-control" placeholder="@lang('sharp::login.password_field')">
                                    </div>
                                    <button type="submit" id="submit" class="btn btn-primary">
                                        @lang('sharp::login.button')
                                    </button>
                                </div>
                            </div>
                        </div>
                        @includeIf(config("sharp.login_page_message_blade_path"))
                        <p class="text-center mt-3 text-muted" style="font-size: .75em">powered by <a href="https://sharp.code16.fr/docs/">Sharp {{sharp_version()}}</a></p>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
