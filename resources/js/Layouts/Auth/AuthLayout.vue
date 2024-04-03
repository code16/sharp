<script setup lang="ts">
import { Alert } from "@/components/ui/alert";
import SharpIcon from "@/svg/SharpIcon.vue";
</script>

<template>
    <div id="sharp-app" class="p-4 h-screen flex justify-center items-center">
        <SharpIcon class="absolute top-8 left-8 w-[100px]" />
        <div class="w-full max-w-sm">
            <slot name="prepend" />

            <slot />

            <slot name="append" />

            <p class="text-center text-xs text-muted-foreground opacity-50 mt-2">
                <span>powered by </span>
                <a href="https://sharp.code16.fr/docs/" target="_blank">Sharp {{ $page.props.sharpVersion }}</a>
            </p>
        </div>
    </div>
</template>
