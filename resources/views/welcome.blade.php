@extends("sharp::layout")

@section("content")

    <x-sharp::page class="Sharp__dashboard-page">
        <div class="SharpActionBar__bar"></div>
        <div class="SharpActionView vh-100">
            <div class="container h-100 d-flex justify-content-center align-items-center">
                <h1 class="text-muted">
                    @lang("sharp::menu.no-dashboard-message")
                </h1>
            </div>
        </div>
    </x-sharp::page>
@endsection
