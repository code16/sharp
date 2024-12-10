@php
/**
 * @var \Code16\Sharp\View\Components\Alert\AssetsOutdated $self
 */
@endphp

@if($self->isAssetsOutdated())
    <div class="fixed bottom-0 left-0 right-0 bg-background border-t p-4 z-50">
        <div class="container">
            <div class="flex items-center space-x-4 md:space-x-6">
                <div class="shrink-0">
                    <svg class="w-6 h-6 stroke-[1.5]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <div class="flex-1">
                    <p class="mb-1">
                        Sharp assets are out of date. Please run the following command:
                    </p>
                    <input class="w-full font-mono bg-inherit text-sm"
                        value="php artisan vendor:publish --tag=sharp-assets --force"
                        onclick="this.select()" aria-label="Command">
                </div>
            </div>
        </div>
    </div>
@endif
