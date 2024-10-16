
@if(view()->exists(sharp()->config()->get('auth.login_form_message')))
    @include(sharp()->config()->get('auth.login_form_message'))
@else
    <x-sharp::card>
      {!! sharp()->config()->get('auth.login_form_message') !!}
    </x-sharp::card>
@endif
