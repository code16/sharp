@extends("sharp::layout", [
    'bodyClass' => 'login'
])

@section("content")
    <div id="sharp-app" class="login__content">
        <div class="container">
            <form method="POST" action="{{ route("code16.sharp.login.2fa.post") }}">
                {{ csrf_field() }}
                <div class="row justify-content-center">
                    <div class="col-sm-9 col-md-6 col-lg-5 col-xl-4">
                        @if($icon = config('sharp.theme.logo_urls.login'))
                            <div class="text-center mb-3">
                                <img src="{{ url($icon) }}" alt="{{config("sharp.name", "Sharp")}}" width="300" class="w-auto h-auto" style="max-height: 100px;max-width: 200px">
                            </div>
                        @elseif(file_exists(public_path($icon = 'sharp-assets/login-icon.png')))
                            <div class="text-center mb-3">
                                <img src="{{ asset($icon) }}?{{ filemtime(public_path($icon)) }}" alt="{{config("sharp.name", "Sharp")}}" width="300" class="w-auto h-auto" style="max-height: 100px;max-width: 200px">
                            </div>
                        @elseif(file_exists($logo = public_path('/vendor/sharp/images/logo.svg')))
                            <div class="text-center logo mb-4">
                                {!! file_get_contents($logo) !!}
                            </div>
                        @endif
                        
                        @if ($errors->any())
                            <div role="alert" class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="card border-0 mb-3">
                            @if(config("sharp.name", 'Sharp') !== 'Sharp')
                                <div class="card-header bg-transparent border-0 pb-0 pt-4">
                                    <h1 class="text-center card-title mb-0 fs-4">{{ config("sharp.name") }}</h1>
                                </div>
                            @endif
                            <div class="card-body p-5 py-4">
                                <div class="SharpForm__form-item mb-3">
                                    <input type="text" name="code" id="code" class="form-control" value="" placeholder="@lang('sharp::login.code_field')" autocomplete="">
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" id="submit" class="btn btn-primary btn-lg">
                                        @lang('sharp::login.button')
                                    </button>
                                </div>
                            </div>
                        </div>

                        <p class="text-center mt-2 text-white login__powered">
                            <span>powered by</span>
                            <a class="text-reset" href="https://sharp.code16.fr/docs/">Sharp {{sharp_version()}}</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
