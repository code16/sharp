<template>
    <ParentLayout :class="classes" />
</template>

<script>
    import ParentLayout from '@vuepress/theme-default/lib/client/layouts/Layout.vue';

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
                const match = siteName.innerHTML.match(/(Sharp )(.+)/i);
                if(match) {
                    siteName.innerHTML = `${match[1]}<span class="version">${match[2]}</span>`
                }
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

<style src="../styles/vendors.scss" lang="scss"></style>
<style src="../styles/index.scss" lang="scss"></style>

