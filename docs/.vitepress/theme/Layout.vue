<script setup>
    import DefaultTheme from 'vitepress/theme'
    import CopyOrDownloadAsMarkdownButtons from 'vitepress-plugin-llms/vitepress-components/CopyOrDownloadAsMarkdownButtons.vue'
    import { useRoute } from "vitepress";
    import mediumZoom from "medium-zoom";
    import { nextTick, onMounted, watch } from "vue";

    const { Layout } = DefaultTheme

    const route = useRoute();
    const initZoom = () => {
        mediumZoom('.content img', { background: 'var(--vp-c-bg)' });
    };
    onMounted(() => {
        initZoom();
    });
    watch(
        () => route.path,
        () => nextTick(() => initZoom())
    );
</script>

<template>
    <Layout>
        <template #aside-outline-before>
            <CopyOrDownloadAsMarkdownButtons />
        </template>
    </Layout>
</template>
