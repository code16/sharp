<script setup lang="ts">
</script>

<template>
    <div id="sharp-app" class="p-4 h-screen flex justify-center items-center">
        <div class="w-full max-w-sm">
            <slot name="prepend" />

            <template v-if="$page.props.status">
                <div class="rounded-md bg-green-50 p-4 my-4">
                    <p class="text-sm font-medium text-green-800">
                        {{ $page.props.status }}
                    </p>
                </div>
            </template>

            <slot />

            <slot name="append" />

            <p class="text-center text-xs text-muted-foreground opacity-50 mt-2">
                <span>powered by </span>
                <a href="https://sharp.code16.fr/docs/" target="_blank">Sharp {{ $page.props.sharpVersion }}</a>
            </p>
        </div>
    </div>
</template>
