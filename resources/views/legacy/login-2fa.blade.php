
<x-sharp::layout class="login">
    <x-slot:title>
        {{ trans('sharp::login.login_page_title') }}
    </x-slot:title>
    <x-sharp::layout.auth>
        <div class="mb-3">
            {!! $helpText !!}
        </div>

        <form method="POST" action="{{ route("code16.sharp.login.2fa.post") }}">
            @csrf
            <div class="SharpForm__form-item mb-3">
                <input type="text" name="code" id="code" class="form-control" value="" placeholder="{{ __('sharp::login.code_field') }}">
            </div>

            <div class="text-center mt-4">
                <button type="submit" id="submit" class="btn btn-primary btn-lg">
                    @lang('sharp::login.button')
                </button>
            </div>
        </form>
    </x-sharp::layout.auth>
</x-sharp::layout>
