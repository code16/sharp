<template>
    <div class="SharpLoading__overlay" :class="classes" v-show="visible">
        <Loading v-bind="$attrs" />
    </div>
</template>

<script>
    import Loading from './Loading.vue';

    export default {
        components: {
            Loading,
        },
        props: {
            visible: {
                type: Boolean,
                default: true,
            },
            absolute: Boolean,
            fade: Boolean,
        },
        computed: {
            classes() {
                return {
                    'SharpLoading__overlay--absolute': this.absolute,
                }
            },
        }
    }
</script>
