@aware([
    'contentComponent'
])
<x-dynamic-component
    :component="$resolveComponentName()"
    :attributes="$resolveAttributes($contentComponent)"
/>
