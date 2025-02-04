@php
/**
 * @var \Code16\Sharp\View\Components\Alert\AssetsOutdated $self
 */
@endphp

@if($self->isAssetsOutdated())
    <div class="fixed bottom-0 left-0 right-0 bg-background border-t p-4 z-50">
        <div class="container">
            <div class="flex flex-wrap items-center gap-4 md:gap-6">
                <div class="shrink-0">
                    <svg class="w-6 h-6 stroke-[1.5]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <div class="flex-1 flex flex-col sm:flex-row sm:items-center gap-4 md:gap-6">
                    <div class="flex-1">
                        <p class="mb-1">
                            Sharp assets are out of date. Please run the following command:
                        </p>
                        <input class="w-full font-mono bg-inherit text-sm"
                            value="php artisan vendor:publish --tag=sharp-assets --force"
                            onclick="this.select()" aria-label="Command">
                    </div>
                    <form action="{{ route('code16.sharp.update-assets') }}" method="post">
                        @csrf
                        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 aria-disabled:pointer-events-none aria-disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                            type="submit"
                        >
                            Update assets
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
