<template>
    <RouterLink :to="url" style="color: inherit">
        <div class="row align-items-center gx-3">
            <div class="col">
                <Logo class="logo" width="120" />
            </div>
            <div class="col-auto" :class="{'visually-hidden': !versionTitle}">
                <span class="site-name">
                    <span class="visually-hidden">Sharp</span>
                    <template v-if="versionTitle">
                        <span class="version">{{ versionTitle }}</span>
                    </template>
                </span>
            </div>
        </div>
    </RouterLink>
</template>

<script setup>
    import { computed } from "vue";
    import { useRouteLocale, useSiteLocaleData } from "@vuepress/client";
    import Logo from '../../public/logo.svg';

    const siteLocale = useSiteLocaleData();
    const routeLocale = useRouteLocale();
    const versionTitle = computed(() =>
        siteLocale.value.title?.match(/(Sharp )(.+)/i)?.[2]
    );
    const url = computed(() => routeLocale.value);
</script>
