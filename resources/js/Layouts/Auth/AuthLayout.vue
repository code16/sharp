<script setup lang="ts">
    import Logo from "@/components/Logo.vue";
    import SharpLogo from '../../../svg/logo.svg';
</script>

<template>
    <div id="sharp-app" class="p-4 py-16 min-h-screen flex justify-center items-center">
        <div class="absolute inset-0 -z-10">
            <!-- Should be the same as in Layout -->
            <div class="grid min-h-screen w-full md:grid-cols-[220px_1fr] lg:grid-cols-[280px_1fr]">
                <div class="flex flex-col p-4 h-14 items-start justify-center">
                    <template v-if="$page.props.logo">
                        <div class="text-accent-foreground">
                            <Logo />
                        </div>
                    </template>
                    <template v-else>
                        <SharpLogo class="h-4 text-accent-foreground" />
                    </template>
                </div>
            </div>
        </div>

        <div class="w-full max-w-sm">
            <slot />

            <p class="text-center text-xs text-muted-foreground opacity-50 mt-2">
                <span>powered by </span>
                <a href="https://sharp.code16.fr/docs/" target="_blank">Sharp {{ $page.props.sharpVersion }}</a>
            </p>
        </div>
    </div>
</template>
