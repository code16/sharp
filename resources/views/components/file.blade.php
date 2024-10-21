@php
/**
 * @see \Code16\Sharp\View\Components\File
 */
@endphp

<p {{ $attributes->class('sharp-file') }}>
    <svg class="sharp-file__icon" width="1.5em" height="1.25em" viewBox="0 0 1024 1024" fill="currentColor" opacity=".75" aria-hidden="true">
        <path d="M854.6 288.6L639.4 73.4c-6-6-14.1-9.4-22.6-9.4H192c-17.7 0-32 14.3-32 32v832c0 17.7 14.3 32 32 32h640c17.7 0 32-14.3 32-32V311.3c0-8.5-3.4-16.7-9.4-22.7zM790.2 326H602V137.8L790.2 326zm1.8 562H232V136h302v216a42 42 0 0 0 42 42h216v494z" />
    </svg>

    <small class="sharp-file__name">
        {{ $legend ?? $name }}
    </small>
</p>
