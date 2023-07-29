@props([
    // slots
    'append' => null,
])

<div id="sharp-app" class="login__content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-9 col-md-6 col-lg-5 col-xl-4">
                @if($logoUrl = app(\Code16\Sharp\Utils\SharpTheme::class)->loginLogoUrl())
                    <div class="text-center mb-3">
                        <img src="{{ url($logoUrl) }}" alt="{{ config("sharp.name") }}" width="300" class="w-auto h-auto" style="max-height: 100px;max-width: 200px">
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
                        {{ $slot }}
                    </div>
                </div>

                {{ $append }}

                <p class="text-center mt-2 text-white login__powered">
                    <span>powered by</span>
                    <a class="text-reset" href="https://sharp.code16.fr/docs/">Sharp {{sharp_version()}}</a>
                </p>
            </div>
        </div>
    </div>
</div>
