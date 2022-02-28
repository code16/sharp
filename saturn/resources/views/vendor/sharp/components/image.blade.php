@props([
    'customProp' => false
])

<p class="mb-4">
    <img {{ $attributes->class(['custom' => $customProp]) }}
        src="{{ $fileModel->thumbnail($thumbnailWidth, $thumbnailHeight, $filters) }}"
    >
</p>
