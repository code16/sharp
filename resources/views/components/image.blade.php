@php
/**
 * @see \Code16\Sharp\View\Components\Image
 */
@endphp

<p>
    <img src="{{ $fileModel->thumbnail($thumbnailWidth, $thumbnailHeight, $filters) }}" {{ $attributes }}>
</p>
