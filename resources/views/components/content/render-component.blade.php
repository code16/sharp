@aware([
    'contentComponent'
])
<x-dynamic-component
    :component="$fragment->getComponentName()"
    :attributes="$resolveAttributes($contentComponent)"
>
    {!! $fragment->content !!}
</x-dynamic-component>
