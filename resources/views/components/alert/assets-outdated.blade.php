@php
/**
 * @var \Code16\Sharp\View\Components\Alert\AssetsOutdated $self
 */
@endphp

@if($self->isAssetsOutdated())
    <style>
        @media (min-width: 768px) {
            :root {
                --outdated-alert-height: 4rem;
                --navbar-top: var(--outdated-alert-height);
            }
        }
    </style>
    <div class="alert alert-warning d-none d-md-flex flex-column justify-content-center position-fixed w-100 start-0 top-0 mb-0 rounded-0 fs-6"
        style="min-height: var(--outdated-alert-height); z-index: 10"
    >
        <div class="container">
            <div class="row align-items-center">
                <div class="col-auto">
                    Sharp assets are out of date. Please run the following command:
                </div>
                <div class="col">
                    <input class="form-control form-control-sm"
                        value="php artisan vendor:publish --provider=Code16\\Sharp\\SharpServiceProvider --tag=assets --force"
                        onclick="this.select()" aria-label="Command">
                </div>
            </div>
        </div>
    </div>
@endif
