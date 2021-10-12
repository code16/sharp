@foreach($fragments() as $fragment)
    <x-dynamic-component
        :component="$fragmentComponent($fragment)"
        :fragment="$fragment"
    ></x-dynamic-component>
@endforeach
