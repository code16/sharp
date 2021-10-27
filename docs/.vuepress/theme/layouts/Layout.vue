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
            },
            init() {
                const siteName = this.$el.querySelector('.site-name');
                siteName.innerHTML = siteName.innerHTML.replace(/(\d)/, '<span class="version">$1</span>');
            },
        },
        mounted() {
            window.addEventListener('scroll', this.handleScroll);
            this.init();
        },
        destroyed() {
            window.removeEventListener('scroll', this.handleScroll);
        },
    }
</script>

<style src="@theme/styles/vendors.scss" lang="scss"></style>
<style src="@theme/styles/index.styl" lang="stylus"></style>

