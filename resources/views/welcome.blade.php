@extends("sharp::layout")

@section("content")

    <x-sharp::page class="Sharp__dashboard-page">
        <div class="SharpActionView Sharp__empty-view">
            <div class="container h-100 d-flex justify-content-center align-items-center">
                <h1>
                    @lang("sharp::menu.no-dashboard-message")
                </h1>
            </div>
        </div>
    </x-sharp::page>

@endsection
