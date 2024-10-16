@props([
    'label',
    'labelLimit' => null,
    'url' => null,
])
<sharp-badge class="line-clamp-1" variant="secondary" {{ $attributes
    ->when(isset($url))->merge([
        'as' => 'a',
        'href' => $url ?? null,
    ])
    ->when($labelLimit && strlen($label) > $labelLimit)->merge([
        'title' => $label,
    ])
}}>{{
    $label ? str($label)->when($labelLimit)->limit($labelLimit) : $slot
}}</sharp-badge>
