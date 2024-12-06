@php
/**
 * @see \Code16\Sharp\View\Components\Image
 */
@endphp

<figure>
    <img src="{{ $fileModel->thumbnail($thumbnailWidth, $thumbnailHeight, $filters) }}" {{ $attributes }}>
    @if($legend)
        <figcaption>
            {{ $legend }}
        </figcaption>
    @endif
</figure>
