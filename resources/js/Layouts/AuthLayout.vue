<script setup lang="ts">
    import { config } from "@/util/config";

</script>

<template>
    <div id="sharp-app" class="p-4 h-screen flex justify-center items-center" style="background: linear-gradient(
          45deg,
          hsl(var(--primary-h), var(--primary-s), var(--accent-bg-l)),
          hsl(var(--primary-h), var(--primary-s), var(--primary-l)) 80%
    )">
        <div class="w-full max-w-sm">
            @if($logoUrl = app(\Code16\Sharp\Utils\SharpTheme::class)->loginLogoUrl())
            <div class="flex justify-center mb-3">
                <img src="{{ url($logoUrl) }}" alt="{{ config("sharp.name") }}" width="300" class="w-auto h-auto" style="max-height: 100px;max-width: 200px">
            </div>
            @elseif(file_exists($logo = public_path('/vendor/sharp/images/logo.svg')))
            <div class="flex justify-center logo mb-4">
                {!! file_get_contents($logo) !!}
            </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded p-4 border-0 mb-3">
                <template v-if="config('sharp.name') !== 'Sharp'">
                    <div class="card-header bg-transparent border-0 pb-0 pt-4">
                        <h1 class="text-center card-title mb-0 fs-4">{{ config("sharp.name") }}</h1>
                    </div>
                </template>

                <div class="card-body p-5 py-4">
                    <slot />
                </div>
            </div>

            <slot name="append" />

            <p class="text-center mt-2 text-white login__powered">
                <span>powered by</span>
                <a class="text-reset" href="https://sharp.code16.fr/docs/">Sharp {{ $page.props.sharpVersion }}</a>
            </p>
        </div>
    </div>
</template>
