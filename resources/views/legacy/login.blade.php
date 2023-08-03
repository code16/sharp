

<x-sharp::layout class="login">
    <x-slot:title>
        {{ trans('sharp::login.login_page_title') }}
    </x-slot:title>
    <x-sharp::layout.auth>
        <form method="POST" action="{{ route("code16.sharp.login.post") }}">
            @csrf
            <div class="SharpForm__form-item mb-3">
                <input type="text" name="login" id="login" class="form-control" value="{{ old('login') }}"
                    placeholder="{{ __('sharp::login.login_field') }}" autocomplete="username">
            </div>

            <div class="SharpForm__form-item mb-3">
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="{{ __('sharp::login.password_field') }}" autocomplete="current-password">
            </div>

            @if(config('sharp.auth.suggest_remember_me', false))
                <div class="SharpForm__form-item mb-3">
                    <div class="SharpCheck form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember"
                            id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            @lang('sharp::login.remember')
                        </label>
                    </div>
                </div>
            @endif

            <div class="text-center mt-4">
                <button type="submit" id="submit" class="btn btn-primary btn-lg">
                    @lang('sharp::login.button')
                </button>
            </div>
        </form>

        <x-slot:append>
            @includeIf(config("sharp.login_page_message_blade_path"))
        </x-slot:append>
    </x-sharp::layout.auth>
</x-sharp::layout>
