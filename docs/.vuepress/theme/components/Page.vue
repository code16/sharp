<script setup>
    import { onMounted } from "vue";
    import ParentPage from '@vuepress/theme-default/components/Page.vue';

    onMounted(() => {
        let isCodeDescription = false;
        [...document.querySelector('h1').parentElement.children].forEach(element => {
            if(isCodeDescription && (isCodeDescription = element.matches(':not(h2, h3)'))) {
                element.classList.add('code-description');
            } else {
                isCodeDescription = element.matches('h3') && element.querySelector('code.full');
            }
        })
    });
</script>

<template>
    <ParentPage />
</template>
