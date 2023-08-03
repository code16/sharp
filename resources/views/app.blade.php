<x-sharp::layout>
    <x-slot:head>
        @routes
        @inertiaHead
    </x-slot:head>

    @inertia

    @if($login ?? false)
        <template id="login-append">
            @includeIf(config("sharp.login_page_message_blade_path"))
        </template>
    @endif
</x-sharp::layout>
