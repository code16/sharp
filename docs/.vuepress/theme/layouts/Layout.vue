<template>
    <ParentLayout :class="classes" />
</template>

<script>
    import ParentLayout from '@parent-theme/layouts/Layout.vue';

    export default {
        components: {
            ParentLayout,
        },
        data() {
            return {
                showLogo: false,
            }
        },
        computed: {
            classes() {
                return {
                    'show-logo': this.showLogo,
                }
            },
        },
        methods: {
            handleScroll() {
                this.showLogo = window.scrollY > 500;
            }
        },
        mounted() {
            window.addEventListener('scroll', this.handleScroll);
        },
        destroyed() {
            window.removeEventListener('scroll', this.handleScroll);
        },
    }
</script>

<style src="@theme/styles/index.styl" lang="stylus"></style>

