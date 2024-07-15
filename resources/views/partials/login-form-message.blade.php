
@if(view()->exists(sharpConfig()->get('auth.login_form_message')))
    @include(sharpConfig()->get('auth.login_form_message'))
@else
    <x-sharp::card>
      {!! sharpConfig()->get('auth.login_form_message') !!}
    </x-sharp::card>
@endif
